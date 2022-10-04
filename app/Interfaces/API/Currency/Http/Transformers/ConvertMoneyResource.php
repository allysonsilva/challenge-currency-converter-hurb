<?php

namespace App\API\Currency\Http\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
 */
class ConvertMoneyResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'currency';

    /**
     * Transform the resource into an array.
     *
     * @SuppressWarnings("UnusedFormalParameter")
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array<mixed>
     */
    public function toArray($request) // phpcs:ignore
    {
        return [
            'currency' => [
                'from' => $this->fromSymbol(),
                'to' => $this->toSymbol(),
                'values' => [
                    'to_convert' => $this->getValueToBeConverted(),
                    'converted' => $this->getValueOfConverted(),
                ],
            ],
        ];
    }
}
