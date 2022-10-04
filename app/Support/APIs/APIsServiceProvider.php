<?php

namespace Support\APIs;

use Illuminate\Support\AggregateServiceProvider;
use Support\APIs\ExchangeRate\CurrencyServiceProvider;

final class APIsServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected $providers = [ // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
        CurrencyServiceProvider::class,
    ];
}
