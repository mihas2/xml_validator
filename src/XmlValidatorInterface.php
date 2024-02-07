<?php

namespace mihas2\XmlValidator;

interface XmlValidatorInterface
{
    public function validate(): bool;

    /**
     * @return XmlValidationError[]
     */
    public function getErrors(): array;

}
