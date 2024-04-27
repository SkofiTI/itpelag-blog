<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;

class Authenticate implements MiddlewareInterface
{
    private bool $authenticated = true;

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if (! $this->authenticated) {
            return new Response('Authentication failed', 401);
        }

        return $handler->handle($request);
    }
}
