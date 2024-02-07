<?php

namespace mihas2\XmlValidator;

interface PunyCodeConverterInterface
{
    public function convert(string $url): string;
}
