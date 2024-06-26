<?php

namespace Framework\Interfaces\Authentication;

interface SessionAuthInterface
{
    public function authenticate(string $username, string $password): bool;

    public function login(AuthUserInterface $user): void;

    public function logout();

    public function getUser(): AuthUserInterface;

    public function check(): bool;
}
