<?php

namespace App\Services;

use App\Entities\Comment;
use Doctrine\DBAL\Connection;
use Framework\Authentication\SessionAuthInterface;

class CommentService
{
    public function __construct(
        private Connection $connection,
        private SessionAuthInterface $sessionAuth
    ) {
    }

    public function store(Comment $comment): Comment
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->insert('comments')
            ->values([
                'post_id' => ':post_id',
                'user_id' => ':user_id',
                'content' => ':content',
                'created_at' => ':created_at',
            ])
            ->setParameters([
                'user_id' => $comment->getUserId(),
                'post_id' => $comment->getPostId(),
                'content' => $comment->getContent(),
                'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
            ->executeQuery();

        $id = $this->connection->lastInsertId();

        $comment->setId($id);

        return $comment;
    }

    public function getAll(int $postId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select([
                'c.id',
                'c.content',
                'c.created_at',
                'u.username',
            ])
            ->from('comments', 'c')
            ->join('c', 'users', 'u', 'u.id = c.user_id')
            ->join('c', 'posts', 'p', 'p.id = c.post_id')
            ->where('c.post_id = :post_id')
            ->setParameter('post_id', $postId)
            ->orderBy('c.created_at', 'ASC')
            ->executeQuery();

        return $result->fetchAllAssociative();
    }
}
