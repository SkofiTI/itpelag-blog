<?php

namespace App\Services;

use App\Entities\Post;
use Doctrine\DBAL\Connection;
use Framework\Http\Exceptions\NotFoundedException;

class PostService
{
    public function __construct(
        private Connection $connection
    ){}

    public function save(Post $post): Post
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->insert('posts')
            ->values([
                'title' => ':title',
                'body' => ':body',
                'created_at' => ':created_at',
            ])
            ->setParameters([
                'title' => $post->getTitle(),
                'body' => $post->getBody(),
                'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
            ->executeQuery();
        
        $id = $this->connection->lastInsertId();

        $post->setId($id);
        
        return $post;
    }

    public function find(int $id): ?Post
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select('*')
            ->from('posts')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
        
        $postData = $result->fetchAssociative();

        if (!$postData) {
            return null;
        }
        
        return Post::create(
            title: $postData['title'],
            body: $postData['body'],
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

        $result = $queryBuilder
            ->select('*')
            ->from('posts')
            ->executeQuery();
        
        return $result->fetchAllAssociative();
    }
}