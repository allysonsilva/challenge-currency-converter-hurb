<?php

declare(strict_types=1);

namespace Support\APIs\ExchangeRate\DTO;

use Support\DTO\BaseDto;
use Spatie\DataTransferObject\Attributes\MapTo;
use Spatie\DataTransferObject\Attributes\Strict;

#[Strict]
final class ConvertedRateDTO extends BaseDto
{
    #[MapTo('value_from_rate')]
    public readonly float|null $valueFromRate;

    #[MapTo('value_to_rate')]
    public readonly float|null $valueToRate;

    #[MapTo('converted_currency')]
    public readonly float $convertedValue;

    #[MapTo('from_symbol')]
    public readonly string $fromSymbol;

    #[MapTo('to_symbol')]
    public readonly string $toSymbol;

    #[MapTo('base_currency')]
    public readonly string $baseCurrency;
}
