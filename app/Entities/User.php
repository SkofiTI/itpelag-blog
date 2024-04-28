<?php

namespace App\Entities;

use Framework\Authentication\AuthUserInterface;

class User implements AuthUserInterface
{
    public function __construct(
        private ?int $id,
        private string $username,
        private string $password,
        private ?\DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(
        string $username,
        string $password,
        ?int $id = null,
        ?\DateTimeImmutable $createdAt = null
    ): static {
        return new static($id, $username, $password, $createdAt ?? new \DateTimeImmutable());
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
