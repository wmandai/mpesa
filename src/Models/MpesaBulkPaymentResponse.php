<?php

namespace Wmandai\Mpesa\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaBulkPaymentResponse extends Model
{
    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(MpesaBulkPaymentRequest::class, 'conversation_id', 'conversation_id');
    }

    public function data()
    {
        return $this->hasOne(MpesaB2cResultParameter::class, 'response_id');
    }
}
