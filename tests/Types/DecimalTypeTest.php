<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Types;

use BcMath\Number;
use Brick\Math\BigDecimal;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(Decimal::class)]
final class DecimalTypeTest extends TestCase
{
    public function testAcceptsString(): void
    {
        self::assertEquals('12.34', (new Decimal('12.34'))->value);
        self::assertEquals('1234', (new Decimal('1234'))->value);
        self::assertEquals('.34', (new Decimal('.34'))->value);
        self::assertEquals('12.', (new Decimal('12.'))->value);
    }

    public function testNonNumeric(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('abc.de');
    }

    public function testNegative(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('-12.34');
    }

    public function testZero1(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('0');
    }

    public function testZero2(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('0.');
    }

    public function testZero3(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('.0');
    }

    public function testZero4(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('.0');
    }

    public function testZero5(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('000.0000');
    }

    public function testEmpty(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('');
    }

    public function testDot(): void
    {
        self::expectException(ValueError::class);
        self::expectExceptionMessage('$value must be a string representing a positive decimal number');

        new Decimal('.');
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

    public function testAcceptsFloat(): void
    {
        self::assertEquals('123.45', Decimal::init(123.45)->value);
        self::assertEquals('1', Decimal::init(1)->value);
        self::assertEquals('0.25', Decimal::init(0.25)->value);
        self::assertEquals('1254456000000000000.000000', Decimal::init(1.254456e18)->value);
        self::assertEquals('0.000000000000000001254456', Decimal::init(1.254456e-18)->value);
    }
}
