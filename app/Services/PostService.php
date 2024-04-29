<?php

namespace App\Services;

use App\Entities\Post;
use Doctrine\DBAL\Connection;
use Framework\Authentication\SessionAuthInterface;
use Framework\Http\Exceptions\NotFoundedException;

class PostService
{
    public function __construct(
        private Connection $connection,
        private SessionAuthInterface $sessionAuth
    ) {
    }

    public function store(Post $post): Post
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->insert('posts')
            ->values([
                'user_id' => ':user_id',
                'title' => ':title',
                'body' => ':body',
                'created_at' => ':created_at',
            ])
            ->setParameters([
                'user_id' => $this->sessionAuth->getUser()->getId(),
                'title' => $post->getTitle(),
                'body' => $post->getBody(),
                'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
            ->executeQuery();

        $id = $this->connection->lastInsertId();

        $post->setId($id);

        return $post;
    }

    public function update(Post $post): Post
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->update('posts')
            ->set('title', ':title')
            ->set('body', ':body')
            ->set('created_at', ':created_at')
            ->setParameters([
                'title' => $post->getTitle(),
                'body' => $post->getBody(),
                'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
            ->where('id = :id')
            ->setParameter('id', $post->getId())
            ->executeQuery();

        return $post;
    }

    public function delete(Post $post): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete('posts')
            ->where('id = :id')
            ->setParameter('id', $post->getId())
            ->executeQuery();
    }

    public function find(int $id): ?array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select([
                'p.id',
                'p.user_id',
                'p.title',
                'p.body',
                'p.created_at',
                'u.username',
            ])
            ->from('posts', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->join('p', 'users', 'u', 'u.id = p.user_id')
            ->executeQuery();

        $postData = $result->fetchAssociative();

        if (! $postData) {
            return null;
        }

        return $postData;
    }

    public function findOrFail(int $id): array
    {
        $post = $this->find($id);

        if (is_null($post)) {
            throw new NotFoundedException("Post $id not found");
        }

        return $post;
    }

    public function getAll(): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select([
                'p.id',
                'p.title',
                'p.body',
                'p.created_at',
                'u.username',
            ])
            ->from('posts', 'p')
            ->join('p', 'users', 'u', 'u.id = p.user_id')
            ->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getAllByUser(int $userId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select([
                'p.id',
                'p.title',
                'p.body',
                'p.created_at',
            ])
            ->from('posts', 'p')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $userId)
            ->executeQuery();

        return $result->fetchAllAssociative();
    }
}
