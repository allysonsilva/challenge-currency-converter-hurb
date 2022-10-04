<?php

namespace App\API\Currency\Http\Transformers;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
 */
class CurrencyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @SuppressWarnings("UnusedFormalParameter")
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array<mixed>
     */
    public function toArray($request) // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        return [
            'data' => CurrencyResource::collection($this->collection),
        ];
    }
}
