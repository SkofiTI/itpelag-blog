<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Routing\RouterInterface;

class RouterDispatch implements MiddlewareInterface
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        [$routeHandler, $vars] = $this->router->dispatch($request);

        $response = call_user_func_array($routeHandler, $vars);

        return $response;
    }
}
