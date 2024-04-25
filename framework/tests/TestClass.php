<?php

namespace Framework\Tests;

class TestClass
{
    public function __construct(
        private readonly SecondTestClass $secondTestClass,
    ){}

    public function getSecondTestClass(): SecondTestClass
    {
        return $this->secondTestClass;
    }
}