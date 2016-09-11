<?php

namespace MartiAdrogue\Container;

use ReflectionClass;
use MartiAdrogue\Container\Common\Reflector;
use MartiAdrogue\Container\Exception\ServiceNotFoundException;
use MartiAdrogue\Container\Exception\ParameterNotFoundException;
use MartiAdrogue\Container\Exception\ContainerException;
use MartiAdrogue\Container\Reference\ParameterReference;
use MartiAdrogue\Container\Reference\ServiceReference;

use MartiAdrogue\Container\ServiceLoader;

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
    private $serviceStore;

    public function __construct(array $serviceSet, array $parameterSet)
    {
        $this->serviceSet = $serviceSet;
        $this->parameterSet = $parameterSet;
        $this->serviceStore = [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        $loader = new ServiceLoader($this);
        $loader->check($name);
        $this->saveServiceInStore($name, $loader);

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

    private function saveServiceInStore($name, $loader)
    {
        if (!isset($this->serviceStore[$name])) {
            $entry = $this->serviceSet[$name];
            $this->serviceStore[$name] = $loader->create($entry, $name);
        }
    }

    private function digDeepIntoParameters(array $tokenSet)
    {
        $context = $this->parameterSet;
        foreach ($tokenSet as $token) {
            $this->checkIfParameterExists($context, $token);
            $context = $context[$token];
        }

        return $context;
    }

    private function checkIfParameterExists($context, $token)
    {
        if (!isset($context[$token])) {
            throw new ParameterNotFoundException('Parameter not found: '.$token);
        }
    }
}
