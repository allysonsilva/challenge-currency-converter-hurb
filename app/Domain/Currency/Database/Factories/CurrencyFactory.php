<?php

namespace CurrencyDomain\Database\Factories;

use CurrencyDomain\Models\Currency;
use CurrencyDomain\Enums\CurrencyType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * phpcs:disable SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
 */
class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model|TModel>
     */
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->word(),
            'symbol' => fake()->regexify('[A-Z]{3}'),
            'rate' => fake()->randomFloat(2, 10, 100),
            'type' => CurrencyType::FICTITIOUS->value,
        ];
    }

    public function name(string $name): static
    {
        return $this->state(function (array $attributes) use ($name) {
            return [
                'name' => $name,
            ];
        });
    }

    public function symbol(string $symbol): static
    {
        return $this->state(function (array $attributes) use ($symbol) {
            return [
                'symbol' => $symbol,
            ];
        });
    }

    public function rate(float $rate): static
    {
        return $this->state(function (array $attributes) use ($rate) {
            return [
                'rate' => $rate,
            ];
        });
    }

    public function typeFiat(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => CurrencyType::FIAT->value,
                'rate' => null,
            ];
        });
    }
}
