<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInputsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'symbol1' => 'required',
            'one' => 'required',
            'oneUSD' => 'required',
            'symbol2' => 'required',
            'two' => 'required',
            'twoUSD' => 'required',
        ];
    }
}
