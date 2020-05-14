<?php

namespace Wmandai\MobileMoney\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Identity
 * @package Wmandai\MobileMoney\Mpesa\Facades
 */
class Identity extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mpesa_identity';
    }
}
