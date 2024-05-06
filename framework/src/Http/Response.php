<?php

namespace Framework\Http;

class Response
{
    public function __construct(
        private string $content = '',
        private int $statusCode = 200,
        private array $headers = [],
    ) {
        http_response_code($statusCode);
    }

    public function send(): void
    {
        echo $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getHeader(string $key)
    {
        return $this->headers[$key];
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
