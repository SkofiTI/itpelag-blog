<?php

namespace Framework\Http;

class Response
{
    public function __construct(
        private mixed $content,
        private int $statusCode = 200,
        private array $headers = [],
    ){
        http_response_code($statusCode);
    }

    public function send()
    {
        echo $this->content;
    }
}