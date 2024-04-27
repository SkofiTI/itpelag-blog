<?php

namespace Framework\Routing;

use Framework\Http\Request;
use Psr\Container\ContainerInterface;

interface RouterInterface
{
    public function dispatch(Request $request, ContainerInterface $container);

    public function registerRoutes(array $routes): void;
}
