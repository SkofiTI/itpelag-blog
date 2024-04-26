<?php

namespace Framework\Http;

use Doctrine\DBAL\Connection;
use Framework\Http\Exceptions\HttpException;
use Framework\Routing\RouterInterface;
use Psr\Container\ContainerInterface;

class Kernel
{
    private string $appEnv = '';

    public function __construct(
        private RouterInterface $router,
        private ContainerInterface $container,
    ){
        $this->appEnv = $this->container->get('APP_ENV');
    }

    public function handle(Request $request): Response
    {
        try {
            [$routeHandler, $vars] = $this->router->dispatch($request, $this->container);
            
            $response = call_user_func_array($routeHandler, $vars);
        } catch (\Exception $e) {
            $response = $this->createExceptionResponse($e);
        }
        
        return $response;
    }

    private function createExceptionResponse(\Exception $e): Response
    {
        if (in_array($this->appEnv, ['local', 'testing'])) {
            throw $e;
        }

        if ($e instanceof HttpException) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }

        return new Response('Server error', 500);
    }
}