<?php

namespace CurrencyDomain\DTO;

use Support\DTO\BaseDto;
use CurrencyDomain\ValueObjects\ValueToBeConverted;
use CurrencyDomain\ValueObjects\ValueTargetCurrency;

final class ConvertedCurrencyResultDTO extends BaseDto
{
    public readonly string $originCurrency;

    public readonly string $targetCurrency;

    public readonly string $baseCurrency;

    public ValueToBeConverted $valueToBeConverted;

    public ValueTargetCurrency $convertedToTargetCurrency;

    public function fromSymbol(): string
    {
        return strtoupper($this->originCurrency);
    }

    public function fromExchangeRate(): string
    {
        return $this->valueToBeConverted->rate;
    }

    public function getValueToBeConverted(): string
    {
        return $this->valueToBeConverted->value();
    }

    public function toSymbol(): string
    {
        return strtoupper($this->targetCurrency);
    }

    public function toExchangeRate(): string
    {
        return $this->convertedToTargetCurrency->rate;
    }

    public function getValueOfConverted(): string
    {
        return $this->convertedToTargetCurrency->value();
    }
}
