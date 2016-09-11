<?php

namespace MartiAdrogue\Container;

use MartiAdrogue\Container\Reference\ServiceReference;
use MartiAdrogue\Container\Reference\AbstractReference;
use MartiAdrogue\Container\Reference\ParameterReference;

use MartiAdrogue\Container\ServiceLoader;

/**
 * @covers MartiAdrogue\Container\Container::<!public>
 * @uses MartiAdrogue\Container\Exception\ParameterNotFoundException
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::getParameter
     */
    public function shouldReturnTheParamRequired()
    {
        $parameters = [
            'hello' => 'world',
        ];
        $container = new Container([], $parameters);

        $this->assertEquals('world', $container->getParameter('hello'));
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::getParameter
     */
    public function shouldReturnParamsWithParents()
    {
        $parameters = [
            'first' => [
                'second' => 'foo',
                'third' => [
                    'fourth' => 'bar',
                ],
            ],
        ];
        $container = new Container([], $parameters);

        $this->assertEquals('foo', $container->getParameter('first.second'));
        $this->assertEquals('bar', $container->getParameter('first.third.fourth'));
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::hasParameter
     * @covers MartiAdrogue\Container\Container::getParameter
     */
    public function shouldCheckIfParameterFromTheListExists()
    {
        $parameters = [
            'group' => [
                'param' => 'bar',
            ],
        ];
        $container = new Container([], $parameters);

        $this->assertTrue($container->hasParameter('group.param'));
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::hasParameter
     * @covers MartiAdrogue\Container\Container::getParameter
     */
    public function shouldCheckIfParameterThatNoExists()
    {
        $parameters = [
            'group' => [
                'param' => 'bar',
            ],
        ];
        $container = new Container([], $parameters);

        $this->assertFalse($container->hasParameter('foo.bar'));
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::get
     * @covers MartiAdrogue\Container\Container::has
     * @covers MartiAdrogue\Container\Container::getParameter
     * @uses MartiAdrogue\Container\ServiceLoader
     * @uses MartiAdrogue\Container\Common\Reflector
     * @uses MartiAdrogue\Container\Reference\AbstractReference
     */
    public function shouldReciveServiceRequired()
    {
        $services = [
            'service' => [
                'class' => MockService::class,
                'arguments' => [
                    new ServiceReference('dependency'),
                    'foo',
                ],
                'calls' => [
                    [
                        'method' => 'setProperty',
                        'arguments' => [
                            new ParameterReference('group.param'),
                        ],
                    ],
                ],
            ],
            'dependency' => [
                'class' => MockDependency::class,
                'arguments' => [
                    new ParameterReference('group.param'),
                ],
            ],
        ];
        $parameters = [
            'group' => [
                'param' => 'bar',
            ],
        ];

        $container = new Container($services, $parameters);
        $service = $container->get('service');
        $this->assertInstanceOf(MockService::class, $service);

        return $service;
    }

    /**
     * @depends shouldReciveServiceRequired
     * @test
     */
    public function shouldTheServiceCallsHaveInicialized(MockService $service)
    {
        $property = $service->getProperty();
        $this->assertEquals('bar', $property);
    }

    /**
     * @depends shouldReciveServiceRequired
     * @test
     */
    public function shouldServiceParameterHaveBeenLoadedCorrectly(MockService $service)
    {
        $this->assertEquals('foo', $service->getParameter());
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::get
     * @covers MartiAdrogue\Container\Container::has
     * @covers MartiAdrogue\Container\Container::getParameter
     * @uses MartiAdrogue\Container\ServiceLoader
     * @uses MartiAdrogue\Container\Common\Reflector
     * @uses MartiAdrogue\Container\Reference\AbstractReference
     */
    public function shouldDepencencyParameterHaveBeenLoadedCorrectly()
    {
        $services = [
            'service' => [
                'class' => MockService::class,
                'arguments' => [
                    new ServiceReference('dependency'),
                    'foo',
                ],
                'calls' => [
                    [
                        'method' => 'setProperty',
                        'arguments' => [
                            new ParameterReference('group.param'),
                        ],
                    ],
                ],
            ],
            'dependency' => [
                'class' => MockDependency::class,
                'arguments' => [
                    new ParameterReference('group.param'),
                ],
            ],
        ];
        $parameters = [
            'group' => [
                'param' => 'bar',
            ],
        ];
        $container = new Container($services, $parameters);
        $dependency = $container->get('dependency');

        $this->assertEquals('bar', $dependency->getParameter());
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::get
     * @covers MartiAdrogue\Container\Container::getParameter
     * @covers MartiAdrogue\Container\Container::has
     * @uses MartiAdrogue\Container\ServiceLoader
     * @uses MartiAdrogue\Container\Common\Reflector
     * @uses MartiAdrogue\Container\Reference\AbstractReference
     */
    public function shouldReciveDepencencyOfAService()
    {
        $services = [
            'service' => [
                'class' => MockService::class,
                'arguments' => [
                    new ServiceReference('dependency'),
                    'foo',
                ],
                'calls' => [
                    [
                        'method' => 'setProperty',
                        'arguments' => [
                            new ParameterReference('group.param'),
                        ],
                    ],
                ],
            ],
            'dependency' => [
                'class' => MockDependency::class,
                'arguments' => [
                    new ParameterReference('group.param'),
                ],
            ],
        ];
        $parameters = [
            'group' => [
                'param' => 'bar',
            ],
        ];

        $container = new Container($services, $parameters);
        $service = $container->get('service');
        $dependency = $container->get('dependency');
        $this->assertInstanceOf(MockDependency::class, $dependency);
        $this->assertSame($dependency, $service->getDependency());
    }

    /**
     * @test
     * @covers MartiAdrogue\Container\Container::__construct
     * @covers MartiAdrogue\Container\Container::get
     * @covers MartiAdrogue\Container\Container::has
     * @covers MartiAdrogue\Container\Container::getParameter
     * @expectedException MartiAdrogue\Container\Exception\ParameterNotFoundException
     */
    public function shouldLaunchParameterNotFoundExceptionWhenAskForAParameterThatNeverExists()
    {
        $container = new Container([], []);
        $container->getParameter('foo');
    }
}

class MockService
{
    private $dependency;
    private $parameter;
    private $property;
    public function __construct(MockDependency $dependency, $parameter)
    {
        $this->dependency = $dependency;
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
class MockDependency
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
