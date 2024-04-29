<?php

namespace App\Controllers;

use App\Entities\Post;
use App\Services\PostService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Framework\Authentication\SessionAuthInterface;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;

class PostController extends AbstractController
{
    public function __construct(
        private PostService $postService,
        private SessionAuthInterface $sessionAuth
    ) {
    }

    public function index()
    {
        return $this->render('index.html.twig', [
            'posts' => $this->postService->getAll(),
        ]);
    }

    public function show(int $id): Response
    {
        return $this->render('show.html.twig', [
            'post' => $this->postService->findOrFail($id),
        ]);
    }

    public function create(): Response
    {
        return $this->render('create.html.twig');
    }

    public function store(): Response
    {
        $user = $this->sessionAuth->getUser();

        $post = Post::create(
            $this->request->getPostData('title'),
            $this->request->getPostData('body'),
            $user->getId(),
        );

        try {
            $post = $this->postService->store($post);
        } catch (UniqueConstraintViolationException $e) {
            $this->request
                ->getSession()
                ->setFlashArray([
                    'error' => 'Название поста должно быть уникальным!',
                    'title' => $post->getTitle(),
                    'body' => $post->getBody(),
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

        if ($post['user_id'] != $userId) {
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

        if ($post['user_id'] != $userId) {
            return new RedirectResponse('/');
        }

        $post = Post::create(
            $this->request->getPostData('title'),
            $this->request->getPostData('body'),
            $userId,
            $id,
        );

        try {
            $post = $this->postService->update($post);
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

    public function delete(int $id)
    {
        $post = $this->postService->findOrFail($id);
        $userId = $this->sessionAuth->getUser()->getId();

        if ($post['user_id'] != $userId) {
            return new RedirectResponse('/');
        }

        $post = Post::create(
            $post['title'],
            $post['body'],
            $userId,
            $id,
        );

        $this->postService->delete($post);

        $this->request
            ->getSession()
            ->setFlash('success', 'Пост успешно удалён!');

        return new RedirectResponse('/');
    }
}
