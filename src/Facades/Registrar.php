<?php

namespace Wmandai\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Registrar
 * @package Wmandai\Mpesa\Facades
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
