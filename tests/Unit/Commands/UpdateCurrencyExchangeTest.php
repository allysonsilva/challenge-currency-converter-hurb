<?php

namespace Tests\Unit\Commands;

use Tests\TestCase;
use Support\Management\RedisLock;
use Illuminate\Cache\PhpRedisLock;
use CurrencyDomain\Models\Currency;
use Support\APIs\ExchangeRate\Facades\ExchangeRate;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Support\APIs\ExchangeRate\Contracts\RedisConstants;
use Support\APIs\ExchangeRate\DTO\LatestExchangeRatesDTO;
use Support\APIs\ExchangeRate\Redis\Repository as RedisRepository;

/**
 * @group Console
 *
 * @testdox UpdateCurrencyExchangeTest - (Tests\Unit\Commands\UpdateCurrencyExchangeTest)
 */
class UpdateCurrencyExchangeTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @testdox Testando o comando do artisan "currency-exchange:update"
     *
     * @test
     */
    public function testConsoleCommand(): void
    {
        $symbols = [
            ['name' => 'Brazilian Real', 'code' => 'BRL'],
            ['name' => 'United States Dollar', 'code' => 'USD'],
            ['name' => 'Euro', 'code' => 'EUR'],
            ['name' => 'British Pound Sterling', 'code' => 'GBP'],
            ['name' => 'Australian Dollar', 'code' => 'AUD'],
        ];

        foreach ($symbols as $symbol) {
            Currency::factory()
                    ->name($symbol['name'])
                    ->symbol($symbol['code'])
                    ->typeFiat()
                    ->create();
        }

        foreach ([['AAA', 2.0], ['BBB', 3.0], ['CCC', 4.0], ['DDD', 5.0]] as $symbol) {
            Currency::factory()
                    ->symbol($symbol[0])
                    ->rate($symbol[1])
                    ->create();
        }

        $exchangeRatesDTO = new LatestExchangeRatesDTO(
            base: 'USD',
            rates: [
                'BRL' => '6.000000',
                'USD' => '1.000000',
                'EUR' => '7.000000',
                'GBP' => '8.000000',
                'AUD' => '9.000000',
            ]
        );

        ExchangeRate::partialMock()
                    ->shouldReceive('symbols')
                    ->with(...['BRL', 'USD', 'EUR', 'GBP', 'AUD'])
                    ->andReturnSelf()
                    ->shouldReceive('latest')
                    ->withNoArgs()
                    ->andReturn($exchangeRatesDTO);

        $this->artisan('currency-exchange:update')->assertExitCode(0);

        $data = app(RedisRepository::class)->getFromCache(RedisConstants::REDIS_KEY_RATES);

        self::assertEqualsCanonicalizing($data, [
            'USD' => '1.000000',
            'AAA' => '2.000000',
            'BBB' => '3.000000',
            'CCC' => '4.000000',
            'DDD' => '5.000000',
            'BRL' => '6.000000',
            'EUR' => '7.000000',
            'GBP' => '8.000000',
            'AUD' => '9.000000',
        ]);
    }

    /**
     * @testdox Lançar exceção "LockTimeoutException" quando o lock não puder ser adquirido
     *
     * @test
     */
    public function throwExceptionWhenLockCannotBeAcquired(): void
    {
        $phpRedisLockMock = $this->mock(PhpRedisLock::class);
        $phpRedisLockMock->shouldReceive('block')->andThrow(new LockTimeoutException());
        $phpRedisLockMock->shouldReceive('release')->andReturnNull();

        $this->partialMock(RedisLock::class)
             ->shouldReceive('lock')
             ->withAnyArgs()
             ->andReturn($phpRedisLockMock);

        $this->expectException(LockTimeoutException::class);

        $this->artisan('currency-exchange:update')->assertExitCode(1);
    }
}
