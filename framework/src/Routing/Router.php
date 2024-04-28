<?php

namespace Framework\Routing;

use Framework\Controller\AbstractController;
use Framework\Http\Request;
use Psr\Container\ContainerInterface;

class Router implements RouterInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    public function dispatch(Request $request): array
    {
        $handler = $request->getRouteHandler();
        $vars = $request->getRouteArgs();

        if (is_array($handler)) {
            [$controllerId, $method] = $handler;
            $controller = $this->container->get($controllerId);

            if (is_subclass_of($controller, AbstractController::class)) {
                $controller->setRequest($request);
            }

            $handler = [$controller, $method];
        }

        return [$handler, $vars];
    }
}
