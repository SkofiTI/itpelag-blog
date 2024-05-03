<?php

namespace App\Controllers;

use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\SessionAuthInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private SessionAuthInterface $sessionAuth
    ) {
    }

    public function index(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    public function login(): RedirectResponse
    {
        $isAuth = $this->sessionAuth->authenticate(
            username: $this->request->getPostData('username'),
            password: $this->request->getPostData('password'),
        );

        if (! $isAuth) {
            $this->request
                ->getSession()
                ->setFlash('error', 'Неверный логин или пароль');

            return new RedirectResponse('login');
        }

        $this->request
            ->getSession()
            ->setFlash('success', 'Авторизация прошла успешно!');

        return new RedirectResponse('/dashboard');
    }

    public function logout(): RedirectResponse
    {
        $this->sessionAuth->logout();

        return new RedirectResponse('/login');
    }
}
