<?php

namespace Framework\Tests;

use Framework\Container\Container;
use Framework\Container\Exceptions\ContainerException;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_getting_service_from_container()
    {
        $container = new Container();

        $container->add('test-class', TestClass::class);

        $this->assertInstanceOf(TestClass::class, $container->get('test-class'));
    }

    public function test_container_has_exception_ContainerException_if_add_wrong_service()
    {
        $container = new Container();

        $this->expectException(ContainerException::class);
        
        $container->add('no-class');
    }

    public function test_has_method()
    {
        $container = new Container();

        $container->add('test-class', TestClass::class);

        $this->assertTrue($container->has('test-class'));
        $this->assertFalse($container->has('no-class'));
    }

    public function test_recursively_autowired()
    {
        $container = new Container();

        $container->add('test-class', TestClass::class);
        $testClass = $container->get('test-class');

        $this->assertInstanceOf(SecondTestClass::class, $testClass->getSecondTestClass());
    }
}