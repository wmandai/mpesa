<?php

namespace Wmandai\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class STK
 * @package Wmandai\Mpesa\Facades
 */
class STK extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mpesa_stk';
    }
}
