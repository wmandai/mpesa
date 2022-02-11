<?php

namespace Wmandai\Mpesa\Http\Controllers;

use Illuminate\Http\Request;
use Wmandai\Mpesa\Events\QueueTimeoutEvent;
use Wmandai\Mpesa\Repositories\Mpesa;
use Wmandai\Mpesa\Traits\InteractsWithDatabase;
use Wmandai\Mpesa\Traits\InteractsWithNotification;

/**
 * MpesaController Class
 *
 * @package Wmandai\Mpesa\Http\Controllers
 */
class MpesaController extends Controller
{
    use InteractsWithNotification;
    use InteractsWithDatabase;

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function timeout($section = null)
    {
        if (config('mpesa.notifications.slack.enabled')) {
            $this->notify('Queue timeout: *' . $section . '*');
        }
        event(new QueueTimeoutEvent($this->request, $section));
        return response()->json(
            [
                'ResponseCode' => '00000000',
                'ResponseDesc' => 'success',
            ]
        );
    }

    public function result($section = null)
    {
        if (config('mpesa.notifications.slack.enabled')) {
            $this->notify('Incoming result: *' . $section . '*');
        }
        if ($section === 'b2c') {
            $this->handleB2cResult();
        }
        return response()->json(
            [
                'ResponseCode' => '00000000',
                'ResponseDesc' => 'success',
            ]
        );
    }

    public function confirmation()
    {
        if (config('mpesa.notifications.slack.enabled')) {
            $this->notify('MPESA Confirmation: *C2B*', true);
        }
        $this->transactionConfirmation($this->request->all());
        return response()->json(
            [
                'ResultCode' => 0,
                'ResultDesc' => 'Confirmation received successfully',
            ]
        );
    }
    public function validation()
    {
        if (config('mpesa.notifications.slack.enabled')) {
            $this->notify('MPESA Validate Payment URL: *C2B*');
        }
        if ($this->transactionValidation($this->request->all())) {
            return response()->json(
                [
                    'ResultCode' => 0,
                    'ResultDesc' => 'Accepted',
                ]
            );
        }
        return response()->json(
            [
                'ResultCode' => 1,
                'ResultDesc' => 'Rejected',
            ]
        );
    }

    public function stkCallback()
    {
        $this->acceptStkCallback($this->request->Body);
        return response()->json(
            [
                'ResultCode' => 0,
                'ResultDesc' => 'STK Callback received successfully',
            ]
        );
    }
}
