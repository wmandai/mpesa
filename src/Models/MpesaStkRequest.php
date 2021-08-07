<?php

namespace Wmandai\Mpesa\Models;

use Illuminate\Database\Eloquent\Model;
use Wmandai\Mpesa\Models\MpesaStkCallback;

/**
 * Model to save STK Push requests to database
 */
class MpesaStkRequest extends Model
{
    protected $guarded = [];

    public function response()
    {
        return $this->hasOne(MpesaStkCallback::class, 'checkout_request_id', 'checkout_request_id');
    }
}
