<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Interfaces\Http\Middleware\MiddlewareInterface;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;
use Framework\Interfaces\Session\SessionInterface;

readonly class StartSession implements MiddlewareInterface
{
    public function __construct(
        private SessionInterface $session
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->session->start();

        $request->setSession($this->session);

        return $handler->handle($request);
    }
}
