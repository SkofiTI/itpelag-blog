<?php

namespace App\Controllers;

use App\Entities\Comment;
use App\Exceptions\LimitQueryException;
use App\Services\CommentService;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\SessionAuthInterface;

class CommentController extends AbstractController
{
    public function __construct(
        private readonly CommentService $commentService,
        private readonly SessionAuthInterface $sessionAuth
    ) {
    }

    public function store(): Response
    {
        $user = $this->sessionAuth->getUser();

        $comment = Comment::create(
            $this->request->getPostData('comment'),
            $user->getId(),
            $this->request->getPostData('post_id'),
        );

        try {
            $comment = $this->commentService->store($comment);
        } catch (LimitQueryException $e) {
            $this->request
                ->getSession()
                ->setFlash('error', 'Нельзя создавать комментарии чаще чем раз в минуту и более чем 3 подряд!');

            return new RedirectResponse("/posts/{$comment->getPostId()}");
        }

        $this->request
            ->getSession()
            ->setFlash('success', 'Комментарий успешно создан!');

        return new RedirectResponse("/posts/{$comment->getPostId()}");
    }
}
