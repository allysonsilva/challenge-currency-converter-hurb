<?php

namespace Support\APIs\ExchangeRate\Services;

use Illuminate\Http\Client\PendingRequest;
use Support\APIs\ExchangeRate\Redis\LuaScripts;
use Support\APIs\ExchangeRate\Contracts\APIClient;
use Support\APIs\ExchangeRate\DTO\ConvertedRateDTO;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Support\APIs\ExchangeRate\DTO\LatestExchangeRatesDTO;
use Support\APIs\ExchangeRate\Exceptions\InvalidSymbolException;

class Open implements APIClient
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

    /**
     * The API key for the Exchange Rates API.
     *
     * @var string
     */
    public readonly string $apiKey;

    public function __construct(string $appId, string $baseUrl)
    {
        $this->apiKey = $appId;
        $this->baseUrl = $baseUrl;

        $this->queryParams['base'] = static::BASE_CURRENCY;
    }

    /**
     * @inheritDoc
     */
    public function withCrypto(): static
    {
        $this->queryParams['show_alternative'] = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function latest(): ?LatestExchangeRatesDTO
    {
        if ($cachedExchangeRate = $this->attemptToResolveFromCache(static::REDIS_KEY_LATEST)) {
            return new LatestExchangeRatesDTO(isCached: true, rates: $cachedExchangeRate);
        }

        /** @var array{base: string, rates: array<string, float>}|null */
        $exchangeRates = $this->makeRequest('latest.json', $this->queryParams);

        if (is_null($exchangeRates)) {
            return null;
        }

        $exchangeRatesDTO = new LatestExchangeRatesDTO(
            base: $exchangeRates['base'],
            rates: $exchangeRates['rates']
        );

        if ($this->shouldCache) {
            $this->cache->storeHashInCache(static::REDIS_KEY_LATEST, $exchangeRatesDTO->toArray());
        }

        return $exchangeRatesDTO;
    }

    /**
     * @inheritDoc
     */
    public function convert(string $from, string $to, float $amount): ConvertedRateDTO
    {
        [$fromRate, $toRate] = $this->getRatesFromCache($from, $to);

        $toFromRate = bcdiv(strval($toRate), strval($fromRate), static::DIGITS_AFTER_DECIMAL_PLACE);

        $convertedValue = bcmul($toFromRate, strval($amount), static::DIGITS_AFTER_DECIMAL_PLACE);

        return new ConvertedRateDTO(
            valueFromRate: floatval($fromRate),
            valueToRate: floatval($toRate),
            convertedValue: floatval($convertedValue),
            fromSymbol: $from,
            toSymbol: $to,
            baseCurrency: static::BASE_CURRENCY
        );
    }

    /**
     * @inheritDoc
     */
    protected function httpClient(): PendingRequest
    {
        // Adding authentication required to use the API
        return $this->client->withToken($this->apiKey, 'Token');
    }

    /**
     * Retrieve currency exchange rates.
     *
     * @param string $from
     * @param string $to
     *
     * @return array{float,float}
     *
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     * @throws \Support\APIs\ExchangeRate\Exceptions\InvalidSymbolException
     */
    private function getRatesFromCache(string $from, string $to): array
    {
        // @phpstan-ignore-next-line
        $ratesFn = fn () => $this->cache->runLuaScript(
            LuaScripts::getRatesWithoutLock(),
            [static::REDIS_KEY_LOCK, static::REDIS_KEY_RATES],
            [$from, $to]
        );

        // The number of milliseconds to wait before retrying to
        // verify that the lock key no longer exists.
        $sleepMilliseconds = 250;

        // Number of seconds to try to perform the operation.
        $seconds = 2;

        $starting = now()->getTimestamp();

        while (! $jsonResult = $ratesFn()) {
            usleep($sleepMilliseconds * 1000);

            if ((now()->getTimestamp() - $seconds) >= $starting) {
                throw new LockTimeoutException();
            }
        }

        /** @var array<string, float> */
        $rates = json_decode($jsonResult, true);

        if (empty($rates[$from] ?? null)) {
            throw new InvalidSymbolException($from);
        }

        if (empty($rates[$to] ?? null)) {
            throw new InvalidSymbolException($to);
        }

        $fromRate = $rates[$from];
        $toRate = $rates[$to];

        return [$fromRate, $toRate];
    }
}
