<?php

use Wmandai\Mpesa\Facades\Mpesa;
use Illuminate\Support\Str;

if (!function_exists('mpesaRegister')) {
    function mpesaRegister()
    {
        return Mpesa::register();
    }
}
if (!function_exists('mpesaBalance')) {
    function mpesaBalance($identifier = 4, $remarks = 'Balance Check')
    {
        return Mpesa::balance($identifier, $remarks);
    }
}
if (!function_exists('mpesaSend')) {
    // commandId can be SalaryPayment,BusinessPayment,PromotionPayment
    function mpesaSend($phone, $amount, $commandId = 'BusinessPayment', $remarks = null)
    {
        return Mpesa::b2c($phone, $amount, $commandId, $remarks);
    }
}
if (!function_exists('mpesaStkStatus')) {
    function mpesaStkStatus($id)
    {
        return Mpesa::stkQuery($id);
    }
}
if (!function_exists('mpesaTransactionStatus')) {
    // $identifier could be 1=msisdn,2=till,4=paybill
    function mpesaTransactionStatus($transactionId, $identifier = 4, $remarks = '')
    {
        return Mpesa::status($transactionId, $identifier, $remarks);
    }
}
if (!function_exists('mpesaRequest')) {
    // $type could be till or paybill
    function mpesaRequest($phone, $amount, $reference = null, $type = 'paybill', $description = null)
    {
        return Mpesa::stkPush($phone, $amount, $reference, $type, $description);
    }
}
if (!function_exists('mpesaSimulate')) {
    // $type could be till or paybill
    function mpesaSimulate($phone, $amount, $ref = 'INVOICEID', $type = 'paybill')
    {
        return Mpesa::simulate($phone, $amount, $ref, $type);
    }
}
if (!function_exists('mpesaReversal')) {
    // $identifier 1=MSISDN, 2=Till_Number, 4=Shortcode
    function mpesaReversal($transactionId, $amount, $identifier = 4)
    {
        return Mpesa::reverse($transactionId, $amount, $identifier);
    }
}
if (!function_exists('correctPhoneNumber')) {
    /**
     * @param string $number
     * @param bool   $strip_plus
     *
     * @return string
     */
    function correctPhoneNumber($number, $strip_plus = true): string
    {
        $number = preg_replace('/\s+/', '', $number);
        $replace = static function ($needle, $replacement) use (&$number) {
            if (Str::startsWith($number, $needle)) {
                $pos = strpos($number, $needle);
                $length = strlen($needle);
                $number = substr_replace($number, $replacement, $pos, $length);
            }
        };
        $replace('2547', '+2547');
        $replace('07', '+2547');
        if ($strip_plus) {
            $replace('+254', '254');
        }
        return $number;
    }
}
if (!function_exists('randomMpesaNumber')) {
    /**
     * Generate a random transaction number
     *
     * @return string
     */
    function randomMpesaNumber(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 15; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return strtoupper($randomString);
    }
}
