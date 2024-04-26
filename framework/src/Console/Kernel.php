<?php

namespace Framework\Console;

use Psr\Container\ContainerInterface;

class Kernel
{
    public function __construct(
        private ContainerInterface $container,
        private Application $application
    ){}

    public function handle(): int
    {
        $this->registerCommands();

        $status = $this->application->run();

        return 0;
    }

    private function registerCommands(): void
    {
        $commandFiles = new \DirectoryIterator(__DIR__.'/Commands');
        $commandNamespace = $this->container->get('framework-commands-namespace');

        foreach ($commandFiles as $commandFile) {
            if (!$commandFile->isFile()) {
                continue;
            }

            $command = $commandNamespace.pathinfo($commandFile, PATHINFO_FILENAME);
            
            if (is_subclass_of($command, CommandInterface::class)) {
                $name = (new \ReflectionClass($command))
                    ->getProperty('name')
                    ->getDefaultValue();
                
                $this->container->add("console:$name", $command);
            }
        }
    }
}