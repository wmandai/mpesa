<?php

namespace Wmandai\MobileMoney\Mpesa\Listeners;

use Wmandai\MobileMoney\Mpesa\Events\B2cPaymentSuccessEvent;

/**
 * Class B2CSuccessListener
 * @package Wmandai\MobileMoney\src\Mpesa\Listeners
 */
class B2CSuccessListener
{
    /**
     * @param B2cPaymentSuccessEvent $event
     */
    public function handle(B2cPaymentSuccessEvent $event)
    {
    }
}
