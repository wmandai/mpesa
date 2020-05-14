<?php

namespace Wmandai\MobileMoney\Mpesa\Listeners;

use Wmandai\MobileMoney\Mpesa\Events\B2cPaymentFailedEvent;

/**
 * Class B2CFailedListener
 * @package Wmandai\MobileMoney\src\Mpesa\Listeners
 */
class B2CFailedListener
{
    /**
     * @param B2cPaymentFailedEvent $event
     */
    public function handle(B2cPaymentFailedEvent $event)
    {
    }
}
