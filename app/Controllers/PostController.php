<?php

namespace App\Controllers;

use App\Entities\Post;
use App\Services\PostService;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;

class PostController extends AbstractController
{
    public function __construct(
        private PostService $postService,
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
        $post = Post::create(
            $this->request->getPostData('title'),
            $this->request->getPostData('body'),
        );

        $post = $this->postService->save($post);

        $this->request
            ->getSession()
            ->setFlash('success', 'Пост успешно создан!');

        return new RedirectResponse("/posts/{$post->getId()}");
    }
}
