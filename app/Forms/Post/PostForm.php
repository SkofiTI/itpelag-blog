<?php

namespace App\Forms\Post;

use App\Entities\Post;
use App\Interfaces\FormInterface;
use App\Services\PostService;
use Framework\Interfaces\Session\SessionInterface;

class PostForm implements FormInterface
{
    private string $title;

    private string $body;

    private int $userId;

    private ?int $id;

    private array $errors = [];

    public function __construct(
        private readonly PostService $postService,
    ) {
    }

    public function setFields(string $title, string $body, int $userId, ?int $id = null): void
    {
        $this->title = $title;
        $this->body = $body;
        $this->userId = $userId;
        $this->id = $id;
    }

    public function save(): Post
    {
        $post = Post::create(
            $this->title,
            $this->body,
            $this->userId,
            $this->id,
        );

        return $this->postService->store($post);
    }

    public function update(): Post
    {
        $post = Post::create(
            $this->title,
            $this->body,
            $this->userId,
            $this->id,
        );

        return $this->postService->update($post);
    }

    public function delete(): void
    {
        $post = Post::create(
            $this->title,
            $this->body,
            $this->userId,
            $this->id,
        );

        $this->postService->delete($post);
    }

    public function validate(): void
    {
        if (empty($this->title)) {
            $this->errors[] = 'Название поста является обязательным полем';
        }

        $titleLength = mb_strlen($this->title);
        if ($titleLength < 3 || $titleLength > 255) {
            $this->errors[] = 'Название поста не может быть короче 3-х и длиннее 255 символов';
        }

        if (empty($this->body)) {
            $this->errors[] = 'Тело поста является обязательным полем';
        }
    }

    public function hasValidationErrors(): bool
    {
        return ! empty($this->errors);
    }

    public function getValidationErrors(): array
    {
        return $this->errors;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}
