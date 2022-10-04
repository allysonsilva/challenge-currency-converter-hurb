<?php

namespace Support\APIs\ExchangeRate\Contracts;

interface RedisConstants
{
    /**
     * Name of the key in redis used to store the currency exchange rates.
     */
    public const REDIS_KEY_RATES = 'currency:rates';

    /**
     * Name of the redis key used to block the use of exchange rates.
     */
    public const REDIS_KEY_LOCK = 'currency_lock';

    /**
     * Name of the key in redis used to store the latest exchange rates.
     */
    public const REDIS_KEY_LATEST = 'currency:latest';

    /**
     * Standard Redis connection used to manage currency exchanges.
     */
    public const REDIS_CONNECTION = 'currency';
}
