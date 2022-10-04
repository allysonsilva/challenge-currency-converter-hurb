<?php

namespace Support\APIs\ExchangeRate;

use Illuminate\Support\Manager;
use Support\APIs\ExchangeRate\Services\Free;
use Support\APIs\ExchangeRate\Services\Open;
use Support\APIs\ExchangeRate\Redis\Repository as RedisRepository;

class CurrencyManager extends Manager
{
    /**
     * Create an instance of the [Free] service for current and historical
     * foreign exchange rates & crypto currencies rates.
     *
     * @return \Support\APIs\ExchangeRate\Services\Free
     */
    public function createFreeDriver(): Free
    {
        $service = new Free($this->config->get('currency.free.url'));

        if (method_exists($service, 'setRedis')) {
            $service->setRedis(app(RedisRepository::class));
        }

        return $service;
    }

    /**
     * Create an instance of the [Open Exchange Rates] service for current and historical
     * foreign exchange rates & crypto currencies rates.
     *
     * @return \Support\APIs\ExchangeRate\Services\Open
     */
    public function createOpenDriver(): Open
    {
        $service = new Open(
            appId: config('currency.open.token') ?? '',
            baseUrl: config('currency.open.url') ?? '',
        );

        if (method_exists($service, 'setRedis')) {
            $service->setRedis(app(RedisRepository::class));
        }

        return $service;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('currency.driver', 'free');
    }
}
