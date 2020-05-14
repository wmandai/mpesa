<?php

namespace Wmandai\Mpesa\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Wmandai\Mpesa\Database\Entities\MpesaStkCallback;

/**
 * Class StkPushPaymentSuccessEvent
 * @package Wmandai\Mpesa\Events
 */
class StkPushPaymentSuccessEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var MpesaStkCallback
     */
    public $stk_callback;
    /**
     * @var array
     */
    public $mpesa_response;

    /**
     * StkPushPaymentSuccessEvent constructor.
     * @param MpesaStkCallback $stkCallback
     * @param array $response
     */
    public function __construct(MpesaStkCallback $stkCallback, array $response = [])
    {
        $this->stk_callback = $stkCallback;
        $this->mpesa_response = $response;
    }
}
