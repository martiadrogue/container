<?php

namespace MartiAdrogue\Container;

use MartiAdrogue\Container\Reference\ServiceReference;
use MartiAdrogue\Container\Reference\ParameterReference;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldReturnTheParamRequired()
    {
        $parameters = [
            'hello' => 'world',
        ];
        $container = new Container([], $parameters);

        $this->assertEquals('world', $container->getParameter('hello'));
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    public function shouldReciveServiceRequired() {
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
    public function shouldTheServiceCallsHaveInicialized(MockService $service) {
        $property = $service->getProperty();
        $this->assertEquals('bar', $property);
    }


    /** @test */
    public function shouldReciveDepencencyOfAService() {
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
     * @depends shouldReciveServiceRequired
     * @test
     */
    public function shouldServiceParameterHaveBeenLoadedCorrectly(MockService $service)
    {
        $this->assertEquals('foo', $service->getParameter());
    }

    /** @test */
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
     * @expectedException MartiAdrogue\Container\Exception\ServiceNotFoundException
     */
    public function shouldLaunchServiceNotFoundException()
    {
        $container = new Container([], []);
        $container->get('foo');
    }

    /**
     * @test
     * @expectedException MartiAdrogue\Container\Exception\ParameterNotFoundException
     */
    public function shouldLaunchParameterNotFoundExceptionWhenAskForAParameterThatNeverExists()
    {
        $container = new Container([], []);
        $container->getParameter('foo');
    }

    /**
     * @test
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage must be an array containing a 'class' key
     */
    public function shouldLaunchContainerExceptionAfterABadServiceEntry()
    {
        $container = new Container(['foo' => 'bar'], []);
        $container->get('foo');
    }

    /**
     * @test
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage class does not exist
     */
    public function shouldLaunchContainerExceptionAfterAnInvalidClassPath()
    {
        $container = new Container(['foo' => ['class' => 'LALALALALALA']], []);
        $container->get('foo');
    }

    /**
     * @test
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage circular reference
     */
    public function shouldLaunchContainerExceptionWhenThereIsACircularReference()
    {
        $container = new Container([
            'foo' => [
                'class' => MockService::class,
                'arguments' => [
                    new ServiceReference('bar'),
                ],
            ],
            'bar' => [
                'class' => MockService::class,
                'arguments' => [
                    new ServiceReference('foo'),
                ],
            ],
        ], []);
        $container->get('foo');
    }

    /**
     * @test
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage service calls must be arrays containing a 'method' key
     */
    public function shouldLaunchContainerExceptionWhenThereIsNoMethod()
    {
        $container = new Container([
            'foo' => [
                'class' => MockDependency::class,
                'arguments' => [
                    'foo',
                ],
                'calls' => [
                    ['foo'],
                ],
            ],
        ], []);
        $container->get('foo');
    }

    /**
     * @test
     * @expectedException        MartiAdrogue\Container\Exception\ContainerException
     * @expectedExceptionMessage call to uncallable method
     */
    public function shouldLaunchContainerExceptionWhenThereIsAnUncallableMethod()
    {
        $container = new Container([
            'foo' => [
                'class' => MockDependency::class,
                'arguments' => [
                    'foo',
                ],
                'calls' => [
                    ['method' => 'LALALALALA'],
                ],
            ],
        ], []);
        $container->get('foo');
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
