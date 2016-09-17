<?php
/**
 * This file is part of the martiadrogue/container package.
 *
 * @author    Martí Adrogué <marti.adrogue@gmail.com>
 * @copyright 2016 Martí Adrogué
 * @license   https://opensource.org/licenses/MIT MIT License
 */
namespace MartiAdrogue\Container\Exception;

use Interop\Container\Exception\NotFoundException as InteropNotFoundException;
use Exception;

class ServiceNotFoundException extends Exception implements InteropNotFoundException
{

    public function __construct()
    {
        # code...
    }
}
