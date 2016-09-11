<?php

namespace MartiAdrogue\Container;

use ReflectionClass;
use MartiAdrogue\Container\Common\Reflector;
use MartiAdrogue\Container\Exception\ServiceNotFoundException;
use MartiAdrogue\Container\Exception\ContainerException;
use MartiAdrogue\Container\Reference\ParameterReference;
use MartiAdrogue\Container\Reference\ServiceReference;

class ServiceLoader
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function check($name)
    {
        if (!$this->container->has($name)) {
            throw new ServiceNotFoundException('Service not found: '.$name);
        }
    }

    public function create(&$entry, $name)
    {
        $this->checkFormat($entry, $name);
        $entry['lock'] = true;

        $arguments = $this->getArguments($entry['arguments']);
        $phpReflector = new ReflectionClass($entry['class']);
        $reflector = new Reflector($phpReflector);
        $service = $reflector->getClass($arguments);

        if (isset($entry['calls'])) {
            $this->initialize($service, $reflector, $name, $entry['calls']);
        }

        return $service;
    }

    private function getArguments($definitionSet)
    {
        if (isset($definitionSet)) {
            return $this->resolveArguments($definitionSet);
        }

        return [];
    }

    private function resolveArguments(array $definitionSet)
    {
        $argumentSet = [];
        foreach ($definitionSet as $definition) {
            $argument = $definition;
            if ($definition instanceof ServiceReference) {
                $serviceName = $definition->getName();
                $argument = $this->container->get($serviceName);
            } elseif ($definition instanceof ParameterReference) {
                $parameterName = $definition->getName();
                $argument = $this->container->getParameter($parameterName);
            }

            $argumentSet[] = $argument;
        }

        return $argumentSet;
    }

    private function initialize($service, $reflector, $name, array $callDefinitionSet)
    {
        foreach ($callDefinitionSet as $callDefinition) {
            $this->checkCalls($service, $callDefinition, $name);

            $arguments = $this->getArguments($callDefinition['arguments']);
            $reflector->callSetMethod($callDefinition['method'], $arguments);
        }
    }


    private function checkFormat($entry, $name)
    {
        if (!is_array($entry) || !isset($entry['class'])) {
            throw new ContainerException($name.' service entry must be an array containing a \'class\' key');
        } elseif (!class_exists($entry['class'])) {
            throw new ContainerException($name.' service class does not exist: '.$entry['class']);
        } elseif (isset($entry['lock'])) {
            throw new ContainerException($name.' service contains a circular reference');
        }
    }

    private function checkCalls($service, $callDefinition, $name)
    {
        if (!is_array($callDefinition) || !isset($callDefinition['method'])) {
            throw new ContainerException($name.' service calls must be arrays containing a \'method\' key');
        } elseif (!is_callable([$service, $callDefinition['method']])) {
            throw new ContainerException($name.' service asks for call to uncallable method: '.
                $callDefinition['method']);
        }
    }
}
