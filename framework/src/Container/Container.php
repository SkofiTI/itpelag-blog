<?php

namespace Framework\Container;

use Framework\Container\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    public function add(string $id, string|object $concrete = null)
    {
        if (is_null($concrete)) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id could not be resolve");
            }

            $concrete = $id;
        }

        $this->services[$id] = $concrete;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id not found");
            }

            $this->add($id);
        }

        $instance = $this->resolve($this->services[$id]);

        return $instance;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    private function resolve(string|object $class)
    {
        $reflectionClass = new \ReflectionClass($class);

        $constructor = $reflectionClass->getConstructor();

        if (is_null($constructor)) {
            return $reflectionClass->newInstance();
        }

        $constructorParameters = $constructor->getParameters();
        $classDependencies = $this->resolveClassDependencies($constructorParameters);
        $instance = $reflectionClass->newInstanceArgs($classDependencies);
        
        return $instance;
    }

    private function resolveClassDependencies(array $constructorParameters): array
    {
        $classDependencies = [];

        /** @var \ReflectionParameter $parameter */
        foreach ($constructorParameters as $parameter) {
            $serviceType = $parameter->getType();
            $service = $this->get($serviceType->getName());
            $classDependencies[] = $service;
        }

        return $classDependencies;
    }
}