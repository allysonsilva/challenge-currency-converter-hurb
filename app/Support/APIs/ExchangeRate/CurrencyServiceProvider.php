<?php

namespace Support\APIs\ExchangeRate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class CurrencyServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('currency', fn ($app) => new CurrencyManager($app));

        $this->app->singleton('currency.driver', fn ($app) => $app['currency']->driver());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function provides()
    {
        return ['currency', 'currency.driver'];
    }
}
