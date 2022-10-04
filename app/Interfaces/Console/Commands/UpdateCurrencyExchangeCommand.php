<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Support\Management\RedisLock;
use CurrencyDomain\Models\Currency;
use Support\APIs\ExchangeRate\Redis\LuaScripts;
use Support\APIs\ExchangeRate\Facades\ExchangeRate;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Support\APIs\ExchangeRate\Contracts\RedisConstants;

/**
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint
 */
class UpdateCurrencyExchangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency-exchange:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates exchange currencies with the latest version in the external API.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param \Support\Management\RedisLock $redisLock
     * @param \CurrencyDomain\Models\Currency $currency
     *
     * @return int
     *
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     */
    public function handle(RedisLock $redisLock, Currency $currency): int
    {
        /** @var \Illuminate\Cache\PhpRedisLock */
        $lock = $redisLock->lock(RedisConstants::REDIS_KEY_LOCK, 5);

        try {
            // Lock acquired after waiting a maximum of 5 seconds...
            $lock->block(2);

            /** @var \Support\APIs\ExchangeRate\DTO\LatestExchangeRatesDTO */
            $latestExchangeRatesDTO = ExchangeRate::symbols(...$currency->foreignExchangeCurrencies())
                                                //   ->withCrypto()
                                                  ->whenThrowException()
                                                  ->shouldBustCache()
                                                  ->latest();

            $exchangeRates = json_encode(array_merge($currency->manualExchangeCurrencies(), $latestExchangeRatesDTO->toArray()));

            $redisLock->runLuaScript(LuaScripts::updateExchangeRates(), [RedisConstants::REDIS_KEY_RATES], [$exchangeRates]);
        } catch (LockTimeoutException $e) {
            throw $e;
            // Unable to acquire lock...
        } finally {
            $lock->release();
        }

        $this->info('Currency exchange successfully updated ✅');

        return 0;
    }
}
