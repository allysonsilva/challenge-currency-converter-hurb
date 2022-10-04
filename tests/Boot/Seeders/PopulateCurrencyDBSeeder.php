<?php

namespace Tests\Boot\Seeders;

use Illuminate\Database\Seeder;
use Support\APIs\ExchangeRate\Contracts\RedisConstants;
use Support\APIs\ExchangeRate\Redis\Repository as RedisRepository;

class PopulateCurrencyDBSeeder extends Seeder
{
    public function __construct(private RedisRepository $currencyRepository)
    {
    }

    /**
     * Run the database seeders.
     *
     * @example php artisan db:seed --class="\\Tests\\Boot\\Seeders\\PopulateCurrencyDBSeeder"
     *
     * @return void
     */
    public function run()
    {
        $file = 'currency-exchange.json';
        $dbContent = file_get_contents(realpath(__DIR__ . "/../DBs/{$file}"));
        $rates = json_decode($dbContent, true)['rates'];

        $this->currencyRepository->forget(RedisConstants::REDIS_KEY_RATES);
        $this->currencyRepository->storeHashInCache(RedisConstants::REDIS_KEY_RATES, $rates);

        $this->command->newLine();
        $this->command->info("Dump [{$file}] executado com sucesso!");
        $this->command->newLine();
    }
}
