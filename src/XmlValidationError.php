<?php

declare(strict_types=1);


namespace mihas2\XmlValidator;

class XmlValidationError
{
    public function __construct(
        public readonly int $lineNumber,
        public readonly int $columnNumber,
        public readonly string $message
    )
    {
    }
}
