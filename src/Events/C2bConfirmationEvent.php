<?php

namespace Wmandai\Mpesa\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Wmandai\Mpesa\Database\Models\MpesaC2bCallback;

/**
 * Class C2BConfirmationEvent
 * @package Wmandai\Mpesa\Events
 */
class C2bConfirmationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var MpesaC2bCallback
     */
    public $transaction;
    /**
     * @var array
     */
    public $mpesa_response;

    /**
     * C2BConfirmationEvent constructor.
     * @param MpesaC2bCallback $c2bCallback
     * @param array $response
     */
    public function __construct(MpesaC2bCallback $c2bCallback, array $response = [])
    {
        $this->transaction = $c2bCallback;
        $this->mpesa_response = $response;
    }
}
