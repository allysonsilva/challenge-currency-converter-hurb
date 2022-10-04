<?php

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Http\Resources\Json\JsonResource;

trait FeatureMacros
{
    protected function assertData(): void
    {
        TestResponse::macro('assertData', function (int $count, string $key = 'data') {
            $this->assertJson(fn (AssertableJson $json) => $json->has($key, $count)->etc());

            return $this;
        });
    }

    /**
     * @example $resource = XyzResource::collection($data);
     *          $response->assertOk()->assertResource($resource);
     */
    protected function assertResourceMacro(): void
    {
        TestResponse::macro('assertResource', function (JsonResource $resource) {
            $responseData = $resource->response()->getData(true);

            $this->assertJson($responseData);

            return $this;
        });
    }
}
