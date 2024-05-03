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
        private PostService $postService,
        private CommentService $commentService,
        private LikeService $likeService,
        private SessionAuthInterface $sessionAuth
    ) {
    }

    public function index()
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
        $userHasLike = $userId ? $this->likeService->hasLike($id, $userId) : false;

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

        $form = new PostForm($this->postService);

        $form->setFields(
            title: $this->request->getPostData('title'),
            body: $this->request->getPostData('body'),
            userId: $user->getId(),
        );

        $validationErrors = $form->getValidationErrors();

        if (! empty($validationErrors)) {
            foreach ($validationErrors as $error) {
                $this->request
                    ->getSession()
                    ->setFlash('error', $error);
            }

            return new RedirectResponse('/posts/create');
        }

        try {
            $post = $form->save();
        } catch (UniqueConstraintViolationException $e) {
            $this->request
                ->getSession()
                ->setFlashArray([
                    'error' => 'Название поста должно быть уникальным!',
                    'title' => $form->getTitle(),
                    'body' => $form->getBody(),
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
        $post = $this->postService->findOrFail($id);
        $userId = $this->sessionAuth->getUser()->getId();

        if ($post->getUserId() != $userId) {
            return new RedirectResponse('/');
        }

        return $this->render('edit.html.twig', [
            'post' => $post,
        ]);
    }

    public function update(int $id): Response
    {
        $post = $this->postService->findOrFail($id);
        $userId = $this->sessionAuth->getUser()->getId();

        if ($post->getUserId() != $userId) {
            return new RedirectResponse('/');
        }

        $form = new PostForm($this->postService);

        $form->setFields(
            title: $this->request->getPostData('title'),
            body: $this->request->getPostData('body'),
            userId: $userId,
            id: $id
        );

        $validationErrors = $form->getValidationErrors();

        if (! empty($validationErrors)) {
            foreach ($validationErrors as $error) {
                $this->request
                    ->getSession()
                    ->setFlash('error', $error);
            }

            return new RedirectResponse("/posts/{$post->getId()}/edit");
        }

        try {
            $post = $form->update();
        } catch (UniqueConstraintViolationException $e) {
            $this->request
                ->getSession()
                ->setFlash('error', 'Название поста должно быть уникальным!');

            return new RedirectResponse("/posts/{$post->getId()}/edit");
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

        if ($post->getUserId() != $userId) {
            return new RedirectResponse('/');
        }

        $form = new PostForm($this->postService);

        $form->setFields(
            $post->getTitle(),
            $post->getBody(),
            userId: $userId,
            id: $id,
        );

        $form->delete();

        $this->request
            ->getSession()
            ->setFlash('success', 'Пост успешно удалён!');

        return new RedirectResponse('/');
    }
}
