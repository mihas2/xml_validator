<?php

declare(strict_types=1);


namespace mihas2\XmlValidator;

class XmlValidator implements XmlValidatorInterface
{
    private int $maxDepth = 10000;
    private int $xmlFlags = \LIBXML_BIGLINES | \LIBXML_PARSEHUGE;
    private array $errors = [];

    public function __construct(
        private string $xmlFile,
        readonly bool $verifyPeer = false,
        readonly bool $verifyPeerName = false,
    )
    {
        try {
            $this->xmlFile = (new PunyCodeConverter())->convert($this->xmlFile);
        } catch (\RuntimeException) {
        }
    }

    public function validate(): bool
    {
        \stream_context_set_default(
            ['ssl' => ['verify_peer' => $this->verifyPeer, 'verify_peer_name' => $this->verifyPeerName]]
        );
        \libxml_use_internal_errors(true);

        $reader = new \XMLReader();
        try {
            $read = $reader->open($this->xmlFile, null, $this->xmlFlags);
        } catch (\Throwable $e) {
            $read = false;
        }

        if ($read) {
            $reader->setParserProperty(\XMLReader::VALIDATE, true);
        } else {
            $this->errors[] = $this->createError(0, 0, 'The file cannot be read: ' . $this->xmlFile);
        }

        $this->validateElementContent($reader);

        \libxml_use_internal_errors(false);

        return ! $this->hasErrors();
    }

    /**
     * @return XmlValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }


    private function validateElementContent(\XMLReader $reader, int $level = 0): void
    {
        if ($level > $this->maxDepth) {
            throw new \OutOfRangeException('The level of nesting is too high, check the xml document');
        }
        $reader->isValid();
        $this->catchXMLErrors();

        try {
            $reader->read();
            do {
                try {
                    if ($reader->nodeType === \XMLReader::ELEMENT) {
                        if ($reader->hasAttributes) {
                            $this->parseAttributes($reader);
                        }
                        if (! $reader->isEmptyElement && $level <= $this->maxDepth) {
                            $this->validateElementContent($reader, ++$level);
                        }
                    }
                } catch (\OutOfRangeException $e) {
                } catch (\Throwable $e) {
                    $this->catchXMLErrors();
                }

                $this->catchXMLErrors();
            } while ($reader->next());
        } catch (\Throwable $e) {
            $this->catchXMLErrors();
        }
    }

    private function catchXMLErrors(): void
    {
        foreach (\libxml_get_errors() as $error) {
            $this->errors[] = $this->createError($error->line, $error->column, $error->message);
        }
        \libxml_clear_errors();
    }

    private function createError(int $lineNumber, int $columnNumber, string $message): XmlValidationError
    {
        return new XmlValidationError(
            $lineNumber,
            $columnNumber,
            trim($message)
        );
    }

    private function parseAttributes(\XMLReader $reader): void
    {
        while ($reader->moveToNextAttribute()) {
        }
    }

    private function hasErrors(): bool
    {
        return \count($this->errors) > 0;
    }

    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    public function setMaxDepth(int $maxDepth): static
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }

    public function getXmlFlags(): int
    {
        return $this->xmlFlags;
    }

    public function setXmlFlags(int $xmlFlags): static
    {
        $this->xmlFlags = $xmlFlags;
        return $this;
    }
}
