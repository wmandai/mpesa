<?php

namespace Wmandai\Mpesa\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * @package Wmandai\Mpesa\Events
 */
class MpesaValidationEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Validation request from MPESA API
     */
    public $transaction;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }
}
