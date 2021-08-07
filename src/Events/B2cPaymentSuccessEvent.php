<?php

namespace Wmandai\Mpesa\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Wmandai\Mpesa\Models\MpesaBulkPaymentResponse;

/**
 * Class B2cPaymentSuccessEvent
 *
 * @package Wmandai\Mpesa\Events
 */
class B2cPaymentSuccessEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var MpesaBulkPaymentResponse
     */
    public $bulkPaymentResponse;
    /**
     * @var array
     */
    public $response;

    /**
     * B2cPaymentSuccessEvent constructor.
     *
     * @param MpesaBulkPaymentResponse $mpesaBulkPaymentResponse
     * @param array                    $response
     */
    public function __construct(MpesaBulkPaymentResponse $mpesaBulkPaymentResponse, $response)
    {
        $this->bulkPaymentResponse = $mpesaBulkPaymentResponse;
        $this->response = $response;
    }
}
