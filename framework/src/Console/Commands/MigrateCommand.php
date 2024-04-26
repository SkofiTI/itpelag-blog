<?php

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;

class MigrateCommand implements CommandInterface
{
    private string $name = 'migrate';

    public function execute(array $parameters = []): int
    {
        return 0;
    }
}