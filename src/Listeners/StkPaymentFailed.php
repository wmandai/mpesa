<?php

namespace Wmandai\Mpesa\Listeners;

use Wmandai\Mpesa\Events\StkPushPaymentFailedEvent;

/**
 * Class StkPaymentFailed
 *
 * @package Wmandai\Mpesa\Listeners
 */
class StkPaymentFailed
{
    /**
     * @param StkPushPaymentFailedEvent $event
     */
    public function handle(StkPushPaymentFailedEvent $event)
    {
        $stk = $event->stk_callback;
        $stk->request()->update(['status' => 'Failed']);
    }
}
