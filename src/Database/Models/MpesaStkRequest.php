<?php

namespace Wmandai\Mpesa\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Wmandai\Mpesa\Database\Models\MpesaStkCallback;

/**
 * Model to save STK Push requests to database
 */
class MpesaStkRequest extends Model
{
    protected $guarded = [];

    public function response()
    {
        return $this->hasOne(MpesaStkCallback::class, 'CheckoutRequestID', 'CheckoutRequestID');
    }
}
