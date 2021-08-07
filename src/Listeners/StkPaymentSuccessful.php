<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Events\StkPushPaymentSuccessEvent;

class StkPaymentSuccessful
{
    /**
     * @param StkPushPaymentSuccessEvent $event
     */
    public function handle(StkPushPaymentSuccessEvent $event)
    {
        $stk = $event->stk_callback;
        $stk->request()->update(['status' => 'Paid']);
    }
}
