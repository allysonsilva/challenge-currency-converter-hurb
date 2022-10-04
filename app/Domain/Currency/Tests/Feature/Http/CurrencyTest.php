<?php

namespace CurrencyDomain\Tests\Feature\Http;

use LogicException;
use Tests\FeatureTestCase;
use Illuminate\Support\Facades\DB;
use CurrencyDomain\Models\Currency;
use Illuminate\Testing\Fluent\AssertableJson;
use Support\APIs\ExchangeRate\Contracts\RedisConstants;
use App\API\Currency\Http\Transformers\CurrencyResource;
use App\API\Currency\Http\Transformers\CurrencyCollection;
use Support\APIs\ExchangeRate\Redis\Repository as RedisRepository;

final class CurrencyTest extends FeatureTestCase
{
    /**
     * @testdox It must be possible to retrieve all exchange currencies
     *
     * @test
     */
    public function it_get_all_currencies()
    {
        $currencies = Currency::factory()
                              ->count(10)
                              ->create()
                              ->sortBy('name');

        $response = $this->getJson(route('api.v1.currencies.index'));

        $resource = new CurrencyCollection($currencies);

        $response->assertOk()
                 ->assertResource($resource);
    }

    /**
     * @testdox It should be possible to filter exchange currencies by name
     *
     * @test
     */
    public function it_get_currency_exchange_by_name()
    {
        Currency::factory()->name('ALAAAA')->create();
        Currency::factory()->name('BBBALB')->create();
        Currency::factory()->name('CCCCCC')->create();
        Currency::factory()->name('CCCDDD')->create();

        $response = $this->getJson(route('api.v1.currencies.index', ['name' => 'AL']));

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('data', 2)
                             ->has('data.0', fn ($json) => $json->where('name', 'ALAAAA')->etc())
                             ->has('data.1', fn ($json) => $json->where('name', 'BBBALB')->etc())
                             ->etc());
    }

    /**
     * @testdox It should be possible to filter exchange currencies by symbol
     *
     * @test
     */
    public function it_get_currency_exchange_by_symbol()
    {
        Currency::factory()->symbol('ASF')->create();
        Currency::factory()->symbol('ASQ')->create();
        Currency::factory()->symbol('ASD')->create();
        Currency::factory()->symbol('ASW')->create();

        $response = $this->getJson(route('api.v1.currencies.index', ['symbol' => 'ASD']));

        $response->assertOk()
                 ->assertJson(fn (AssertableJson $json) =>
                        $json->has('data', 1)
                             ->has('data.0', fn ($json) => $json->where('symbol', 'ASD')->etc())
                             ->etc());
    }

    /**
     * @testdox It must be possible to create a new currency exchange
     *
     * @test
     */
    public function created_successfully()
    {
        DB::spy();
        $spyRedis = $this->spy(RedisRepository::class);

        $currency = Currency::factory()->make();

        $this->postJson(route('api.v1.currencies.store'), $currency->toArray())
             ->assertCreated();

        $spyRedis->shouldHaveReceived('hSet')
                 ->with(RedisConstants::REDIS_KEY_RATES, $currency->symbol, $currency->rate);

        DB::shouldHaveReceived('beginTransaction')->once()->withNoArgs();
        DB::shouldHaveReceived('commit')->once()->withNoArgs();
    }

    /**
     * @testdox It should be possible to rollback if the resource cannot be saved
     *
     * @test
     */
    public function handleRollbackOnStore()
    {
        DB::spy();
        $spyRedis = $this->spy(RedisRepository::class);

        $data = Currency::factory()->make()->toArray();

        $this->partialMock(Currency::class)
             ->shouldReceive('store')
             ->with($data)
             ->andThrow(new LogicException('Handle Rollback'));

        $this->expectException(LogicException::class);

        $this->withoutExceptionHandling()
             ->postJson(route('api.v1.currencies.store'), $data);

        $spyRedis->shouldNotHaveReceived('hSet');
        DB::shouldHaveReceived('rollBack')->once()->withNoArgs();
    }

    /**
     * @testdox It must be possible to show a new currency exchange
     *
     * @test
     */
    public function shown_correctly()
    {
        $currency = Currency::factory()->create();

        $resource = new CurrencyResource($currency);

        $this->getJson(route('api.v1.currencies.show', $currency))
             ->assertOk()
             ->assertResource($resource);
    }

    /**
     * @testdox It must be possible to update a new currency exchange
     *
     * @test
     */
    public function update_successfully()
    {
        $currency = Currency::factory()->create();
        $currencyUpdated = Currency::factory()->make();

        DB::spy();
        $spyRedis = $this->spy(RedisRepository::class);

        $response = $this->putJson(route('api.v1.currencies.update', $currency->getKey()), $currencyUpdated->toArray());

        foreach ($currency->only('id', 'created_at', 'updated_at') as $key => $value) {
            $currencyUpdated->{$key} = $value;
        }

        $response->assertOk()
                 ->assertResource((new CurrencyResource($currencyUpdated)));

        $spyRedis->shouldHaveReceived('hSet')
                 ->with(RedisConstants::REDIS_KEY_RATES, $currencyUpdated->symbol, $currencyUpdated->rate);

        DB::shouldHaveReceived('beginTransaction')->once()->withNoArgs();
        DB::shouldHaveReceived('commit')->once()->withNoArgs();
    }

    /**
     * @testdox It should be possible to rollback if the resource cannot be updated
     *
     * @test
     */
    public function handleRollbackOnUpdate()
    {
        DB::spy();
        $spyRedis = $this->spy(RedisRepository::class);

        $currency = Currency::factory()->create();

        $this->partialMock(Currency::class)
             ->shouldReceive('updateModel')
             ->with($currency->toArray(), $currency->getKey())
             ->andThrow(new LogicException('Handle Rollback'));

        $this->expectException(LogicException::class);

        $this->withoutExceptionHandling()
             ->putJson(route('api.v1.currencies.update', $currency->getKey()), $currency->toArray());

        $spyRedis->shouldNotHaveReceived('hSet');
        DB::shouldHaveReceived('rollBack')->once()->withNoArgs();
    }

    /**
     * @testdox It must be possible to remove an exchange currency
     *
     * @test
     */
    public function successfully_delete()
    {
        $currency = Currency::factory()->create();

        DB::spy();
        $spyRedis = $this->spy(RedisRepository::class);

        $this->deleteJson(route('api.v1.currencies.destroy', $currency->getKey()))
             ->assertNoContent();

        $spyRedis->shouldHaveReceived('hDel')
                 ->with(RedisConstants::REDIS_KEY_RATES, $currency->symbol);

        DB::shouldHaveReceived('beginTransaction')->once()->withNoArgs();
        DB::shouldHaveReceived('commit')->once()->withNoArgs();
    }

    /**
     * @testdox It should be possible to rollback if the resource cannot be deleted
     *
     * @test
     */
    public function handleRollbackOnDelete()
    {
        DB::spy();
        $spyRedis = $this->spy(RedisRepository::class);

        $currency = Currency::factory()->create();

        $this->partialMock(Currency::class)
             ->shouldReceive('deleteModel')
             ->with($currency->getKey())
             ->andThrow(new LogicException('Handle Rollback'));

        $this->expectException(LogicException::class);

        $this->withoutExceptionHandling()
             ->deleteJson(route('api.v1.currencies.destroy', $currency->getKey()));

        $spyRedis->shouldNotHaveReceived('hDel');
        DB::shouldHaveReceived('rollBack')->once()->withNoArgs();
    }
}
