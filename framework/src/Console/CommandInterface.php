<?php

namespace Framework\Console;

interface CommandInterface
{
    public function execute(array $parameters = []): int;
}
