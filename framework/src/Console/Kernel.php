<?php

namespace Framework\Console;

use Framework\Interfaces\Console\CommandInterface;
use Psr\Container\ContainerInterface;

class Kernel
{
    const COMMAND_NAMESPACE = 'Framework\\Console\\Commands\\';

    public function __construct(
        private ContainerInterface $container,
        private Application $application
    ) {
    }

    public function handle(): int
    {
        $this->registerCommands();

        $status = $this->application->run();

        return $status;
    }

    private function registerCommands(): void
    {
        $commandFiles = new \DirectoryIterator(__DIR__.'/Commands');

        foreach ($commandFiles as $commandFile) {
            if (! $commandFile->isFile()) {
                continue;
            }

            $command = self::COMMAND_NAMESPACE.pathinfo($commandFile, PATHINFO_FILENAME);

            if (is_subclass_of($command, CommandInterface::class)) {
                $name = (new \ReflectionClass($command))
                    ->getProperty('name')
                    ->getDefaultValue();

                $this->container->add("console:$name", $command);
            }
        }
    }
}
