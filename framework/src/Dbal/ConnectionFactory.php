<?php

namespace Framework\Dbal;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

readonly class ConnectionFactory
{
    public function __construct(
        private string $connectionUrl
    ) {
    }

    public function create(): Connection
    {
        return DriverManager::getConnection([
            'url' => $this->connectionUrl,
        ]);
    }
}
