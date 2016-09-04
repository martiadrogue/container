<?php

namespace MartiAdrogue\Container\Reference;

use MartiAdrogue\Container\Reference\ServiceReference;

class ServiceReferenceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldGetTheNameOfService()
    {
        $reference = new ServiceReference('foo.bar');
        $this->assertEquals('foo.bar', $reference->getName());
    }
}
