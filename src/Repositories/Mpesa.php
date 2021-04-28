<?php

namespace Wmandai\Mpesa\Repositories;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Wmandai\Mpesa\Database\Models\MpesaBulkPaymentRequest;
use Wmandai\Mpesa\Database\Models\MpesaBulkPaymentResponse;
use Wmandai\Mpesa\Database\Models\MpesaC2bCallback;
use Wmandai\Mpesa\Database\Models\MpesaStkCallback;
use Wmandai\Mpesa\Database\Models\MpesaStkRequest;
use Wmandai\Mpesa\Events\B2cPaymentFailedEvent;
use Wmandai\Mpesa\Events\B2cPaymentSuccessEvent;
use Wmandai\Mpesa\Events\C2bConfirmationEvent;
use Wmandai\Mpesa\Events\StkPushPaymentFailedEvent;
use Wmandai\Mpesa\Events\StkPushPaymentSuccessEvent;
use Wmandai\Mpesa\Notifications\MpesaNotification;

/**
 * Class Mpesa
 *
 * @package Wmandai\Mpesa\Repositories
 */
class Mpesa
{
    /**
     * @param string $json
     *
     * @return $this|array|\Illuminate\Database\Eloquent\Model
     */
    public function processStkPushCallback($json)
    {
        $object = json_decode($json);
        $data = $object->stkCallback;
        $real_data = [
            'MerchantRequestID' => $data->MerchantRequestID,
            'CheckoutRequestID' => $data->CheckoutRequestID,
            'ResultCode' => $data->ResultCode,
            'ResultDesc' => $data->ResultDesc,
        ];
        if ($data->ResultCode == 0) {
            $_payload = $data->CallbackMetadata->Item;
            foreach ($_payload as $callback) {
                $real_data[$callback->Name] = @$callback->Value;
            }
            $callback = MpesaStkCallback::create($real_data);
        } else {
            $callback = MpesaStkCallback::create($real_data);
        }
        $this->fireStkEvent($callback, get_object_vars($data));
        return $callback;
    }

    /**
     * @param $response
     * @param array $body
     *
     * @return MpesaBulkPaymentRequest|\Illuminate\Database\Eloquent\Model
     */
    public function saveB2cRequest($response, $body = [])
    {
        return MpesaBulkPaymentRequest::create([
            'conversation_id' => $response->ConversationID,
            'originator_conversation_id' => $response->OriginatorConversationID,
            'amount' => $body['Amount'],
            'phone' => $body['PartyB'],
            'remarks' => $body['Remarks'],
            'CommandID' => $body['CommandID'],
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * @param string $json
     *
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function processConfirmation($json)
    {
        $data = json_decode($json, true);
        $callback = MpesaC2bCallback::create($data);
        event(new C2bConfirmationEvent($callback, $data));
        return $callback;
    }

    /**
     * @return MpesaBulkPaymentResponse|\Illuminate\Database\Eloquent\Model
     */
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

        $response = null;
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

    public function saveResultParams(array $params, MpesaBulkPaymentResponse $response): \Illuminate\Database\Eloquent\Model
    {
        $params_payload = $params['ResultParameter'];
        $new_params = Arr::pluck($params_payload, 'Value', 'Key');
        return $response->data()->create($new_params);
    }

    /**
     * @param string|null $initiator
     *
     * @return MpesaBulkPaymentResponse|void
     */
    public function handleResult($initiator = null)
    {
        if ($initiator === 'b2c') {
            return $this->handleB2cResult();
        }
        return;
    }

    /**
     * @param $title
     * @param bool $important
     */
    // TODO notify via slack incoming stk callback
    public function notification($title, $important = false): void
    {
        $slack = config('mpesa.notifications.slack_web_hook');
        if (!$important && empty($slack) && config('mpesa.notifications.only_important')) {
            return;
        }
        $payload = [
            'message' => request()->all(),
            'title' => $title,
        ];

        Notification::route('slack', config('mpesa.notifications.slack_web_hook'))->notify(new MpesaNotification($payload));
    }

    /**
     * @return array
     */
    public function queryStkStatus(): array
    {
        $stk = MpesaStkRequest::whereDoesntHave('response')->get();
        $success = $errors = [];
        foreach ($stk as $item) {
            try {
                $status = mpesa_stk_status($item->id);
                if (isset($status->errorMessage)) {
                    $errors[$item->CheckoutRequestID] = $status->errorMessage;
                    continue;
                }
                $attributes = [
                    'MerchantRequestID' => $status->MerchantRequestID,
                    'CheckoutRequestID' => $status->CheckoutRequestID,
                    'ResultCode' => $status->ResultCode,
                    'ResultDesc' => $status->ResultDesc,
                    'Amount' => $item->amount,
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

    /**
     * @param MpesaStkCallback $stkCallback
     * @param array $response
     *
     * @return MpesaStkCallback
     */
    public function fireStkEvent(MpesaStkCallback $stkCallback, $response): MpesaStkCallback
    {
        if ($stkCallback->ResultCode == 0) {
            event(new StkPushPaymentSuccessEvent($stkCallback, $response));
        } else {
            event(new StkPushPaymentFailedEvent($stkCallback, $response));
        }
        return $stkCallback;
    }
}
