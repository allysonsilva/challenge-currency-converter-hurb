<?php

namespace Support\APIs\ExchangeRate\Contracts;

interface APIConstants
{
    /**
     * Enter the three-letter currency code of your preferred base currency.
     */
    public const BASE_CURRENCY = 'USD';

    /**
     * Is used to set the number of digits after the decimal place in the result.
     */
    public const DIGITS_AFTER_DECIMAL_PLACE = 6;
}
