<?php

namespace Wmandai\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * B2C Facade
 * @method static \Wmandai\Mpesa\Library\BulkSender
 */

class B2C extends Facade
{
    /**
     * @method static \Wmandai\Mpesa\Library\BulkSender
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mpesa_b2c';
    }
}
