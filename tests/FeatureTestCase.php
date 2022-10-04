<?php

namespace Tests;

use Tests\Traits\FeatureMacros;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Http\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Auth\Middleware\Authorize as AuthorizeMiddleware;

abstract class FeatureTestCase extends TestCase
{
    use FeatureMacros;
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            ThrottleRequests::class,
            AuthenticateMiddleware::class,
            AuthorizeMiddleware::class,
        ]);
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits(): array
    {
        $uses = parent::setUpTraits();

        if (isset($uses[FeatureMacros::class])) {
            $this->assertData();
            $this->assertResourceMacro();
        }

        return $uses;
    }
}
