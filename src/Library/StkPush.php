<?php

namespace Wmandai\Mpesa\Library;

use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Wmandai\Mpesa\Database\Models\MpesaStkRequest;
use Wmandai\Mpesa\Events\StkPushRequestedEvent;
use Wmandai\Mpesa\Exceptions\MpesaException;
use Wmandai\Mpesa\Repositories\Generator;

/**
 * Class StkPush
 *
 * @package Wmandai\Mpesa\Library
 */
class StkPush extends ApiCore
{
    /**
     * @var string
     */
    protected $number;
    /**
     * @var string
     */
    protected $amount;
    /**
     * @var string
     */
    protected $reference;
    /**
     * @var string
     */
    protected $description;

    /**
     * @param string $amount
     *
     * @return $this
     * @throws \Exception
     * @throws MpesaException
     */
    public function request($amount): self
    {
        if (!is_numeric($amount)) {
            throw new MpesaException('The amount must be numeric, got ' . $amount);
        }
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param string $number
     *
     * @return $this
     */
    public function from($number): self
    {
        $this->number = $this->formatPhoneNumber($number);
        return $this;
    }

    /**
     * Set the mpesa reference
     *
     * @param string $reference
     * @param string $description
     *
     * @return $this
     * @throws \Exception
     * @throws MpesaException
     */
    public function usingReference($reference, $description): self
    {
        \preg_match('/[^A-Za-z0-9]/', $reference, $matches);
        if (\count($matches)) {
            throw new MpesaException('Reference should be alphanumeric.');
        }
        $this->reference = $reference;
        $this->description = $description;
        return $this;
    }

    /**
     * Send a payment request
     *
     * @param null|int $amount
     * @param null|string $number
     * @param null|string $reference
     * @param null|string $description
     *
     * @return mixed
     * @throws \Wmandai\Mpesa\Exceptions\MpesaException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function push($amount = null, $number = null, $reference = null, $description = null)
    {
        $time = Carbon::now()->format('YmdHis');
        $shortCode = config('mpesa.c2b.short_code');
        $passkey = config('mpesa.c2b.passkey');
        $callback = config('mpesa.c2b.stk_callback');
        $password = base64_encode($shortCode . $passkey . $time);
        $good_phone = $this->formatPhoneNumber($number ?: $this->number);
        $body = [
            'BusinessShortCode' => $shortCode,
            'Password' => $password,
            'Timestamp' => $time,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount ?: $this->amount,
            'PartyA' => $good_phone,
            'PartyB' => $shortCode,
            'PhoneNumber' => $good_phone,
            'CallBackURL' => $callback,
            'AccountReference' => $reference ?? $this->reference ?? $good_phone,
            'TransactionDesc' => $description ?? $this->description ?? Generator::generateTransactionNumber(),
        ];
        $response = $this->sendRequest($body, 'stk_push');
        return $this->saveStkRequest($body, (array) $response);
    }

    /**
     * @param array $body
     * @param array $response
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     * @throws MpesaException
     */
    public function saveStkRequest($body, $response)
    {
        if ($response['ResponseCode'] == 0) {
            $incoming = [
                'phone' => $body['PartyA'],
                'amount' => $body['Amount'],
                'reference' => $body['AccountReference'],
                'description' => $body['TransactionDesc'],
                'CheckoutRequestID' => $response['CheckoutRequestID'],
                'MerchantRequestID' => $response['MerchantRequestID'],
                'user_id' => @(Auth::id() ?: request('user_id')),
            ];
            $stk = MpesaStkRequest::create($incoming);
            event(new StkPushRequestedEvent($stk, request()));
            return $stk;
        }
        throw new MpesaException($response['ResponseDescription']);
    }

    /**
     * Validate an initialized transaction.
     *
     * @param string|int $checkoutRequestID
     *
     * @return mixed
     * @throws MpesaException
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function validate($checkoutRequestID)
    {
        if ((int) $checkoutRequestID) {
            $checkoutRequestID = MpesaStkRequest::find($checkoutRequestID)->CheckoutRequestID;
        }
        $time = Carbon::now()->format('YmdHis');
        $shortCode = \config('mpesa.c2b.short_code');
        $passkey = \config('mpesa.c2b.passkey');
        $password = \base64_encode($shortCode . $passkey . $time);
        $body = [
            'BusinessShortCode' => $shortCode,
            'Password' => $password,
            'Timestamp' => $time,
            'CheckoutRequestID' => $checkoutRequestID,
        ];
        try {
            return $this->sendRequest($body, 'stk_status');
        } catch (RequestException $exception) {
            throw new MpesaException($exception->getMessage());
        }
    }
}
