<?php

use Wmandai\Mpesa\Facades\B2C;
use Wmandai\Mpesa\Facades\Identity;
use Wmandai\Mpesa\Facades\STK;
use Wmandai\Mpesa\Library\Simulate;

if (!function_exists('mpesa_balance')) {
    /**
     * @method \Wmandai\Mpesa\Library\BulkSender balance()
     * @return mixed
     */
    function mpesa_balance()
    {
        return B2C::balance();
    }
}
if (!function_exists('mpesa_send')) {
    /**
     * @method \Wmandai\Mpesa\Library\BulkSender send()
     * @param string $phone
     * @param int $amount
     * @param $remarks
     * @return mixed
     */
    function mpesa_send($phone, $amount, $remarks = null)
    {
        return B2C::send($phone, $amount, $remarks);
    }
}
if (!function_exists('mpesa_id_check')) {
    /**
     * @method \Wmandai\Mpesa\Library\IdCheck validate()
     * @param string $phone
     * @return mixed
     */
    function mpesa_id_check($phone)
    {
        return Identity::validate($phone);
    }
}
if (!function_exists('mpesa_stk_status')) {
    /**
     * @method \Wmandai\Mpesa\Library\StkPush validate()
     * @param int $id
     * @return mixed
     */
    function mpesa_stk_status($id)
    {
        return STK::validate($id);
    }
}
if (!function_exists('mpesa_request')) {
    /**
     * @method \Wmandai\Mpesa\Library\STKPush push()
     * @param string $phone
     * @param int $amount
     * @param string|null $reference
     * @param string|null $description
     * @return mixed
     */
    function mpesa_request($phone, $amount, $reference = null, $description = null)
    {
        return STK::push($amount, $phone, $reference, $description);
    }
}
if (!function_exists('mpesa_validate')) {
    /**
     * @method \Wmandai\Mpesa\Library\StkPush validate()
     * @param string|int $id
     * @return mixed
     */
    function mpesa_validate($id)
    {
        return STK::validate($id);
    }
}
if (!function_exists('mpesa_simulate')) {
    /**
     * @method \Wmandai\Mpesa\Library\Simulate push()
     * @param int $phone
     * @param string $amount
     * @return mixed
     * @throws \Wmandai\MobileMoney\Exceptions\MpesaException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function mpesa_simulate($phone, $amount)
    {
        return app(Simulate::class)->push($phone, $amount);
    }
}
