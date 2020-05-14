<?php

namespace Wmandai\MobileMoney\Mpesa\Events;

use Illuminate\Http\Request;

/**
 * Class QueueTimeoutEvent
 * @package Wmandai\MobileMoney\Events
 */
class QueueTimeoutEvent
{
    /**
     * @var Request
     */
    public $request;
    /**
     * @var string
     */
    public $initiator;

    /**
     * QueueTimeoutEvent constructor.
     * @param Request $request
     * @param string|null $initiator
     */
    public function __construct(Request $request, $initiator = null)
    {
        $this->request = $request;
        $this->initiator = $initiator;
    }
}
