<?php

declare(strict_types=1);


namespace mihas2\XmlValidator;

class PunyCodeConverter implements PunyCodeConverterInterface
{
    private array $parsedUrl;

    public function convert(string $url): string
    {
        if (! \filter_var(FILTER_VALIDATE_URL)) {
            throw new \RuntimeException('Parameter url must be valid URL: ' . $url);
        }

        $this->parsedUrl = \parse_url($url);
        if (! isset($this->parsedUrl['host'])) {
            throw new \RuntimeException('Parameter url must be contain host: ' . $url);
        }

        if ($this->containsUtf8($this->parsedUrl['host'])) {
            $this->parsedUrl['host'] = \idn_to_ascii($this->parsedUrl['host']);
        }

        return $this->unparseUrl();
    }

    private function unparseUrl(): string
    {
        $scheme = isset($this->parsedUrl['scheme']) ? $this->parsedUrl['scheme'] . '://' : '';
        $host = $this->parsedUrl['host'] ?? '';
        $port = isset($this->parsedUrl['port']) ? ':' . $this->parsedUrl['port'] : '';
        $user = $this->parsedUrl['user'] ?? '';
        $pass = isset($this->parsedUrl['pass']) ? ':' . $this->parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = $this->parsedUrl['path'] ?? '';
        $query = isset($this->parsedUrl['query']) ? '?' . $this->parsedUrl['query'] : '';
        $fragment = isset($this->parsedUrl['fragment']) ? '#' . $this->parsedUrl['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    private function containsUtf8(string $string): bool
    {
        // This regular expression checks for 4-byte sequences typical of UTF-8
        return preg_match('/[\x{80}-\x{FF}]/', $string) !== false;
    }
}
