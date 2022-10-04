<?php

namespace App\API\Currency\Http\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
 */
class CurrencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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
            'id' => $this->id,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'rate' => $this->rate,
            'type' => $this->type->render(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
