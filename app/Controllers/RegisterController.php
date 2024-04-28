<?php

namespace App\Controllers;

use App\Forms\User\RegisterForm;
use Framework\Controller\AbstractController;
use Framework\Http\RedirectResponse;
use Framework\Http\Response;

class RegisterController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    public function register(): Response
    {
        $form = new RegisterForm();

        $form->setFields(
            name: $this->request->getPostData('name'),
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

        return $this->render('auth/register.html.twig');
    }
}
