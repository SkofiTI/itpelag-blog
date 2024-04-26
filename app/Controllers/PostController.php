<?php

namespace App\Controllers;

use App\Entities\Post;
use App\Services\PostService;
use Framework\Controller\AbstractController;
use Framework\Http\Response;

class PostController extends AbstractController
{
    public function __construct(
        private PostService $postService
    ){}

    public function index()
    {
        return $this->render('index.html.twig');
    }

    public function show(int $id): Response
    {
        return $this->render('show.html.twig', [
            'postId' => $id
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

        return $this->render('create.html.twig');
    }

    public function login(): Response
    {
        return $this->render('login.html.twig');
    }

    public function registration(): Response
    {
        return $this->render('registration.html.twig');
    }
}