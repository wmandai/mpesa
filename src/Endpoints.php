<?php

namespace Wmandai\Mpesa;

use Wmandai\Mpesa\Exceptions\MpesaException;

class Endpoints
{
    public static function build($endpoint)
    {
        return self::getEndpoint($endpoint);
    }

    public static function getEndpoint($section): string
    {
        $endpoints = [
            'auth' => 'oauth/v1/generate?grant_type=client_credentials',
            'id_check' => 'mpesa/checkidentity/v1/query',
            'register' => 'mpesa/c2b/v1/registerurl',
            'stk_push' => 'mpesa/stkpush/v1/processrequest',
            'stk_status' => 'mpesa/stkpushquery/v1/query',
            'b2c' => 'mpesa/b2c/v1/paymentrequest',
            'status' => 'mpesa/transactionstatus/v1/query',
            'balance' => 'mpesa/accountbalance/v1/query',
            'b2b' => 'mpesa/b2b/v1/paymentrequest',
            'simulate' => 'mpesa/c2b/v1/simulate',
            'reversal' => 'mpesa/reversal/v1/request',
            'register_pull' => 'pulltransactions/v1/register',
            'pull_transactions' => 'pulltransactions/v1/query'
        ];
        if (in_array($section, $endpoints)) {
            return self::getUrl($section);
        }
        throw new MpesaException('Unknown endpoint');
    }

    public static function getUrl($suffix): string
    {
        $baseEndpoint = 'https://api.safaricom.co.ke/';
        if (config('mpesa.sandbox')) {
            $baseEndpoint = 'https://sandbox.safaricom.co.ke/';
        }
        return $baseEndpoint . $suffix;
    }
}
