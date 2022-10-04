<?php

namespace CurrencyDomain\Models;

use Support\ORM\BaseModel;
use CurrencyDomain\Enums\CurrencyType;
use CurrencyDomain\Database\Factories\CurrencyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

/**
 * phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
 */
class Currency extends BaseModel
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'symbol',
        'rate',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, mixed>
     */
    protected $casts = [
        'rate' => 'decimal:6',
        'type' => CurrencyType::class,
    ];

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQueryForeignExchangeCurrencies(BuilderContract $query): BuilderContract
    {
        return $query->where('type', '!=', CurrencyType::FICTITIOUS->value)
                     ->whereNull('rate');
    }

    /**
     * Recovers all currency symbols that have
     * not been manipulated manually are not fictitious currencies.
     *
     * @return array<string>
     */
    public function foreignExchangeCurrencies(): array
    {
        return $this->queryForeignExchangeCurrencies()
                    ->pluck('symbol')
                    ->all();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQueryManualExchangeCurrencies(BuilderContract $query): BuilderContract
    {
        return $query->where('type', CurrencyType::FICTITIOUS->value)
                     ->orWhereNotNull('rate');
    }

    /**
     * Recovers all exchange rates that your values have been manipulated or are fictitious currencies.
     *
     * @return array<string,float>
     */
    public function manualExchangeCurrencies(): array
    {
        return $this->queryManualExchangeCurrencies()
                    ->pluck('rate', 'symbol')
                    ->all();
    }

    public static function getTableName(): string
    {
        return with(new static())->getTable();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory<static>
     */
    protected static function newFactory()
    {
        return CurrencyFactory::new();
    }
}
