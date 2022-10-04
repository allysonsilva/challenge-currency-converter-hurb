<?php

namespace App\API\Currency\Http\Routes;

use Illuminate\Routing\Router;
use Support\Http\Routing\RouteFile;
use Illuminate\Support\Facades\Route;
use App\API\Currency\Http\Controllers\ConvertMoney;
use App\API\Currency\Http\Controllers\CurrencyController;

/**
 * @codeCoverageIgnore
 */
class Api extends RouteFile
{
    protected function routes(Router $router): void
    {
        $router->prefix('currencies')->group(function () {
            Route::get('convert', ConvertMoney::class)->name('currencies.convert');
        });

        Route::apiResource('currencies', CurrencyController::class);
    }
}
