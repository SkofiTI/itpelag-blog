<?php

namespace App\Forms\User;

use App\Entities\User;
use App\Interfaces\FormInterface;
use App\Services\UserService;

class RegisterForm implements FormInterface
{
    private string $username;

    private string $password;

    private string $passwordConfirmation;

    public function __construct(
        private UserService $userService
    ) {
    }

    public function setFields(string $username, string $password, string $passwordConfirmation): void
    {
        $this->username = $username;
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    public function save(): User
    {
        $user = User::create(
            username: $this->username,
            password: password_hash($this->password, PASSWORD_DEFAULT),
        );

        $user = $this->userService->store($user);

        return $user;
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->username)) {
            $errors[] = 'Имя пользователя является обязательным полем';
        }

        if (empty($this->password) || strlen($this->password) < 8) {
            $errors[] = 'Минимальная длина пароля 8 символов';
        }

        if ($this->password !== $this->passwordConfirmation) {
            $errors[] = 'Пароли не совпадают';
        }

        return $errors;
    }

    public function hasValidationErrors(): bool
    {
        return ! empty($this->getValidationErrors());
    }
}
