<?php

namespace Wmandai\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Identity
 * @package Wmandai\Mpesa\Facades
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
