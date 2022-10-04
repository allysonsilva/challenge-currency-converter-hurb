<?php

namespace Support\APIs\ExchangeRate\Contracts;

interface APIParams
{
    /**
     * Changing base currency.
     * Enter the three-letter currency code of your preferred base currency.
     *
     * @param string $base
     *
     * @return $this
     */
    public function base(string $base): static;

    /**
     * Enter a list of comma-separated currency codes to limit output currencies.
     *
     * @param array<string> $symbols
     *
     * @return $this
     */
    public function symbols(string ...$symbols): static;

    /**
     * Extend returned values with digital currency rates (crypto).
     *
     * @return $this
     */
    public function withCrypto(): static;
}
