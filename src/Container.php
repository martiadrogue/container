<?php

namespace MartiAdrogue\Container;

use ReflectionClass;
use ReflectionMethod;
use MartiAdrogue\Container\Exception\ServiceNotFoundException;
use MartiAdrogue\Container\Exception\ParameterNotFoundException;
use MartiAdrogue\Container\Exception\ContainerException;
use MartiAdrogue\Container\Reference\ParameterReference;
use MartiAdrogue\Container\Reference\ServiceReference;

/**
 * Loading the definitions into properties that can be accessed later. We have
 * also created a serviceStore property, and initialized it to be an empty
 * array. When the container is asked to create services, we will save these in
 * this array so that they can be retrieved later without having to recreate
 * them.
 */
class Container implements ServiceFillable, ParameterFillable
{
    private $serviceSet;
    private $parameterSet;
    private $ServiceStore;

    public function __construct(array $serviceSet, array $parameterSet)
    {
        $this->serviceSet = $serviceSet;
        $this->parameterSet = $parameterSet;
        $this->ServiceStore = [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {

        if (!$this->has($name)) {
            throw new ServiceNotFoundException('Service not found: '.$name);
        }

        $this->saveServiceToStore($name);

        return $this->serviceStore[$name];
    }

    /**
     * Checks to see if the container has the definition for a service and
     * returns it. It use `.` as a delimiter ot Access any element within
     * N-array using a single string.
     *
     * @param string $name Identifier of the entry to look for
     *
     * @throws ServiceNotFoundException  No entry was found for this identifier
     * @throws ServiceContainerException Error while retrieving the entry
     *
     * @return mixed Service
     */
    public function getParameter($name)
    {
        $tokenSet = explode('.', $name);

        return $this->digDeepIntoParameters($tokenSet);
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return isset($this->serviceSet[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        try {
            $this->getParameter($name);
        } catch (ParameterNotFoundException $exception) {
            return false;
        }

        return true;
    }

    private function createService($name)
    {
        $entry = &$this->serviceSet[$name];

        if (!is_array($entry) || !isset($entry['class'])) {
            throw new ContainerException($name.' service entry must be an array containing a \'class\' key');
        } elseif (!class_exists($entry['class'])) {
            throw new ContainerException($name.' service class does not exist: '.$entry['class']);
        } elseif (isset($entry['lock'])) {
            throw new ContainerException($name.' service contains a circular reference');
        }

        $entry['lock'] = true;

        $arguments = $this->getArguments($entry['arguments']);
        $service = $this->getServiceByReflection($entry['class'], $arguments);

        if (isset($entry['calls'])) {
            $this->initializeService($service, $entry['class'], $name, $entry['calls']);
        }

        return $service;
    }

    private function resolveArguments(array $definitionSet)
    {
        $argumentSet = [];
        foreach ($definitionSet as $definition) {
            $argument = $definition;
            if ($definition instanceof ServiceReference) {
                $serviceName = $definition->getName();
                $argument = $this->get($serviceName);
            } elseif ($definition instanceof ParameterReference) {
                $parameterName = $definition->getName();
                $argument = $this->getParameter($parameterName);
            }

            $argumentSet[] = $argument;
        }

        return $argumentSet;
    }

    private function saveServiceToStore($name)
    {
        if (!isset($this->serviceStore[$name])) {
            $this->serviceStore[$name] = $this->createService($name);
        }
    }

    private function initializeService($service, $className, $name, array $callDefinitionSet)
    {
        foreach ($callDefinitionSet as $callDefinition) {
            if (!is_array($callDefinition) || !isset($callDefinition['method'])) {
                throw new ContainerException($name.' service calls must be arrays containing a \'method\' key');
            } elseif (!is_callable([$service, $callDefinition['method']])) {
                throw new ContainerException($name.' service asks for call to uncallable method: '.$callDefinition['method']);
            }

            $arguments = $this->getArguments($callDefinition['arguments'], $callDefinition['arguments']);
            $this->callMethodByReflection($service, $className, $callDefinition['method'], $arguments);
        }
    }

    private function digDeepIntoParameters(array $tokenSet)
    {
        $context = $this->parameterSet;
        foreach ($tokenSet as $token) {
            if (!isset($context[$token])) {
                throw new ParameterNotFoundException('Parameter not found: '.$token);
            }
            $context = $context[$token];
        }

        return $context;
    }

    private function getArguments($definitionSet)
    {
        if (isset($definitionSet)) {
            return $this->resolveArguments($definitionSet);
        }

        return [];
    }

    private function getServiceByReflection($className, $arguments)
    {
        $reflector = new ReflectionClass($className);

        return $reflector->newInstanceArgs($arguments);
    }

    private function callMethodByReflection($object, $className, $methodName, array $arguments)
    {
        $reflector = new ReflectionMethod($className, $methodName);

        return $reflector->invokeArgs($object, $arguments);
    }
}
