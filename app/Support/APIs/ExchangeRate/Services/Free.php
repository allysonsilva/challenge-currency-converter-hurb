<?php

namespace Support\APIs\ExchangeRate\Services;

use Support\APIs\ExchangeRate\Contracts\APIClient;
use Support\APIs\ExchangeRate\DTO\ConvertedRateDTO;
use Support\APIs\ExchangeRate\DTO\LatestExchangeRatesDTO;

class Free implements APIClient
{
    use Concerns\HandleCache;
    use Concerns\HandleRequest;
    use Concerns\CommonQueryParams;

    /**
     * The base URL for the Exchange Rates API.
     *
     * @var string
     */
    public readonly string $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;

        $this->base(static::BASE_CURRENCY);
    }

    /**
     * @inheritDoc
     */
    public function withCrypto(): static
    {
        $this->queryParams['source'] = 'crypto';

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function latest(): ?LatestExchangeRatesDTO
    {
        /** @var array{base: string, rates: array<string, float>}|null */
        $exchangeRates = $this->makeRequest('latest', $this->queryParams);

        if (empty($exchangeRates)) {
            return null;
        }

        return new LatestExchangeRatesDTO(
            base: $exchangeRates['base'],
            rates: $exchangeRates['rates']
        );
    }

    /**
     * @inheritDoc
     */
    public function convert(string $from, string $to, float $amount): ConvertedRateDTO
    {
        /** @var array<mixed> */
        $convertedValue = $this->makeRequest('convert', array_merge($this->queryParams, [
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
        ]));

        return new ConvertedRateDTO(
            convertedValue: floatval($convertedValue['result']),
            fromSymbol: $from,
            toSymbol: $to,
            baseCurrency: static::BASE_CURRENCY
        );
    }
}
