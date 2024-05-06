<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Interfaces\Http\Middleware\MiddlewareInterface;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;
use Framework\Interfaces\Routing\RouterInterface;

readonly class RouterDispatch implements MiddlewareInterface
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        [$routeHandler, $vars] = $this->router->dispatch($request);

        return call_user_func_array($routeHandler, $vars);
    }
}
