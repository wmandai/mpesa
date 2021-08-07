<?php

namespace Wmandai\Mpesa\Models;

use Illuminate\Database\Eloquent\Model;

class MpesaC2bCallback extends Model
{
    protected $guarded = [];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }
}
