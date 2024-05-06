<?php

namespace App\Controllers;

use App\Services\PostService;
use Framework\Controller\AbstractController;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\SessionAuthInterface;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly PostService $postService,
        private readonly SessionAuthInterface $sessionAuth
    ) {
    }

    public function index(): Response
    {
        return $this->render('dashboard.html.twig', [
            'posts' => $this->postService->getAllByUser($this->sessionAuth->getUser()->getId()),
        ]);
    }
}
