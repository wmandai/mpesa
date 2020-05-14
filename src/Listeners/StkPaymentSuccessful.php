<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Events\StkPushPaymentSuccessEvent;

/**
 * Class StkPaymentSuccessful
 * @package Wmandai\Mpesa\Listeners
 */
class StkPaymentSuccessful
{
    /**
     * @param StkPushPaymentSuccessEvent $event
     */
    public function handle(StkPushPaymentSuccessEvent $event)
    {
        /**
         * @var \Wmandai\Mpesa\Database\Entities\MpesaStkCallback $stk
         *
         */
        $stk = $event->stk_callback;
        $stk->request()->update(['status' => 'Paid']);
    }
}
