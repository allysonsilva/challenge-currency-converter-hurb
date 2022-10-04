<?php

namespace Domain;

use Illuminate\Support\AggregateServiceProvider;
use CurrencyDomain\Providers\CurrencyDomainServiceProvider;

/**
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
 */
final class DomainServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected $providers = [
        CurrencyDomainServiceProvider::class,
    ];
}
