<?php

namespace MartiAdrogue\Container;

use MartiAdrogue\Container\Exception\ServiceNotFoundException;
use MartiAdrogue\Container\Reference\ServiceReference;
use MartiAdrogue\Container\Reference\AbstractReference;
use MartiAdrogue\Container\Reference\ParameterReference;

use MartiAdrogue\Container\MockEntry;
use MartiAdrogue\Container\ServiceLoader;

/**
 * @covers MartiAdrogue\Container\ServiceLoader::<!public>
 */
class ServiceLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers MartiAdrogue\Container\ServiceLoader::__construct
     * @covers MartiAdrogue\Container\ServiceLoader::check
     * @uses MartiAdrogue\Container\Exception\ServiceNotFoundException
     * @expectedException MartiAdrogue\Container\Exception\ServiceNotFoundException
     */
    public function shouldLaunchServiceNotFoundException()
    {
        $containerMock = $this->getMock(Container::class, ['has'], [], '', false);
        $containerMock->expects($this->once())->method('has')->willReturn(false);

        $loader = new ServiceLoader($containerMock);
        $loader->check('foo');
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\ServiceLoader::__construct
     * @covers MartiAdrogue\Container\ServiceLoader::create
     * @uses        MartiAdrogue\Container\Exception\ContainerException
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage array service entry must containing a 'class' key
     */
    public function shouldStopIfServiceIsCorrupted()
    {
        $containerMock = $this->getMock(Container::class, [], [], '', false);
        $entry = ['cluss' => 'foo'];
        $loader = new ServiceLoader($containerMock);
        $loader->create($entry, 'foo');
    }

    /**
    * @test
    * @covers MartiAdrogue\Container\ServiceLoader::__construct
    * @covers MartiAdrogue\Container\ServiceLoader::create
    * @uses        MartiAdrogue\Container\Exception\ContainerException
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage class does not exist
     */
    public function shouldStopIfServiceIsClassDoesNotExist()
    {
        $containerMock = $this->getMock(Container::class, [], [], '', false);
        $entry = ['class' => 'foo'];
        $loader = new ServiceLoader($containerMock);
        $loader->create($entry, 'foo');
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\ServiceLoader::__construct
     * @covers MartiAdrogue\Container\ServiceLoader::create
     * @uses MartiAdrogue\Container\Common\Reflector
     * @uses        MartiAdrogue\Container\Exception\ContainerException
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage array service entry must containing a 'method' key
     */
    public function shouldStopWhenServiceHasNotMethodKey()
    {
        $containerMock = $this->getMock(Container::class, [], [], '', false);
        $entry = ['class' => MockDependencyServive::class,
                    'arguments' => [ 'foo', ],
                    'calls' => [ ['foo'], ],
                ];
        $loader = new ServiceLoader($containerMock);
        $loader->create($entry, 'foo');
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\ServiceLoader::__construct
     * @covers MartiAdrogue\Container\ServiceLoader::create
     * @uses MartiAdrogue\Container\Common\Reflector
     * @uses        MartiAdrogue\Container\Exception\ContainerException
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage call to uncallable method
     */
    public function shouldStopWhenServiceHasNotRealMethodCall()
    {
        $containerMock = $this->getMock(Container::class, [], [], '', false);
        $entry = ['class' => MockDependencyServive::class,
                    'arguments' => [ 'foo', ],
                    'calls' => [ ['method' => 'LALALALALA'], ],
                ];
        $loader = new ServiceLoader($containerMock);
        $loader->create($entry, 'foo');
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\ServiceLoader::__construct
     * @covers MartiAdrogue\Container\ServiceLoader::create
     * @uses        MartiAdrogue\Container\Common\Reflector
     */
    public function shouldServiceChangeItsState()
    {
        $entry = ['class' => MockEntry::class, 'arguments' => ['777']];
        $containerMock = $this->getMock(Container::class, [], [], '', false);

        $loader = new ServiceLoader($containerMock);
        $loader->create($entry, MockEntry::class);

        $this->assertArrayHasKey('lock', $entry, "When a service is in use it must have key lock with its default value.");
        $this->assertSame(true, $entry['lock'], "During the use of a service its lock key must have value TRUE.");
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\ServiceLoader::__construct
     * @covers MartiAdrogue\Container\ServiceLoader::create
     * @uses        MartiAdrogue\Container\Common\Reflector
     */
    public function shouldGetServiceCalled()
    {
        $entry = ['class' => MockEntry::class, 'arguments' => ['777']];
        $containerMock = $this->getMock(Container::class, [], [], '', false);

        $loader = new ServiceLoader($containerMock);
        $service = $loader->create($entry, MockEntry::class);

        $this->assertInstanceOf(MockEntry::class, $service, "The key class of entry and object returned must com from same class.");
    }
}

class MockEntry
{
    private $parameter;
    private $property;

    public function __construct($parameter)
    {
        $this->parameter = $parameter;
    }
    public function getDependency()
    {
        return $this->dependency;
    }
    public function getParameter()
    {
        return $this->parameter;
    }
    public function setProperty($value)
    {
        $this->property = $value;
    }
    public function getProperty()
    {
        return $this->property;
    }
}

class MockDependencyServive
{
    private $parameter;
    public function __construct($parameter)
    {
        $this->parameter = $parameter;
    }
    public function getParameter()
    {
        return $this->parameter;
    }
}
