<?php

namespace Framework\Interfaces\Console;

interface CommandInterface
{
    public function execute(array $parameters = []): int;
}
