<?php
/**
 * This file is part of the martiadrogue/container package.
 *
 * @author    Martí Adrogué <marti.adrogue@gmail.com>
 * @copyright 2016 Martí Adrogué
 * @license   https://opensource.org/licenses/MIT MIT License
 */
namespace MartiAdrogue\Container\Exception;

use Interop\Container\Exception\ContainerException as InteropContainerException;
use Exception;

class ContainerException extends Exception implements InteropContainerException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}
