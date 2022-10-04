<?php

namespace App\API\Currency\Http\QueryFilters;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

final class OrderBy
{
    public function handle(BuilderContract $builder, $next)
    {
        $builder->orderBy('name')
                ->orderByDesc('id');

        return $next($builder);
    }
}
