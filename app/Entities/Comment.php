<?php

namespace App\Entities;

class Comment
{
    public function __construct(
        private ?int $id,
        private int $userId,
        private int $postId,
        private string $content,
        private ?\DateTimeImmutable $createdAt,
    ) {
    }

    public static function create(
        string $content,
        int $userId,
        int $postId,
        ?int $id = null,
        ?\DateTimeImmutable $createdAt = null
    ): static {
        return new static($id, $userId, $postId, $content, $createdAt ?? new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
