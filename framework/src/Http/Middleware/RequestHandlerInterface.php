<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;

interface RequestHandlerInterface
{
    public function handle(Request $request): Response;

    public function injectMiddlewares(array $middlewares): void;
}
