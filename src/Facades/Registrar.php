<?php

namespace Wmandai\MobileMoney\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Registrar
 * @package Wmandai\MobileMoney\Mpesa\Facades
 */
class Registrar extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mpesa_registrar';
    }
}
