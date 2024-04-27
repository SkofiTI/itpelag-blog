<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;

interface MiddlewareInterface
{
    public function process(Request $request, RequestHandlerInterface $handler): Response;
}
