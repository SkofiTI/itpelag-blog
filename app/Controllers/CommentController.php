<?php

namespace App\Controllers;

use App\Entities\Comment;
use App\Services\CommentService;
use Framework\Authentication\SessionAuthInterface;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;

class CommentController extends AbstractController
{
    public function __construct(
        private CommentService $commentService,
        private SessionAuthInterface $sessionAuth
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

        $comment = $this->commentService->store($comment);

        $this->request
            ->getSession()
            ->setFlash('success', 'Комментарий успешно создан!');

        return new RedirectResponse("/posts/{$comment->getPostId()}");
    }
}
