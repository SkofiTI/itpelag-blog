<?php

namespace Framework\Authentication;

interface AuthUserServiceInterface
{
    public function findByUsername(string $username): ?AuthUserInterface;
}
