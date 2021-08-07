<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Events\B2cPaymentFailedEvent;

/**
 * Class B2CFailedListener
 *
 * @package Wmandai\Mpesa\Listeners
 */
class B2CFailedListener
{
    /**
     * @param B2cPaymentFailedEvent $event
     */
    public function handle(B2cPaymentFailedEvent $event)
    {
        // $response = $event->bulkPaymentResponse;
        // $mpesaResponse = $event->response;
    }
}
