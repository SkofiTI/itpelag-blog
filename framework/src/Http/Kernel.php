<?php

namespace Framework\Http;

use Framework\Http\Exceptions\HttpException;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;
use Psr\Container\ContainerInterface;

class Kernel
{
    private string $appEnv = '';

    public function __construct(
        private readonly RequestHandlerInterface $requestHandler
    ) {
        $this->appEnv = getenv('APP_ENV') ?? 'production';
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

    public function terminate(Request $request): void
    {
        $request->getSession()?->clearFlash();
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
