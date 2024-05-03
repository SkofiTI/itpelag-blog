<?php

namespace Framework\Http\Middleware;

use Framework\Http\RedirectResponse;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\SessionAuthInterface;
use Framework\Interfaces\Http\Middleware\MiddlewareInterface;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;

class Guest implements MiddlewareInterface
{
    public function __construct(
        private SessionAuthInterface $sessionAuth,
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if ($this->sessionAuth->check()) {
            return new RedirectResponse('/dashboard');
        }

        return $handler->handle($request);
    }
}
