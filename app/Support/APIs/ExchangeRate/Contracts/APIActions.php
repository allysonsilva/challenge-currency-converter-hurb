<?php

namespace Support\APIs\ExchangeRate\Contracts;

use Support\APIs\ExchangeRate\DTO\ConvertedRateDTO;
use Support\APIs\ExchangeRate\DTO\LatestExchangeRatesDTO;

interface APIActions
{
    /**
     * Get the latest foreign exchange reference rates.
     * Latest endpoint will return exchange rate data updated on daily basis.
     *
     * @return \Support\APIs\ExchangeRate\DTO\LatestExchangeRatesDTO|null
     */
    public function latest(): ?LatestExchangeRatesDTO;

    /**
     * Return the converted values between the $from and $to parameters.
     *
     * @param string $from
     * @param string $to
     * @param float $amount
     *
     * @return \Support\APIs\ExchangeRate\DTO\ConvertedRateDTO
     */
    public function convert(string $from, string $to, float $amount): ConvertedRateDTO;
}
