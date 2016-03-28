<?php

namespace MartiAdrogue\Skeleton;

use MartiAdrogue\Container\Reference\ParameterReference;

class ParameterReferenceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldGetTheNameOfParameter()
    {
        $reference = new ParameterReference('foo.bar');
        $this->assertEquals('foo.bar', $reference->getName());    }
}
