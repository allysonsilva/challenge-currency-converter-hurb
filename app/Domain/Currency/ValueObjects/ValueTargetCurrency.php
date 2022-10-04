<?php

namespace CurrencyDomain\ValueObjects;

use Stringable;
use Cknow\Money\Money;
use Illuminate\Support\Traits\ForwardsCalls;

final class ValueTargetCurrency implements Stringable
{
    use ForwardsCalls;

    public function __construct(
        private readonly Money $value,
        public readonly float $rate
    ) {
    }

    /**
     * @codeCoverageIgnore
     */
    public function value(): string
    {
        return $this->value->render();
    }

    /**
     * @codeCoverageIgnore
     */
    public function __toString(): string
    {
        return $this->value->__toString();
    }

    /**
     * Handle dynamic method calls into the money.
     *
     * @param  string  $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->forwardCallTo($this->value, $method, $arguments);
    }
}
