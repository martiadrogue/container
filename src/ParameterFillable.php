<?php

namespace MartiAdrogue\Container;

/**
 * To be a container, it has to be able to store and retrieve instances of
 * parameters.
 */
interface ParameterFillable
{
    /**
     * Retrieve a parameter from the container.
     *
     * @param string $name The parameter name.
     *
     * @return mixed The parameter.
     *
     * @throws ContainerException On failure.
     */
    public function getParameter($name);
    /**
     * Check to see if the container has a parameter.
     *
     * @param string $name The parameter name.
     *
     * @return bool True if the container has the parameter.
     */
    public function hasParameter($name);
}
