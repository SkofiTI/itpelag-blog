<?php

namespace Framework\Http\Middleware;

use Framework\Http\Request;
use Framework\Http\Response;
use Psr\Container\ContainerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private array $middlewares = [
        StartSession::class,
        Authenticate::class,
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
}
