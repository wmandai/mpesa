<?php

namespace Wmandai\Mpesa\Tests;

use Wmandai\Mpesa\Daraja;

class DarajaTest extends TestCase
{
    /** @test */
    public function it_can_instantiate_an_object()
    {
        $sdk = new Daraja();

        $this->assertTrue(is_object($sdk));
    }
}
