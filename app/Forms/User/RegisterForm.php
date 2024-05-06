<?php

namespace App\Forms\User;

use App\Entities\User;
use App\Interfaces\FormInterface;
use App\Services\UserService;
use Framework\Http\Request;

class RegisterForm implements FormInterface
{
    private string $username;

    private string $password;

    private string $passwordConfirmation;

    private array $errors = [];

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

    public function validate(): void
    {
        if (empty($this->username)) {
            $this->errors[] = 'Имя пользователя является обязательным полем';
        }

        $usernameLength = mb_strlen($this->username);
        if ($usernameLength < 3 || $usernameLength > 255) {
            $this->errors[] = 'Имя пользователя не может быть короче 3-х и длиннее 255 символов';
        }

        if (empty($this->password) || strlen($this->password) < 8) {
            $this->errors[] = 'Минимальная длина пароля 8 символов';
        }

        if ($this->password !== $this->passwordConfirmation) {
            $this->errors[] = 'Пароли не совпадают';
        }
    }

    public function hasValidationErrors(): bool
    {
        return ! empty($this->errors);
    }

    public function getValidationErrors(): array
    {
        return $this->errors;
    }
}
