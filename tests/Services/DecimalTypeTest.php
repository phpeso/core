<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use BcMath\Number;
use Brick\Math\BigDecimal;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Decimal::class)]
class DecimalTypeTest extends TestCase
{
    public function testAcceptsString(): void
    {
        self::assertEquals('12.34', (new Decimal('12.34'))->value);
        self::assertEquals('1234', (new Decimal('1234'))->value);
        self::assertEquals('.34', (new Decimal('.34'))->value);
        self::assertEquals('12.', (new Decimal('12.'))->value);
        self::assertEquals('0', (new Decimal('.'))->value);
        self::assertEquals('-12.34', (new Decimal('-12.34'))->value);
    }

    public function testNonNumeric(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a decimal number');

        new Decimal('abc.de');
    }

    public function testAcceptsAnotherDecimal(): void
    {
        $decimal = new Decimal('12.34');

        self::assertEquals($decimal, Decimal::init($decimal));
    }

    public function testAcceptsBcMath(): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('PHP 8.4+ only');
        }

        $number = new Number('12.34');
        self::assertEquals('12.34', Decimal::init($number)->value);
    }

    public function testAcceptsBrick(): void
    {
        $number = BigDecimal::of('12.34');
        self::assertEquals('12.34', Decimal::init($number)->value);
    }
}
