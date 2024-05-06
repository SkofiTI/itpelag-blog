<?php

namespace App\Services;

use App\Entities\Like;
use Doctrine\DBAL\Connection;
use Framework\Interfaces\Authentication\SessionAuthInterface;

readonly class LikeService
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function store(Like $like): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->insert('likes')
            ->values([
                'user_id' => ':user_id',
                'post_id' => ':post_id',
            ])
            ->setParameters([
                'user_id' => $like->getUserId(),
                'post_id' => $like->getPostId(),
            ])
            ->executeQuery();
    }

    public function delete(Like $like): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete('likes')
            ->where('user_id = :user_id')
            ->andWhere('post_id = :post_id')
            ->setParameters([
                'user_id' => $like->getUserId(),
                'post_id' => $like->getPostId(),
            ])
            ->executeQuery();
    }

    public function getCountByPost(int $postId): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder
            ->select('*')
            ->from('likes', 'l')
            ->where('post_id = :post_id')
            ->setParameter('post_id', $postId)
            ->executeQuery()
            ->rowCount();
    }

    public function getAllByPost(int $postId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return $queryBuilder
            ->select('*')
            ->from('likes', 'l')
            ->join('l', 'posts', 'p', 'p.id = l.user_id')
            ->where('l.post_id = :post_id')
            ->setParameter('post_id', $postId)
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function hasLike(int $postId, int $userId): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        return (bool) $queryBuilder
            ->select('*')
            ->from('likes', 'l')
            ->where('user_id = :user_id')
            ->andWhere('post_id = :post_id')
            ->setParameters([
                'user_id' => $userId,
                'post_id' => $postId,
            ])
            ->executeQuery()
            ->fetchOne();
    }
}
