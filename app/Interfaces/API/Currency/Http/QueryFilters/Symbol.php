<?php

namespace App\API\Currency\Http\QueryFilters;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

final class Symbol
{
    public function handle(BuilderContract $builder, $next)
    {
        $symbol = request()->query('symbol');

        if (! empty($symbol)) {
            $builder->where('symbol', $symbol);
        }

        return $next($builder);
    }
}
