<?php

namespace App\Controllers;

use Framework\Controller\AbstractController;
use Framework\Http\Response;

class PostController extends AbstractController
{
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

    public function login(): Response
    {
        return $this->render('login.html.twig');
    }

    public function registration(): Response
    {
        return $this->render('registration.html.twig');
    }
}