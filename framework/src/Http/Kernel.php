<?php

namespace Framework\Http;

use Framework\Http\Exceptions\HttpException;
use Framework\Http\Middleware\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class Kernel
{
    private string $appEnv = '';

    public function __construct(
        private ContainerInterface $container,
        private RequestHandlerInterface $requestHandler
    ) {
        $this->appEnv = $this->container->get('APP_ENV');
    }

    public function handle(Request $request): Response
    {
        try {
            $response = $this->requestHandler->handle($request);
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
