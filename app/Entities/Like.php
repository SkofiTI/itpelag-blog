<?php

namespace App\Entities;

class Like
{
    public function __construct(
        private int $postId,
        private int $userId,
    ) {
    }

    public static function create(
        int $postId,
        int $userId,
    ): static {
        return new static($postId, $userId);
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
