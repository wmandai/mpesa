<?php

namespace Wmandai\Mpesa\Database\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Wmandai\Mpesa\Database\Models\MpesaBulkPaymentResponse
 *
 * @property int $id
 * @property int $ResultType
 * @property int $ResultCode
 * @property string $ResultDesc
 * @property string $OriginatorConversationID
 * @property string $ConversationID
 * @property string $TransactionID
 * @property string|null $ResultParameters
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Wmandai\Mpesa\Database\Models\MpesaBulkPaymentRequest $request
 * @mixin \Eloquent
 */
class MpesaBulkPaymentResponse extends Model
{
    protected $guarded = [];

    public function request()
    {
        return $this->belongsTo(MpesaBulkPaymentRequest::class, 'ConversationID', 'conversation_id');
    }

    public function data()
    {
        return $this->hasOne(MpesaB2cResultParameter::class, 'response_id');
    }
}
