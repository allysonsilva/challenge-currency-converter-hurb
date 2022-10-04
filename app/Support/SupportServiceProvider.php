<?php

namespace Support;

use Support\APIs\APIsServiceProvider;
use Illuminate\Support\AggregateServiceProvider;

final class SupportServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected $providers = [ // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
        APIsServiceProvider::class,
    ];
}
