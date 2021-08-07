<?php

namespace Wmandai\Mpesa\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class FacadeTest extends TestCase
{
    /** @test */
    public function it_can_test_it_is_a_facade()
    {
        $facade = new ReflectionClass('Illuminate\Support\Facades\Facade');

        $reflection = new ReflectionClass('Wmandai\Mpesa\Facades\Mpesa');

        $this->assertTrue($reflection->isSubclassOf($facade));
    }

    /** @test */
    public function it_can_test_it_is_a_facade_accessor()
    {
        $reflection = new ReflectionClass('Wmandai\Mpesa\Facades\Mpesa');

        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $this->assertEquals('mpesa', $method->invoke(null));
    }
}
