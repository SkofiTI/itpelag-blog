<?php

namespace App\Controllers;

use Framework\Controller\AbstractController;
use Framework\Http\Response;

class LoginController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    public function login(): Response
    {
        return $this->render('auth/login.html.twig');
    }
}
