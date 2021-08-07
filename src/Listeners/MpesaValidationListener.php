<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Events\MpesaValidationEvent;

class MpesaValidationListener
{
    public function handle(MpesaValidationEvent $event)
    {
        // $customerAccountNumber = $event->transaction->BillRefNumber;
    }
}
