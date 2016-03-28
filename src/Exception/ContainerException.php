<?php

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
