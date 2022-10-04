<?php

namespace App\API\Currency\Http\Requests;

use Illuminate\Validation\Rule;
use CurrencyDomain\Models\Currency;
use Illuminate\Validation\Rules\Enum;
use CurrencyDomain\Enums\CurrencyType;
use Illuminate\Foundation\Http\FormRequest;

class CurrencyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var int|null */
        $currencyKey = $this->route('currency');

        return [
            'name' => 'required',
            'symbol' => [
                'required',
                'size:3',
                Rule::unique(Currency::getTableName())->ignore($currencyKey),
            ],
            'rate' => ['required', 'regex:/^\d+\.\d{2,6}$/'],
            'type' => ['required', new Enum(CurrencyType::class)],
        ];
    }
}
