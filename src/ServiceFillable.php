<?php
/**
 * This file is part of the martiadrogue/container package.
 *
 * @author    Martí Adrogué <marti.adrogue@gmail.com>
 * @copyright 2016 Martí Adrogué
 * @license   https://opensource.org/licenses/MIT MIT License
 */
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
