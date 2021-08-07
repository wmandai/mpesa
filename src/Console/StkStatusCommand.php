<?php

namespace Wmandai\Mpesa\Console;

use Exception;
use Illuminate\Console\Command;
use Wmandai\Mpesa\Daraja;
use Wmandai\Mpesa\Events\StkPushPaymentFailedEvent;
use Wmandai\Mpesa\Events\StkPushPaymentSuccessEvent;
use Wmandai\Mpesa\Models\MpesaStkCallback;
use Wmandai\Mpesa\Models\MpesaStkRequest;

/**
 * Class StkStatusCommand
 *
 * @package Wmandai\Mpesa\Commands
 */
class StkStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mpesa:query_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check status of all pending transactions';

    protected $mpesa;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->mpesa = new Daraja();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $results = $this->runStkQueries();
        dd($results);
    }

    /**
     * @return array
     */
    public function runStkQueries(): array
    {
        $stk = MpesaStkRequest::whereDoesntHave('response')->get();
        $success = $errors = [];
        foreach ($stk as $item) {
            try {
                $status = mpesaStkStatus($item->id);
                if (isset($status->errorMessage)) {
                    $errors[$item->CheckoutRequestID] = $status->errorMessage;
                    continue;
                }
                $attributes = [
                    'merchant_request_id' => $status->MerchantRequestID,
                    'checkout_request_id' => $status->CheckoutRequestID,
                    'result_code' => $status->ResultCode,
                    'result_desc' => $status->ResultDesc,
                    'amount' => $item->amount,
                ];
                $errors[$item->CheckoutRequestID] = $status->ResultDesc;
                $callback = MpesaStkCallback::create($attributes);
                $this->fireStkEvent($callback, get_object_vars($status));
            } catch (Exception $e) {
                $errors[$item->CheckoutRequestID] = $e->getMessage();
            }
        }
        return ['successful' => $success, 'errors' => $errors];
    }

    protected function fireStkEvent(MpesaStkCallback $stkCallback, $response): MpesaStkCallback
    {
        if ($stkCallback->result_code == 0) {
            event(new StkPushPaymentSuccessEvent($stkCallback, $response));
        } else {
            event(new StkPushPaymentFailedEvent($stkCallback, $response));
        }
        return $stkCallback;
    }
}
