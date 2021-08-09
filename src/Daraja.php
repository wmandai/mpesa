<?php

namespace Wmandai\Mpesa;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Wmandai\Mpesa\Exceptions\MpesaException;
use Wmandai\Mpesa\Traits\InteractsWithDatabase;
use Wmandai\Mpesa\Traits\MakesHttpRequests;

class Daraja
{
    use MakesHttpRequests;
    use InteractsWithDatabase;

    public $consumerKey;
    public $consumerSecret;
    public $accessToken = '';
    public $shortCode;
    public $businessShortCode;
    public $b2cShortCode;
    public $passKey;
    public $stkCallbackUrl;
    public $initiatorUsername;
    public $initiatorPassword;
    public $timeoutUrl;
    public $resultUrl;
    public $securityCredential;
    public $validationUrl;
    public $confirmationUrl;

    public $pullNominatedNumber;
    public $pullCallbackUrl;
    public $retry = 3;
    /**
     * Initializes the class with an array of API values.
     */

    public function __construct()
    {
        $this->consumerKey = config('mpesa.c2b.consumer_key');
        $this->consumerSecret = config('mpesa.c2b.consumer_secret');
        $this->shortCode = (int) config('mpesa.c2b.shortcode');
        $this->businessShortCode = (int) config('mpesa.business_shortcode');
        $this->b2cShortCode = config('mpesa.b2c.shortcode');
        $this->passKey = config('mpesa.passkey');
        $this->stkCallbackUrl = config('mpesa.stk_callback_url');
        $this->validationUrl = config('mpesa.validation_url');
        $this->confirmationUrl = config('mpesa.confirmation_url');
        $this->initiatorUsername = config('mpesa.initiator_username');
        $this->initiatorPassword = config('mpesa.initiator_password');
        $this->pullNominatedNumber = config('mpesa.pull.nominated_number');
        $this->pullCallbackUrl = config('mpesa.pull.callback');

        $this->timeoutUrl = config('mpesa.timeout_url');
        $this->resultUrl = config('mpesa.result_url');
    }

