<?php

namespace MartiAdrogue\Skeleton;

use ReflectionClass;
use Object;
use MartiAdrogue\Container\Common\Reflector;
use MartiAdrogue\Container\Reference\ParameterReference;
use MartiAdrogue\Container\Reference\ServiceReference;

class ReflectorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldReturnAnInstanceOfRequiredClass()
    {
        $constructorArgs = [];
        $reflectionClass = $this->getMock(ReflectionClass::class, ['newInstanceArgs'], [], 'ReflectionClassMock', false);
        $reflectionClass
            ->expects($this->once())
            ->method('newInstanceArgs')
            ->will($this->returnValue(new ServiceReference('')));

        $reference = new Reflector($reflectionClass);
        $object = $reference->getClass($constructorArgs);
        $this->assertInstanceOf(ServiceReference::class, $object);
    }
}
