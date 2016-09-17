<?php
/**
 * This file is part of the martiadrogue/container package.
 *
 * @author    Martí Adrogué <marti.adrogue@gmail.com>
 * @copyright 2016 Martí Adrogué
 * @license   https://opensource.org/licenses/MIT MIT License
 */
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
