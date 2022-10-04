<?php

namespace Support\ORM;

use Illuminate\Database\Eloquent\Scope as GlobalScope;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class BaseModel extends EloquentModel
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = [ // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
        'id',
    ];

    /**
     * Store a newly created resource in storage.
     *
     * @param array<mixed> $attributes
     *
     * @return $this
     */
    public function store(array $attributes): static
    {
        $entity = $this->newInstance()->fill($attributes);

        $entity->save();

        return $entity;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param array<string, mixed> $attributes
     * @param int $id
     *
     * @return $this
     */
    public function updateModel(array $attributes, int $id): static
    {
        $entity = $this->newInstance()
                       ->lockForUpdate()
                       ->findOrFail($id);

        $entity->fill($attributes);

        $entity->save();

        return $entity;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return $this|bool
     */
    public function deleteModel(int $id): static|bool
    {
        $entity = $this->newInstance()
                       ->lockForUpdate()
                       ->findOrFail($id);

        if (! $entity->delete()) {
            return false; // @codeCoverageIgnore
        }

        return $entity;
    }

    /**
     * Aplica um Query Scopes no objeto.
     *
     * @param \Illuminate\Database\Eloquent\Scope $criterion
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function addCriteria(GlobalScope $criterion): void
    {
        static::addGlobalScope($criterion);
    }
}
