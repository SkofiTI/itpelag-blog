<?php

namespace Framework\Http;

use Framework\Http\Middleware\ExtractRouteInfo;
use Framework\Http\Middleware\RouterDispatch;
use Framework\Http\Middleware\StartSession;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private array $middlewares = [
        StartSession::class,
        ExtractRouteInfo::class,
        RouterDispatch::class,
    ];

    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function handle(Request $request): Response
    {
        if (empty($this->middlewares)) {
            return new Response('Server error', 500);
        }

        $middlewareClass = array_shift($this->middlewares);
        $middleware = $this->container->get($middlewareClass);

        $response = $middleware->process($request, $this);

        return $response;
    }

    public function injectMiddlewares(array $middlewares): void
    {
        array_splice($this->middlewares, 0, 0, $middlewares);
    }
}
