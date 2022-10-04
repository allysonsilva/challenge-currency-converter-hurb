<?php

namespace CurrencyDomain\Actions;

use Support\Contracts\ShouldActionInterface;
use CurrencyDomain\DTO\ConvertedCurrencyResultDTO;
use CurrencyDomain\ValueObjects\ValueToBeConverted;
use Support\APIs\ExchangeRate\Facades\ExchangeRate;
use CurrencyDomain\ValueObjects\ValueTargetCurrency;

class CurrencyExchangeConvert implements ShouldActionInterface
{
    /**
     * Execute the Action.
     *
     * @param string $from
     * @param string $to
     * @param float $amount
     *
     * @return \CurrencyDomain\DTO\ConvertedCurrencyResultDTO
     */
    public function execute(string $from, string $to, float $amount): ConvertedCurrencyResultDTO
    {
        /** @var \Support\APIs\ExchangeRate\DTO\ConvertedRateDTO */
        $convertedDTO = ExchangeRate::convert($from, $to, $amount);

        return new ConvertedCurrencyResultDTO(
            originCurrency: $from,
            targetCurrency: $to,
            baseCurrency: $convertedDTO->baseCurrency,
            valueToBeConverted: new ValueToBeConverted(money($amount, $from), $convertedDTO->valueFromRate),
            convertedToTargetCurrency: new ValueTargetCurrency(money($convertedDTO->convertedValue, $to), $convertedDTO->valueToRate),
        );
    }
}
