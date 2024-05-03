<?php

namespace Framework\Interfaces\Authentication;

interface AuthUserServiceInterface
{
    public function findByUsername(string $username): ?AuthUserInterface;

    public function findOrFail(int $id): AuthUserInterface;
}
