<?php

namespace App\API\Currency\Http\Controllers;

use Exception;
use Support\Http\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use CurrencyDomain\Models\Currency;
use App\API\Currency\Http\QueryFilters\Name;
use App\API\Currency\Http\QueryFilters\Symbol;
use App\API\Currency\Http\QueryFilters\OrderBy;
use App\API\Currency\Http\Requests\CurrencyRequest;
use Support\APIs\ExchangeRate\Contracts\RedisConstants;
use App\API\Currency\Http\Transformers\CurrencyResource;
use App\API\Currency\Http\Transformers\CurrencyCollection;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Support\APIs\ExchangeRate\Redis\Repository as RedisRepository;

/**
 * phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.Missing
 * phpcs:disable Squiz.Functions
 */
class CurrencyController extends Controller
{
    public function __construct(private readonly RedisRepository $cacheRepository,
                                private readonly Currency $repository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Pipeline\Pipeline $pipeline
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|\Illuminate\Http\Response
     *
     * phpcs:enable Squiz.Functions
     */
    public function index(Pipeline $pipeline)
    {
        $query = $pipeline->send($this->repository::query())
                          ->through([Name::class, Symbol::class, OrderBy::class])
                          ->thenReturn();

        return new CurrencyCollection($query->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\API\Currency\Http\Requests\CurrencyRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CurrencyRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $model = $this->repository->store($request->validated());
            $this->cacheRepository->hSet(RedisConstants::REDIS_KEY_RATES, $request->input('symbol'), $request->input('rate'));

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();

            throw $ex;
        }

        return (new CurrencyResource($model))
                    ->response()
                    ->setStatusCode(HttpResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param \CurrencyDomain\Models\Currency $currency
     *
     * @return \App\API\Currency\Http\Transformers\CurrencyResource
     */
    public function show(Currency $currency): CurrencyResource
    {
        return new CurrencyResource($currency);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\API\Currency\Http\Requests\CurrencyRequest $request
     * @param int $id
     *
     * @return \App\API\Currency\Http\Transformers\CurrencyResource|\Illuminate\Http\JsonResponse
     */
    public function update(CurrencyRequest $request, int $id): CurrencyResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $model = $this->repository->updateModel($request->validated(), $id);
            $this->cacheRepository->hSet(RedisConstants::REDIS_KEY_RATES, $request->input('symbol'), $request->input('rate'));

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();

            throw $ex;
        }

        return new CurrencyResource($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $model = $this->repository->deleteModel($id);
            $this->cacheRepository->hDel(RedisConstants::REDIS_KEY_RATES, $model->symbol);

            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();

            throw $ex;
        }

        return response()->json(status: HttpResponse::HTTP_NO_CONTENT);
    }
}
