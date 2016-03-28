<?php

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
