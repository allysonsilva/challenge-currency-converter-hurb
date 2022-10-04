<?php

namespace Support\APIs\ExchangeRate\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Support\APIs\ExchangeRate\CurrencyManager
 */
class ExchangeRate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'currency.driver';
    }
}
