<?php

namespace Framework\Http\Middleware;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Framework\Http\Exceptions\MethodNotAllowedException;
use Framework\Http\Exceptions\RouteNotFoundException;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Interfaces\Http\Middleware\MiddlewareInterface;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;

use function FastRoute\simpleDispatcher;

readonly class ExtractRouteInfo implements MiddlewareInterface
{
    public function __construct(
        private array $routes
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $collector) {
            foreach ($this->routes as $route) {
                $collector->addRoute(...$route);
            }
        });

        $routeInfo = $dispatcher->dispatch(
            $request->getMethod(),
            $request->getPath(),
        );

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $request->setRouteHandler($routeInfo[1][0]);
                $request->setRouteArgs($routeInfo[2]);
                $handler->injectMiddlewares($routeInfo[1][1]);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = implode(', ', $routeInfo[1]);
                $e = new MethodNotAllowedException("Supported HTTP methods: $allowedMethods");
                $e->setStatusCode(405);
                throw $e;
            default:
                $e = new RouteNotFoundException('Route not found');
                $e->setStatusCode(404);
                throw $e;
        }

        return $handler->handle($request);
    }
}
