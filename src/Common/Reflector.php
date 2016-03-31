<?php

namespace MartiAdrogue\Container\Common;

use ReflectionClass;
use ReflectionMethod;

class Reflector
{
    private $reflectionClass;
    private $object;

    public function __construct(ReflectionClass $reflectionClass)
    {
        $this->reflectionClass = $reflectionClass;
    }

    public function getClass(array $arguments)
    {
        $this->object = $this->reflectionClass->newInstanceArgs($arguments);

        return $this->object;
    }

    public function callSetMethod($name, array $arguments)
    {
        $reflectionMethod = new ReflectionMethod($this->reflectionClass->getName(), $name);
        $reflectionMethod->invokeArgs($this->object, $arguments);
    }
}
