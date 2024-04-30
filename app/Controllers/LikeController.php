<?php

namespace App\Controllers;

use App\Entities\Like;
use App\Services\LikeService;
use Framework\Authentication\SessionAuthInterface;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;

class LikeController extends AbstractController
{
    public function __construct(
        private SessionAuthInterface $sessionAuth,
        private LikeService $likeService,
    ) {
    }

    public function store(): Response
    {
        if (! $this->sessionAuth->check()) {
            return new RedirectResponse('/login');
        }

        $user = $this->sessionAuth->getUser();

        $like = Like::create(
            $this->request->getPostData('post_id'),
            $user->getId(),
        );

        $this->likeService->store($like);

        return new RedirectResponse("/posts/{$like->getPostId()}");
    }

    public function delete(): Response
    {
        if (! $this->sessionAuth->check()) {
            return new RedirectResponse('/login');
        }

        $user = $this->sessionAuth->getUser();

        $like = Like::create(
            $this->request->getPostData('post_id'),
            $user->getId(),
        );

        $this->likeService->delete($like);

        return new RedirectResponse("/posts/{$like->getPostId()}");
    }
}
