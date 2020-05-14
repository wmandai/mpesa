<?php


namespace Wmandai\MobileMoney\Mpesa\Listeners;

use Wmandai\MobileMoney\Mpesa\Events\StkPushPaymentSuccessEvent;

/**
 * Class StkPaymentSuccessful
 * @package Wmandai\MobileMoney\Listeners
 */
class StkPaymentSuccessful
{
    /**
     * @param StkPushPaymentSuccessEvent $event
     */
    public function handle(StkPushPaymentSuccessEvent $event)
    {
        /** @var \Wmandai\MobileMoney\Mpesa\Database\Entities\MpesaStkCallback $stk */
        $stk = $event->stk_callback;
        $stk->request()->update(['status' => 'Paid']);
    }
}
