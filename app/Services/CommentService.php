<?php

namespace App\Services;

use App\Entities\Comment;
use App\Exceptions\LimitQueryException;
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

        $lastComment = $this->getLastByUser($comment->getPostId(), $comment->getUserId());

        if ($lastComment) {
            $queryLimit = 3;
            $limitComments = $this->getWithLimit($comment->getPostId(), $queryLimit);
            $userCommentsCount = 0;

            foreach ($limitComments as $limitComment) {
                if ($limitComment['user_id'] === $comment->getUserId()) {
                    $userCommentsCount++;
                }
            }

            $lastCommentDate = new \DateTimeImmutable($lastComment['created_at']);
            $diffTime = $lastCommentDate->diff($comment->getCreatedAt());
            $minutes = $diffTime->days * 24 * 60 + $diffTime->h * 60 + $diffTime->i;

            if (($userCommentsCount === $queryLimit) || ($minutes < 1)) {
                throw new LimitQueryException('Достигнут лимит запросов!');
            }
        }

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

    public function getAll(int $postId, string $sortOrder = 'DESC'): array
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
            ->where('c.post_id = :post_id')
            ->setParameter('post_id', $postId)
            ->orderBy('c.created_at', $sortOrder)
            ->executeQuery();

        return $result->fetchAllAssociative();
    }

    public function getLastByUser(int $postId, int $userId): array|false
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select([
                'c.id',
                'c.content',
                'c.created_at',
            ])
            ->from('comments', 'c')
            ->join('c', 'users', 'u', 'u.id = c.user_id')
            ->where('user_id = :user_id')
            ->join('c', 'posts', 'p', 'p.id = c.post_id')
            ->where('c.post_id = :post_id')
            ->setParameters([
                'post_id' => $postId,
                'user_id' => $userId,
            ])
            ->setMaxResults(1)
            ->orderBy('created_at', 'DESC')
            ->executeQuery();

        return $result->fetchAssociative();
    }

    public function getWithLimit(int $postId, int $limit, string $sortOrder = 'DESC'): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select([
                'c.id',
                'c.content',
                'c.created_at',
                'c.user_id',
                'c.post_id',
            ])
            ->from('comments', 'c')
            ->where('post_id = :post_id')
            ->setParameter('post_id', $postId)
            ->setMaxResults($limit)
            ->orderBy('created_at', $sortOrder)
            ->executeQuery();

        return $result->fetchAllAssociative();
    }
}
