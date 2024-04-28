<?php

namespace Framework\Authentication;

interface AuthUserInterface
{
    public function getId(): int;

    public function getUsername(): string;

    public function getPassword(): string;
}
