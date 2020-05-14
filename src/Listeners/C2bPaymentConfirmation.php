<?php

namespace Wmandai\MobileMoney\Mpesa\Listeners;

use Wmandai\MobileMoney\Mpesa\Database\Entities\MpesaStkRequest;
use Wmandai\MobileMoney\Mpesa\Events\C2bConfirmationEvent;

/**
 * Class C2bPaymentConfirmation
 * @package Wmandai\MobileMoney\Listeners
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
