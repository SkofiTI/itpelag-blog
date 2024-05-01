<?php

namespace Framework\Authentication;

interface AuthUserServiceInterface
{
    public function findByUsername(string $username): ?AuthUserInterface;

    public function findOrFail(int $id): AuthUserInterface;
}
