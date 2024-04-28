<?php

namespace App\Forms\User;

class RegisterForm
{
    private string $name;

    private string $username;

    private string $password;

    private string $passwordConfirmation;

    public function setFields(string $name, string $username, string $password, string $passwordConfirmation): void
    {
        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
        $this->passwordConfirmation = $passwordConfirmation;
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Имя (name) является обязательным полем';
        }

        if (empty($this->username)) {
            $errors[] = 'Имя пользователя (username) является обязательным полем';
        }

        if (empty($this->password) || strlen($this->password) < 8) {
            $errors[] = 'Минимальная длинна пароля 8 символов';
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
