<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Rate Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "free", "open"
    |
    */

    'driver' => env('EXCHANGE_RATE_DRIVER', 'free'),

    /*
    |--------------------------------------------------------------------------
    | Free Options
    |--------------------------------------------------------------------------
    |
    */

    'free' => [
        'url' => env('EXCHANGERATE_HOST_API_URL', 'https://api.exchangerate.host/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Open Exchange Rates Options
    |--------------------------------------------------------------------------
    |
    */

    'open' => [
        'url' => env('EXCHANGE_RATES_OPEN_API_URL', 'https://openexchangerates.org/api/'),
        'token' => env('EXCHANGE_RATES_OPEN_API_KEY'),
    ],

];
