<?php

namespace App\Forms\Post;

use App\Entities\Post;
use App\Interfaces\FormInterface;
use App\Services\PostService;

class PostForm implements FormInterface
{
    private string $title;

    private string $body;

    private int $userId;

    private ?int $id;

    public function __construct(
        private PostService $postService
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

        $post = $this->postService->store($post);

        return $post;
    }

    public function update(): Post
    {
        $post = Post::create(
            $this->title,
            $this->body,
            $this->userId,
            $this->id,
        );

        $post = $this->postService->update($post);

        return $post;
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

    public function getValidationErrors(): array
    {
        $errors = [];

        if (empty($this->title)) {
            $errors[] = 'Название поста является обязательным полем';
        }

        $titleLength = mb_strlen($this->title);
        if ($titleLength < 3 || $titleLength > 255) {
            $errors[] = 'Название поста не может быть короче 3-х и длиннее 255 символов';
        }

        if (empty($this->body)) {
            $errors[] = 'Тело поста является обязательным полем';
        }

        return $errors;
    }

    public function hasValidationErrors(): bool
    {
        return ! empty($this->getValidationErrors());
    }
}
