<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Events\B2cPaymentSuccessEvent;

/**
 * Class B2CSuccessListener
 *
 * @package Wmandai\Mpesa\Listeners
 */
class B2CSuccessListener
{
    /**
     * @param B2cPaymentSuccessEvent $event
     */
    public function handle(B2cPaymentSuccessEvent $event)
    {
        // $response = $event->bulkPaymentResponse;
        // $mpesaResponse = $event->response;
    }
}
