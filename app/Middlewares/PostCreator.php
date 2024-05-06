<?php

namespace App\Middlewares;

use App\Services\PostService;
use Framework\Http\RedirectResponse;
use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Interfaces\Http\Middleware\MiddlewareInterface;
use Framework\Interfaces\Http\Middleware\RequestHandlerInterface;

readonly class PostCreator implements MiddlewareInterface
{
    public function __construct(
        private PostService $postService,
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if (! $this->postService->isCreator($request->getRouteArgs()['id'])) {
            return new RedirectResponse('/');
        }

        return $handler->handle($request);
    }
}
