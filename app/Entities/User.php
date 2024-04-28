<?php

namespace App\Entities;

class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $username,
        private string $password,
        private ?\DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(
        string $name,
        string $username,
        string $password,
        ?int $id = null,
        ?\DateTimeImmutable $createdAt = null
    ): static {
        return new static($id, $name, $username, $password, $createdAt ?? new \DateTimeImmutable());
    }
}
