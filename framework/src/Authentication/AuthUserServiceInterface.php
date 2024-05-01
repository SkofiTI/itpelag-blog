<?php

namespace Framework\Authentication;

interface AuthUserServiceInterface
{
    public function findByUsername(string $username): ?AuthUserInterface;

    public function find(int $id): ?AuthUserInterface;
}
