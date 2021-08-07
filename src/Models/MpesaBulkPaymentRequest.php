<?php

namespace Wmandai\Mpesa\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaBulkPaymentRequest extends Model
{
    protected $guarded = [];

    public function response()
    {
        return $this->hasOne(MpesaBulkPaymentResponse::class, 'conversation_id', 'conversation_id');
    }
}
