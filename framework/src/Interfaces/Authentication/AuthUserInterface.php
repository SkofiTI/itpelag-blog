<?php

namespace Framework\Interfaces\Authentication;

interface AuthUserInterface
{
    public function getId(): int;

    public function getLogin(): string;

    public function getPassword(): string;
}
