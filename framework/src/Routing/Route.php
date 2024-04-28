<?php

namespace Framework\Routing;

class Route
{
    public static function get(string $uri, array|callable $handler, array $middleware = []): array
    {
        return ['GET', $uri, [$handler, $middleware]];
    }

    public static function post(string $uri, array|callable $handler, array $middleware = []): array
    {
        return ['POST', $uri, [$handler, $middleware]];
    }
}
