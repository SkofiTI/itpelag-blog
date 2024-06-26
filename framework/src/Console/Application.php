<?php

namespace Framework\Console;

use Framework\Console\Exceptions\ConsoleException;
use Framework\Interfaces\Console\CommandInterface;
use Psr\Container\ContainerInterface;

readonly class Application
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function run(): int
    {
        $argv = $_SERVER['argv'];
        $args = array_slice($argv, 2);
        $options = $this->parseOptions($args);

        $commandName = $argv[1] ?? null;

        if (is_null($commandName)) {
            throw new ConsoleException('Invalid console command');
        }

        /** @var CommandInterface $command */
        $command = $this->container->get("console:$commandName");

        return $command->execute($options);
    }

    private function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            if (str_starts_with($arg, '--')) {
                $option = explode('=', substr($arg, 2));
                $options[$option[0]] = $option[1] ?? true;
            }
        }

        return $options;
    }
}
