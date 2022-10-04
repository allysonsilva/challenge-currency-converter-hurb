<?php

namespace Support\APIs\ExchangeRate\DTO\Collections;

use Illuminate\Support\Collection;
use Support\APIs\ExchangeRate\DTO\RateDTO;

class CollectionOfCurrency extends Collection
{
    /**
     * Convert the Collection instance to an array.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        /** @var \Support\APIs\ExchangeRate\DTO\RateDTO $rateDTO */
        return $this->mapWithKeys(function ($rateDTO) {
            return $rateDTO->toArray();
        })->all();
    }

    /**
     * Get an item at a given offset.
     *
     * @param string $key
     *
     * @return \Support\APIs\ExchangeRate\DTO\RateDTO
     */
    public function offsetGet($key): RateDTO // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    {
        return parent::offsetGet($key);
    }
}
