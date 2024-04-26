<?php

namespace App\Services;

use App\Entities\Post;
use Doctrine\DBAL\Connection;

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
}