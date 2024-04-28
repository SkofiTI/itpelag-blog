<?php

namespace App\Controllers;

use App\Services\PostService;
use Framework\Authentication\SessionAuthInterface;
use Framework\Controller\AbstractController;

class DashboardController extends AbstractController
{
    public function __construct(
        private PostService $postService,
        private SessionAuthInterface $sessionAuth
    ) {
    }

    public function index()
    {
        return $this->render('dashboard.html.twig', [
            'posts' => $this->postService->getAllByUser($this->sessionAuth->getUser()->getId()),
        ]);
    }
}
