<?php

namespace Framework\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

class ConnectionFactory
{
    public function __construct(
        private readonly string $connectionUrl
    ){}

    public function create(): Connection
    {
        $connection = DriverManager::getConnection([
            'url' => $this->connectionUrl
        ]);

        $connection->setAutoCommit(false);

        return $connection;
    }
}