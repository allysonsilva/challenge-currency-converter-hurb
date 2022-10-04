<?php

namespace App\API\Currency\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvertMoneyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $symbolRules = [
            'required',
            'size:3',
            'regex:/^[A-Z]{3}$/m',
        ];

        return [
            'from' => $symbolRules,
            'to' => $symbolRules,
            'amount' => [
                'required',
                'regex:/^\d+\.\d{2,6}$/',
            ],
        ];
    }
}
