<?php

namespace Support\APIs\ExchangeRate\DTO\Casters;

use Illuminate\Support\Arr;
use Spatie\DataTransferObject\Caster;
use Support\APIs\ExchangeRate\DTO\RateDTO;
use Support\APIs\ExchangeRate\DTO\Collections\CollectionOfCurrency;

class RateCollectionCaster implements Caster
{
    public function cast(mixed $rates): CollectionOfCurrency
    {
        $ratesDTO = Arr::map($rates, fn ($value, $symbol) => new RateDTO(symbol: $symbol, exchangeRate: $value));

        return new CollectionOfCurrency($ratesDTO);
    }
}
