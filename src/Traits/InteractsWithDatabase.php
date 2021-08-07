<?php

namespace Wmandai\Mpesa\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Wmandai\Mpesa\Events\B2cPaymentFailedEvent;
use Wmandai\Mpesa\Events\B2cPaymentSuccessEvent;
use Wmandai\Mpesa\Events\MpesaConfirmationEvent;
use Wmandai\Mpesa\Events\MpesaValidationEvent;
use Wmandai\Mpesa\Events\StkPushPaymentFailedEvent;
use Wmandai\Mpesa\Events\StkPushPaymentSuccessEvent;
use Wmandai\Mpesa\Events\StkPushRequestedEvent;
use Wmandai\Mpesa\Models\MpesaBulkPaymentRequest;
use Wmandai\Mpesa\Models\MpesaBulkPaymentResponse;
use Wmandai\Mpesa\Models\MpesaC2bCallback;
use Wmandai\Mpesa\Models\MpesaStkCallback;
use Wmandai\Mpesa\Models\MpesaStkRequest;

trait InteractsWithDatabase
{

    public function saveStkRequest($body, $response)
    {
        $stk = MpesaStkRequest::create(
            [
                'phone' => $body['PartyA'],
                'amount' => $body['Amount'],
                'reference' => $body['AccountReference'],
                'description' => $body['TransactionDesc'],
                'checkout_request_id' => $response['CheckoutRequestID'],
                'merchant_request_id' => $response['MerchantRequestID'],
                'user_id' => @(Auth::id() ?: request('user_id')),
            ]
        );
        event(new StkPushRequestedEvent($stk, request()));
        return $stk;
    }
    /**
     * @param $response
     * @param array $body
     *
     * @return MpesaBulkPaymentRequest|\Illuminate\Database\Eloquent\Model
     */
    public function saveB2cRequest($body, $response)
    {
        return MpesaBulkPaymentRequest::create(
            [
                'conversation_id' => $response->ConversationID,
                'originator_conversation_id' => $response->OriginatorConversationID,
                'amount' => $body['Amount'],
                'phone' => $body['PartyB'],
                'remarks' => $body['Remarks'],
                'command_id' => $body['CommandID'],
                'user_id' => Auth::id(),
            ]
        );
    }

    public function transactionConfirmation($transaction)
    {
        Log::error($transaction);
        // TODO change table names
        $transaction = [
            'transaction_type' => $transaction->TransactionType,
        ];
        $callback = MpesaC2bCallback::create($transaction);
        event(new MpesaConfirmationEvent($callback, $transaction));
        return $callback;
    }
    public function transactionValidation($transaction)
    {
        event(new MpesaValidationEvent($transaction));
        return true;
    }

    public function acceptStkCallback($transaction)
    {
        // TODO check database columns
        Log::error($transaction);
        $data = $transaction->stkCallback;
        $real_data = [
            'MerchantRequestID' => $data->MerchantRequestID,
            'CheckoutRequestID' => $data->CheckoutRequestID,
            'ResultCode' => $data->ResultCode,
            'ResultDesc' => $data->ResultDesc,
        ];
        if ($data->ResultCode == 0) {
            $payload = $data->CallbackMetadata->Item;
            foreach ($payload as $callback) {
                $real_data[$callback->Name] = @$callback->Value;
            }
            $callback = MpesaStkCallback::create($real_data);
        } else {
            $callback = MpesaStkCallback::create($real_data);
        }
        $this->fireStkEvent($callback, get_object_vars($data));
        return $callback;
    }

    public function handleB2cResult()
    {
        $data = request('Result');

        //check if data is an array
        if (!is_array($data)) {
            $data->toArray();
        }

        $common = [
            'ResultType', 'ResultCode', 'ResultDesc', 'OriginatorConversationID', 'ConversationID', 'TransactionID',
        ];
        $seek = ['OriginatorConversationID' => $data['OriginatorConversationID']];

        if ($data['ResultCode'] !== 0) {
            $response = MpesaBulkPaymentResponse::updateOrCreate($seek, Arr::only($data, $common));
            event(new B2cPaymentFailedEvent($response, $data));
            return $response;
        }
        $resultParameter = $data['ResultParameters'];

        $data['ResultParameters'] = json_encode($resultParameter);
        $response = MpesaBulkPaymentResponse::updateOrCreate($seek, Arr::except($data, ['ReferenceData']));
        $this->saveResultParams($resultParameter, $response);
        event(new B2cPaymentSuccessEvent($response, $data));
        return $response;
    }

    public function saveResultParams(
        array $params,
        MpesaBulkPaymentResponse $response
    ): \Illuminate\Database\Eloquent\Model {
        $params_payload = $params['ResultParameter'];
        $new_params = Arr::pluck($params_payload, 'Value', 'Key');
        // TODO rename data to something meaningful
        return $response->data()->create($new_params);
    }

    public function fireStkEvent(MpesaStkCallback $stkCallback, $response): MpesaStkCallback
    {
        if ($stkCallback->result_code == 0) {
            event(new StkPushPaymentSuccessEvent($stkCallback, $response));
        } else {
            event(new StkPushPaymentFailedEvent($stkCallback, $response));
        }
        return $stkCallback;
    }
}
