<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Events\MpesaConfirmationEvent;
use Wmandai\Mpesa\Models\MpesaStkRequest;

class MpesaConfirmationListener
{
    public function handle(MpesaConfirmationEvent $event)
    {
        // $c2b = $event->transaction;
        // $response = $event->mpesa_response;
        // Try to check if this was from STK
        // $request = MpesaStkRequest::whereReference($c2b->BillRefNumber)->first();
    }
}
