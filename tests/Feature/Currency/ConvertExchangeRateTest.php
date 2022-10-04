<?php

namespace Tests\Feature\Currency;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\API\Currency\Mail\ConvertedCurrency;
use CurrencyDomain\DTO\ConvertedCurrencyResultDTO;
use Core\Http\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Auth\Middleware\Authorize as AuthorizeMiddleware;

class ConvertExchangeRateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config(['currency.driver' => 'open']);

        $this->withoutMiddleware([AuthenticateMiddleware::class, AuthorizeMiddleware::class]);
    }

    /**
     * @dataProvider getFixtures
     */
    public function testConvertExchangeRate(array $queryRequest, int $statusCode, array $responseBody)
    {
        $response = $this->getJson(route('api.v1.currencies.convert', $queryRequest));

        $response
            ->assertStatus($statusCode)
            ->assertJson($responseBody);
    }

    /**
     * @testdox Deve ser enviado um email sobre o resultado da conversão
     *
     * @test
     */
    public function withMail(): void
    {
        Mail::fake();

        $query = [
            'from' => 'BRL',
            'to' => 'USD',
            'amount' => '5000.00',
        ];

        $response = $this->withMiddleware(AuthenticateMiddleware::class)->getJson(route('api.v1.currencies.convert', $query));

        Mail::assertQueued(function (ConvertedCurrency $mail) {
            return $mail->details instanceof ConvertedCurrencyResultDTO &&
                    $mail->hasSubject('Valor convertido de BRL => USD');
        });

        $response->assertOk();
    }

    private function getFixtures(): iterable
    {
        $fixtures = glob(__DIR__ . '/Fixtures/*.json');

        foreach ($fixtures as $filename) {
            $fixture = json_decode(file_get_contents($filename) ?: '', true);

            yield $fixture['name'] => [
                'query' => data_get($fixture, 'request.query'),
                'statusCode' => data_get($fixture, 'response.statusCode'),
                'responseBody' => data_get($fixture, 'response.body'),
            ];
        }
    }
}
