<?php

namespace MartiAdrogue\Container;

use Interop\Container\ContainerInterface;

/**
 * {@inheritDoc}
 */
interface ServiceFillable extends ContainerInterface {
    /**
     * {@inheritDoc}
     */
    public function get($name);
    /**
     * {@inheritDoc}
     */
    public function has($name);
}
