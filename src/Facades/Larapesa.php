<?php

namespace Wmandai\MobileMoney\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wmandai\MobileMoney\Mpesa\Skeleton\SkeletonClass
 */
class LaraPesa extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-mpesa';
    }
}
