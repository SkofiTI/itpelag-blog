<?php

namespace App\Services;

use App\Entities\User;
use Doctrine\DBAL\Connection;
use Framework\Http\Exceptions\NotFoundedException;
use Framework\Interfaces\Authentication\AuthUserInterface;
use Framework\Interfaces\Authentication\AuthUserServiceInterface;

class UserService implements AuthUserServiceInterface
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function store(User $user): User
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->insert('users')
            ->values([
                'username' => ':username',
                'password' => ':password',
                'created_at' => ':created_at',
            ])
            ->setParameters([
                'username' => $user->getLogin(),
                'password' => $user->getPassword(),
                'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ])
            ->executeQuery();

        $id = $this->connection->lastInsertId();

        $user->setId($id);

        return $user;
    }

    public function find(int $id): ?User
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select('*')
            ->from('users')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        $userData = $result->fetchAssociative();

        if (! $userData) {
            return null;
        }

        return User::create(
            id: $userData['id'],
            username: $userData['username'],
            password: $userData['password'],
            createdAt: new \DateTimeImmutable($userData['created_at']),
        );
    }

    public function findOrFail(int $id): User
    {
        $user = $this->find($id);

        if (is_null($user)) {
            throw new NotFoundedException("User $id not found");
        }

        return $user;
    }

    public function findByUsername(string $username): ?AuthUserInterface
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $result = $queryBuilder
            ->select('*')
            ->from('users')
            ->where('username = :username')
            ->setParameter('username', $username)
            ->executeQuery();

        $userData = $result->fetchAssociative();

        if (! $userData) {
            return null;
        }

        return User::create(
            id: $userData['id'],
            username: $userData['username'],
            password: $userData['password'],
            createdAt: new \DateTimeImmutable($userData['created_at']),
        );
    }
}
