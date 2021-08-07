<?php

namespace Wmandai\Mpesa\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaStkCallback extends Model
{
    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(MpesaStkRequest::class, 'checkout_request_id', 'checkout_request_id');
    }
}
