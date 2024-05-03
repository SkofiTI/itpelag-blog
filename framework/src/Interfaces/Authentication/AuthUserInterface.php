<?php

namespace Framework\Interfaces\Authentication;

interface AuthUserInterface
{
    public function getId(): int;

    public function getUsername(): string;

    public function getPassword(): string;
}
