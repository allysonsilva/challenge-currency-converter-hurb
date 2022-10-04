<?php

namespace App\API\Currency\Http\QueryFilters;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

final class Name
{
    public function handle(BuilderContract $builder, $next)
    {
        if (! empty($name = request()->query('name'))) {
            $builder->where('name', 'LIKE', "%$name%");
        }

        return $next($builder);
    }
}
