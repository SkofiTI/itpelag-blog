<?php

namespace App\Controllers;

use App\Entities\Like;
use App\Services\LikeService;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\AuthUserInterface;
use Framework\Interfaces\Authentication\SessionAuthInterface;

class LikeController extends AbstractController
{
    private AuthUserInterface $user;

    public function __construct(
        private readonly SessionAuthInterface $sessionAuth,
        private readonly LikeService $likeService,
    ) {
        $this->user = $this->sessionAuth->getUser();
    }

    public function store(): Response
    {
        $like = Like::create(
            $this->request->getPostData('post_id'),
            $this->user->getId(),
        );

        $this->likeService->store($like);

        return new RedirectResponse("/posts/{$like->getPostId()}");
    }

    public function delete(): Response
    {
        $like = Like::create(
            $this->request->getPostData('post_id'),
            $this->user->getId(),
        );

        $this->likeService->delete($like);

        return new RedirectResponse("/posts/{$like->getPostId()}");
    }
}
