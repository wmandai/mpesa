<?php

namespace Wmandai\Mpesa\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Wmandai\Mpesa\Models\MpesaC2bCallback;

class MpesaConfirmationEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

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
     *
     * @param MpesaC2bCallback $c2bCallback
     * @param array            $response
     */
    public function __construct(MpesaC2bCallback $c2bCallback, array $response = [])
    {
        $this->transaction = $c2bCallback;
        $this->mpesa_response = $response;
    }
}
