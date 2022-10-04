<?php

namespace Tests\Feature\ThirdPartyAPI;

use Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Support\APIs\ExchangeRate\DTO\ConvertedRateDTO;
use Support\APIs\ExchangeRate\Facades\ExchangeRate;

/**
 * @group 3rdPartyAPI
 */
class ExchangeRateFreeTest extends TestCase
{
    /**
     * @var array<mixed, mixed>
     */
    private array $rates;

    public function setUp(): void
    {
        parent::setUp();

        config(['currency.driver' => 'free']);

        $this->rates = [
            'base' => 'USD',
            'rates' => [
                'AAA' => 10,
                'BBB' => 20,
                'CCC' => 30,
                'DDD' => 40,
                'EEE' => 50,
                'FFF' => 60,
                'GGG' => 70,
                'HHH' => 80,
            ],
        ];
    }

    /**
     * @testdox Recuperando os valores das últimas taxas de câmbio.
     *
     * @test
     */
    public function latestRates(): void
    {
        // Arrange
        $rates = [
            'base' => 'USD',
            'rates' => [
                'A' => 1.0,
                'B' => 2.1,
                'C' => 3.2,
                'E' => 5.3,
            ],
        ];

        ExchangeRate::httpFake('latest', Http::response($rates));

        // Act
        $latest = ExchangeRate::latest();

        // Assert
        self::assertEqualsCanonicalizing($rates['rates'], $latest->toArray());
        Http::assertSentCount(1);
    }

    /**
     * @testdox Deve ser possível retornar `null` em `latest` quando alguma exceção for lançado e não haver tratamento para essa exceção
     *
     * @test
     */
    public function handleNoException()
    {
        Http::fake([
            '*' => Http::response('Server Error', 500),
        ]);

        self::assertNull(ExchangeRate::latest());

        Http::assertSentCount(1);
    }

    /**
     * @testdox Testando os parâmetros de query string na requisição `latest` da API de TAXA DE CÂMBIO
     *
     * @test
     */
    public function addingParametersInApiRequest(): void
    {
        $exchangeRate = ExchangeRate::symbols('ABC', 'DEF')->base('BRL')->withCrypto();
        ExchangeRate::httpFake('latest', Http::response($this->rates));

        $exchangeRate->latest();

        Http::assertSent(function (Request $request) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY), $query);

            return 'BRL' === $query['base'] &&
                    'ABC,DEF' === $query['symbols'] &&
                    'crypto' === $query['source'];
        });
    }

    /**
     * @testdox Conversão de moedas realiada com sucesso
     *
     * @test
     */
    public function currencyConvertedSuccessfully(): void
    {
        ExchangeRate::httpFake('convert', Http::response(['result' => 10.00]), [
            'from' => 'AAA',
            'to' => 'BBB',
            'amount' => 5.00,
        ]);

        $convertedDTO = ExchangeRate::convert('AAA', 'BBB', 5.00);

        Http::assertSentCount(1);
        static::assertTrue($convertedDTO instanceof ConvertedRateDTO);
        static::assertSame($convertedDTO->convertedValue, 10.00);
    }

    /**
     * @testdox Deve ser possível manipular exceções na resposta da API
     *
     * @test
     */
    public function handleWithException(): void
    {
        $this->expectException(RequestException::class);

        Http::fake([
            '*' => Http::response('Server Error', 500),
        ]);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter
        ExchangeRate::whenThrowException(function (Response $response, $e) {
            static::assertEquals(500, $response->status());
        })->latest();

        Http::assertSentCount(1);
    }
}