    public function setCredentials()
    {
        if (config('mpesa.sandbox')) {
            $pubkey = File::get(__DIR__ . '/cert/SandboxCertificate.cer');
        } else {
            $pubkey = File::get(__DIR__ . '/cert/ProductionCertificate.cer');
        }
        openssl_public_encrypt($this->initiatorPassword, $output, $pubkey, OPENSSL_PKCS1_PADDING);

        $this->securityCredential = base64_encode($output);
    }
    /**
     * Get Daraja API Access Token
     *
     * @param boolean $bulk
     */
    public function getAccessToken(bool $bulk = false)
    {
        try {
            $seconds = 60 * 60; // Default 1 hour
            if ($bulk) {
                $this->consumerKey = config('mpesa.b2c.consumer_key');
                $this->consumerSecret = config('mpesa.b2c.consumer_secret');
            }
            return Cache::remember(
                config('mpesa.cache_prefix') . '_access_token',
                $seconds,
                function () {
                    $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
                    $response = $this->get(
                        'https://sandbox.safaricom.co.ke/oauth/v1/generate',
                        ['grant_type' => 'client_credentials'],
                        [
                            "Authorization" => 'Basic ' . $credentials,
                            "Content-Type" => 'application/json',
                        ]
                    );
                    if (isset($response->errorMessage)) {
                        throw new MpesaException($response->errorMessage);
                    }
                    $this->accessToken = $response->access_token;
                    return $this->accessToken;
                }
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function register()
    {
        return $this->send(
            Endpoints::build('register'),
            [
                'ShortCode' => $this->shortCode,
                'ResponseType' => 'Completed',
                'ConfirmationURL' => $this->confirmationUrl,
                'ValidationURL' => $this->validationUrl,
            ]
        );
    }
    public function registerPull()
    {
        return $this->send(
            Endpoints::build('register_pull'),
            [
                'ShortCode' => $this->shortCode,
                'RequestType' => 'Pull',
                'NominatedNumber' => $this->pullNominatedNumber,
                'CallbackURL' => $this->pullCallbackUrl,
            ]
        );
    }

    /**
     * To make a pull of the missed transactions.
     * NB: This API pulls transactions for a period not exceeding 48hrs.
     */
    public function pullTransactions($startDate, $endDate, $offSet = 0)
    {
        $body = [
            'ShortCode' => $this->shortCode,
            'StartDate' => $startDate, // Format 2019-07-31 20:35:21 / 2019-07-31 19:00
            'EndDate' => $endDate, // Format 2019-07-31 20:35:21 / 2019-07-31 22:35
            'OffSetValue' => $offSet
        ];
        return $this->send(Endpoints::build('pull_transactions'), $body);
    }

    /**
     * This method is used to send money to the clients Mpesa account.
     */
    public function b2c(string $phoneNumber, $amount, string $commandId = 'BusinessPayment', string $remarks = '')
    {
        Cache::forget(config('mpesa.cache_prefix') . '_access_token');
        $this->getAccessToken(true);
        $this->setCredentials();
        $body = [
            'InitiatorName' => $this->initiatorUsername,
            'SecurityCredential' => $this->securityCredential,
            'CommandID' => $commandId, // SalaryPayment,BusinessPayment,PromotionPayment
            'Amount' => $amount,
            'PartyA' => $this->b2cShortCode,
            'PartyB' => correctPhoneNumber($phoneNumber),
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $this->timeoutUrl . '/b2c',
            'ResultURL' => $this->resultUrl . '/b2c',
            'Occasion' => '',
        ];
        $response = $this->send(Endpoints::build('b2c'), $body);
        if ($response->ResponseCode == 0) {
            return $this->saveB2cRequest($body, $response);
        } else {
            if ($this->retry > 0) {
                $this->retry--;
                return $this->b2c($amount, $phoneNumber, $commandId, $remarks);
            }
        }
    }

    /**
     * This method is used to send money to other business Mpesa paybills.
     * Unfortunately, this is currently unavailable
     * @deprecated deprecated since version Daraja 2.0
     */
    public function b2b(
        $amount,
        $receiverCode,
        string $command = 'BusinessToBusinessTransfer',
        string $ref = 'Transfer',
        string $remarks = ''
    ) {
        $this->setCredentials();
        return $this->send(
            Endpoints::build('b2b'),
            [
                'Initiator' => $this->initiatorUsername,
                'SecurityCredential' => $this->securityCredential,
                'CommandID' => $command,
                'SenderIdentifierType' => 'Shortcode',
                'RecieverIdentifierType' => 'Shortcode',
                'Amount' => $amount,
                'PartyA' => $this->shortCode,
                'PartyB' => $receiverCode,
                'AccountReference' => $ref,
                'Remarks' => $remarks,
                'QueueTimeOutURL' => $this->timeoutUrl . '/b2b',
                'ResultURL' => $this->resultUrl . '/b2b',
            ]
        );
    }

    /**
     * Use this to simulate a C2B Transaction
     * for testing your ConfirmURL and ValidationURL in C2B
     */
    public function simulate(string $phoneNumber, $amount, string $ref, string $type = 'paybill')
    {
        if (!config('mpesa.sandbox', true)) {
            throw new MpesaException('You cannot simulate a transaction while in Production Mode');
        }
        if ($type == 'till') {
            $this->shortCode = $this->businessShortCode;
        }
        $body = [
            'ShortCode' => $this->shortCode,
            'CommandID' => ($type == 'paybill') ? 'CustomerPayBillOnline' : 'CustomerBuyGoodsOnline',
            'Amount' => $amount,
            'Msisdn' => correctPhoneNumber($phoneNumber),
            'BillRefNumber' => $ref,
        ];
        return $this->send(Endpoints::build('simulate'), $body);
    }

    /**
     * Check Business Code balance
     */
    public function balance(int $identifier = 4, string $remarks = 'Balance Check')
    {
        $this->setCredentials();
        return $this->send(
            Endpoints::build('balance'),
            [
                'CommandID' => 'AccountBalance',
                'PartyA' => $this->shortCode,
                'IdentifierType' => $identifier,
                'Remarks' => $remarks,
                'Initiator' => $this->initiatorUsername,
                'SecurityCredential' => $this->securityCredential,
                'QueueTimeOutURL' => $this->timeoutUrl . '/balance',
                'ResultURL' => $this->resultUrl . '/balance',
            ]
        );
    }

    /**
     * This method is used to check a transaction status
     */
    public function status(string $transactionId, int $identifier = 4, string $remarks = '')
    {
        $this->setCredentials();
        $body = [
            'CommandID' => 'TransactionStatusQuery',
            'PartyA' => $this->shortCode,
            'IdentifierType' => $identifier,
            'Remarks' => $remarks,
            'Initiator' => $this->initiatorUsername,
            'SecurityCredential' => $this->securityCredential,
            'QueueTimeOutURL' => $this->timeoutUrl . '/status',
            'ResultURL' => $this->resultUrl . '/status',
            'TransactionID' => $transactionId,
            'Occassion' => '',
        ];
        return $this->send(Endpoints::build('status'), $body);
    }

    public function stkPush(string $phoneNumber, $amount, string $ref = null, string $type = 'paybill', $desc = null)
    {
        if (!is_numeric($amount) || $amount < 1 || !is_numeric($phoneNumber)) {
            throw new \Exception(
                "Amount should be greater than 1 and phone number should be in the format 254xxxxxxxx"
            );
        }
        if ($type == 'till') {
            $this->shortCode = $this->businessShortCode;
        }
        $timestamp = date('YmdHis');
        $passwd = base64_encode($this->shortCode . $this->passKey . $timestamp);
        $body = [
            'BusinessShortCode' => $this->shortCode,
            'Password' => $passwd,
            'Timestamp' => $timestamp,
            'TransactionType' => ($type == 'paybill') ? 'CustomerPayBillOnline' : 'CustomerBuyGoodsOnline',
            'Amount' => $amount,
            'PartyA' => correctPhoneNumber($phoneNumber),
            'PartyB' => $this->shortCode,
            'PhoneNumber' => correctPhoneNumber($phoneNumber),
            'CallBackURL' => $this->stkCallbackUrl,
            'AccountReference' => is_null($ref) ? correctPhoneNumber($phoneNumber) : $ref,
            'TransactionDesc' => is_null($desc) ? randomMpesaNumber() : $desc,
        ];
        dd($body);
        $response = $this->send(Endpoints::build('stk_push'), $body);
        dd($response);
        if ($response->ResponseCode == 0) {
            // STK Push sent successfully
            return $this->saveStkRequest($body, (array) $response);
        }
        throw new MpesaException($response->ResponseDescription);
    }

    public function stkQuery(string $checkoutRequestID)
    {
        $timestamp = date('YmdHis');
        $this->shortCode = $this->businessShortCode;
        $passwd = base64_encode($this->shortCode . $this->passKey . $timestamp);
        if (!strlen($checkoutRequestID)) {
            throw new \Exception("Checkout Request ID cannot be null");
        }
        $body = [
            'BusinessShortCode' => $this->shortCode,
            'Password' => $passwd,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestID,
        ];
        return $this->send(Endpoints::build('stk_status'), $body);
    }

    /**
     * This method is used to reverse a transaction
     */

    public function reverse(string $transactionId, $amount, int $identifier = 4)
    {
        $this->setCredentials();
        return $this->send(
            Endpoints::build('reversal'),
            [
                'CommandID' => 'TransactionReversal',
                'ReceiverParty' => $this->shortCode,
                'RecieverIdentifierType' => $identifier, // 1=MSISDN, 2=Till_Number, 4=Shortcode
                'Remarks' => 'REVERSAL',
                'Amount' => $amount,
                'Initiator' => $this->initiatorUsername,
                'SecurityCredential' => $this->securityCredential,
                'QueueTimeOutURL' => $this->timeoutUrl . '/reversal',
                'ResultURL' => $this->resultUrl . '/reversal',
                'TransactionID' => $transactionId,
            ]
        );
    }
    /**
     * Handles submission of all API endpoints queries
     *
     * @param string $url
     * @param array $data
     */
    public function send(string $url, array $data)
    {
        if ($this->accessToken == '') {
            $this->accessToken = $this->getAccessToken();
        }
        if ($this->accessToken != '' || $this->accessToken !== false) {
            return $this->post(
                $url,
                $data,
                [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Accept' => 'application/json',
                ]
            );
        } else {
            return false;
        }
    }
}
