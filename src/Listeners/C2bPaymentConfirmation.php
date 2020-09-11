<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Database\Models\MpesaStkRequest;
use Wmandai\Mpesa\Events\C2bConfirmationEvent;

/**
 * Class C2bPaymentConfirmation
 * @package Wmandai\Mpesa\Listeners
 */
class C2bPaymentConfirmation
{
    /**
     * Handle the event.
     *
     * @param C2bConfirmationEvent $event
     * @return void
     */
    public function handle(C2bConfirmationEvent $event)
    {
        $c2b = $event->transaction;
        // Try to check if this was from STK
        $request = MpesaStkRequest::whereReference($c2b->BillRefNumber)->first();
    }
}
