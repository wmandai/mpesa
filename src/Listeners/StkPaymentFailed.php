<?php

namespace Wmandai\MobileMoney\Mpesa\Listeners;

use Wmandai\MobileMoney\Mpesa\Events\StkPushPaymentFailedEvent;

/**
 * Class StkPaymentFailed
 * @package Wmandai\MobileMoney\Listeners
 */
class StkPaymentFailed
{
    /**
     * @param StkPushPaymentFailedEvent $event
     */
    public function handle(StkPushPaymentFailedEvent $event)
    {
        /** @var \Wmandai\MobileMoney\Mpesa\Database\Entities\MpesaStkCallback $stk */
        $stk = $event->stk_callback;
        $stk->request()->update(['status' => 'Failed']);
    }
}
