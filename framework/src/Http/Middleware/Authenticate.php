<?php

namespace Framework\Http\Middleware;

use Framework\Http\RedirectResponse;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\SessionAuthInterface;
use Framework\Interfaces\Http\Middleware\MiddlewareInterface;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;

readonly class Authenticate implements MiddlewareInterface
{
    public function __construct(
        private SessionAuthInterface $sessionAuth,
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if (! $this->sessionAuth->check()) {
            return new RedirectResponse('/login');
        }

        return $handler->handle($request);
    }
}
