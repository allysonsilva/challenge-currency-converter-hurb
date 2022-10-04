<?php

namespace Support\APIs\ExchangeRate\Services\Concerns;

trait CommonQueryParams
{
    /**
     * Parameters that will be used in the request.
     *
     * @var array<string, mixed>
     */
    protected array $queryParams = [];

    /**
     * @inheritDoc
     */
    public function base(string $base): static
    {
        $this->queryParams['base'] = strtoupper($base);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function symbols(string ...$symbols): static
    {
        $this->queryParams['symbols'] = implode(',', $symbols);

        return $this;
    }
}
