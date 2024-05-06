<?php

namespace App\Controllers;

use App\Forms\Post\PostForm;
use App\Services\CommentService;
use App\Services\LikeService;
use App\Services\PostService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\SessionAuthInterface;

class PostController extends AbstractController
{
    public function __construct(
        private readonly PostService $postService,
        private readonly CommentService $commentService,
        private readonly LikeService $likeService,
        private readonly SessionAuthInterface $sessionAuth,
        private readonly PostForm $form,
    ) {
    }

    public function index(): Response
    {
        $limit = 10;
        $firstPage = 1;
        $totalPages = ceil(count($this->postService->getAll()) / $limit);

        $page = $this->request->getParameters('page', $firstPage);

        if ($page < $firstPage) {
            $page = $firstPage;
        }

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        return $this->render('index.html.twig', [
            'posts' => $this->postService->getAllPaginate($page, $limit),
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }

    public function show(int $id): Response
    {
        $userId = $this->sessionAuth->check() ? $this->sessionAuth->getUser()->getId() : null;
        $userHasLike = $userId && $this->likeService->hasLike($id, $userId);

        return $this->render('show.html.twig', [
            'post' => $this->postService->findOrFail($id),
            'comments' => $this->commentService->getAll($id, 'ASC'),
            'likesCount' => $this->likeService->getCountByPost($id),
            'userHasLike' => $userHasLike,
        ]);
    }

    public function create(): Response
    {
        return $this->render('create.html.twig');
    }

    public function store(): Response
    {
        $user = $this->sessionAuth->getUser();

        $this->form->setFields(
            title: $this->request->getPostData('title'),
            body: $this->request->getPostData('body'),
            userId: $user->getId(),
        );
        
        $this->form->validate();

        if ($this->form->hasValidationErrors()) {
            $this->request
                ->getSession()
                ->setFlash('error', $this->form->getValidationErrors());

            return new RedirectResponse('/posts/create');
        }

        try {
            $post = $this->form->save();
        } catch (UniqueConstraintViolationException $e) {
            $this->request
                ->getSession()
                ->setFlashArray([
                    'error' => 'Название поста должно быть уникальным!',
                    'title' => $this->form->getTitle(),
                    'body' => $this->form->getBody(),
                ]);

            return new RedirectResponse('/posts/create');
        }

        $this->request
            ->getSession()
            ->setFlash('success', 'Пост успешно создан!');

        return new RedirectResponse("/posts/{$post->getId()}");
    }

    public function edit(int $id): Response
    {
        return $this->render('edit.html.twig', [
            'post' => $this->postService->findOrFail($id),
        ]);
    }

    public function update(int $id): Response
    {
        $this->form->setFields(
            title: $this->request->getPostData('title'),
            body: $this->request->getPostData('body'),
            userId: $this->sessionAuth->getUser()->getId(),
            id: $id
        );

        $this->form->validate();

        if ($this->form->hasValidationErrors()) {
            $this->request
                ->getSession()
                ->setFlash('error', $this->form->getValidationErrors());

            return new RedirectResponse("/posts/$id/edit");
        }

        try {
            $post = $this->form->update();
        } catch (UniqueConstraintViolationException $e) {
            $this->request
                ->getSession()
                ->setFlash('error', 'Название поста должно быть уникальным!');

            return new RedirectResponse("/posts/$id/edit");
        }

        $this->request
            ->getSession()
            ->setFlash('success', 'Пост успешно изменён!');

        return new RedirectResponse("/posts/{$post->getId()}");
    }

    public function delete(int $id): Response
    {
        $post = $this->postService->findOrFail($id);
        $userId = $this->sessionAuth->getUser()->getId();

        $this->form->setFields(
            $post->getTitle(),
            $post->getBody(),
            userId: $userId,
            id: $id,
        );

        $this->form->delete();

        $this->request
            ->getSession()
            ->setFlash('success', 'Пост успешно удалён!');

        return new RedirectResponse('/');
    }
}
