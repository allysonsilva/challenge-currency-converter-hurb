<?php

namespace CurrencyDomain\Tests\Unit;

use Tests\TestCase;
use Cknow\Money\Money;
use CurrencyDomain\ValueObjects\ValueToBeConverted;
use CurrencyDomain\ValueObjects\ValueTargetCurrency;

final class ValueObjectsTest extends TestCase
{
    /**
     * @testdox Testing Value Object "ValueTargetCurrency"
     *
     * @test
     */
    public function valueTargetCurrency()
    {
        $valueTargetCurrency = new ValueTargetCurrency(money(10.00, 'BRL'), 5.0);

        self::assertTrue($valueTargetCurrency->equals(Money::BRL(10.00)));
        self::assertSame($valueTargetCurrency->multiply(2)->render(), 'R$ 20,00');
        self::assertTrue($valueTargetCurrency->equals($valueTargetCurrency->methodNotExist()));
    }

    /**
     * @testdox Testing Value Object "ValueToBeConverted"
     *
     * @test
     */
    public function valueToBeConverted()
    {
        $valueToBeConverted = new ValueToBeConverted(money(20.00, 'BRL'), 10.00);

        self::assertTrue($valueToBeConverted->equals(Money::BRL(20.00)));
        self::assertSame($valueToBeConverted->multiply(2)->render(), 'R$ 40,00');
        self::assertTrue($valueToBeConverted->equals($valueToBeConverted->methodNotExist()));
    }
}
