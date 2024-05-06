<?php

namespace App\Services;

use App\Entities\Post;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Framework\Http\Exceptions\NotFoundedException;
use Framework\Interfaces\Authentication\SessionAuthInterface;

readonly class PostService
{
    public function __construct(
        private Connection $connection,
        private SessionAuthInterface $sessionAuth
    ) {
    }

    /**
     * @throws Exception
     */
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

    public function find(int $id): ?Post
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select([
                'p.id',
                'p.user_id',
                'p.title',
                'p.body',
                'p.created_at',
            ])
            ->from('posts', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $postData = $result->fetchAssociative();

        if (! $postData) {
            return null;
        }

        return Post::create(
            title: $postData['title'],
            body: $postData['body'],
            userId: $postData['user_id'],
            id: $postData['id'],
            createdAt: new \DateTimeImmutable($postData['created_at']),
        );
    }

    public function findOrFail(int $id): Post
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

        return $queryBuilder
            ->select([
                'p.id',
                'p.title',
                'p.body',
                'p.created_at',
                'u.username',
            ])
            ->from('posts', 'p')
            ->join('p', 'users', 'u', 'u.id = p.user_id')
            ->orderBy('p.created_at', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getAllPaginate(int $page, int $limit): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder
            ->select([
                'p.id',
                'p.title',
                'p.body',
                'p.created_at',
                'u.username',
            ])
            ->from('posts', 'p')
            ->join('p', 'users', 'u', 'u.id = p.user_id')
            ->orderBy('p.created_at', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function getAllByUser(int $userId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder
            ->select([
                'p.id',
                'p.title',
                'p.body',
                'p.created_at',
            ])
            ->from('posts', 'p')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $userId)
            ->orderBy('p.created_at', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function isCreator(int $id): bool
    {
        try {
            $post = $this->findOrFail($id);
        } catch (NotFoundedException $e) {
            return false;
        }

        $userId = $this->sessionAuth->getUser()->getId();

        return $post->getUserId() === $userId;
    }
}
