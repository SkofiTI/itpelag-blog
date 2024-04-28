<?php

namespace App\Controllers;

use Framework\Controller\AbstractController;
use Framework\Http\Response;

class RegisterController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    public function register(): Response
    {
        return $this->render('auth/register.html.twig');
    }
}
