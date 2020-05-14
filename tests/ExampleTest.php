<?php

namespace Wmandai\Mpesa\Tests;

use Orchestra\Testbench\TestCase;
use Wmandai\MobileMoney\Mpesa\LaravelMpesaServiceProvider;

class ExampleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelMpesaServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
