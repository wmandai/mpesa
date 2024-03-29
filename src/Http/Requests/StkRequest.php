<?php

namespace Wmandai\Mpesa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StkRequest
 *
 * @package Wmandai\Mpesa\Http\Requests
 */
class StkRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount' => 'required|numeric',
            'phone' => 'required',
            'reference' => 'required',
            'description' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }
}
