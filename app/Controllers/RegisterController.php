<?php

namespace App\Controllers;

use App\Forms\User\RegisterForm;
use App\Services\UserService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;
use Framework\Interfaces\Authentication\SessionAuthInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private SessionAuthInterface $sessionAuth
    ) {
    }

    public function index(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    public function register(): Response
    {
        $form = new RegisterForm($this->userService);

        $form->setFields(
            username: $this->request->getPostData('username'),
            password: $this->request->getPostData('password'),
            passwordConfirmation: $this->request->getPostData('password_confirmation'),
        );

        if ($form->hasValidationErrors()) {
            foreach ($form->getValidationErrors() as $error) {
                $this->request
                    ->getSession()
                    ->setFlash('error', $error);
            }

            return new RedirectResponse('/register');
        }

        try {
            $user = $form->save();
        } catch (UniqueConstraintViolationException $e) {
            $this->request
                ->getSession()
                ->setFlash('error', 'Пользователь с таким именем уже существует');

            return new RedirectResponse('/register');
        }

        $this->request
            ->getSession()
            ->setFlash('success', "Пользователь {$user->getUsername()} успешно зарегистрирован!");

        $this->sessionAuth->login($user);

        return new RedirectResponse('/dashboard');
    }
}
