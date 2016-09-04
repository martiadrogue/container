<?php

namespace MartiAdrogue\Container;

use Interop\Container\ContainerInterface;

/**
 * {@inheritdoc}
 */
interface ServiceFillable extends ContainerInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($name);
    /**
     * {@inheritdoc}
     */
    public function has($name);
}
