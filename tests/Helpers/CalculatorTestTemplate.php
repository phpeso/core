<?php

namespace Peso\Core\Tests\Helpers;

use Peso\Core\Helpers\CalculatorInterface;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\TestCase;

abstract class CalculatorTestTemplate extends TestCase
{
    abstract protected function getCalculator(): CalculatorInterface;

    public function testMultiplyBrick(): void
    {
        $calc = $this->getCalculator();

        // simple case
        $x = new Decimal('1.11111');
        $y = new Decimal('1.23456');

        self::assertEquals('1.3717319616', $calc->multiply($x, $y)->value);

        // save precision
        $x = new Decimal('1.11000');
        $y = new Decimal('1.23000');

        self::assertEquals('1.3653000000', $calc->multiply($x, $y)->value);
    }

    public function testInvert(): void
    {
        $calc = $this->getCalculator();

        // 1
        self::assertEquals('1.0000000000', $calc->invert(new Decimal('1'))->value);
        // exact value
        self::assertEquals('0.0156250000', $calc->invert(new Decimal('64'))->value);
        // periodic
        self::assertEquals('0.3333333333', $calc->invert(new Decimal('3'))->value);
        self::assertEquals('0.0059523810', $calc->invert(new Decimal('168'))->value);
        self::assertEquals('0.0040816327', $calc->invert(new Decimal('245'))->value);
    }

    public function testRound(): void
    {
        $calc = $this->getCalculator();

        // half even
        self::assertEquals('0.12132', $calc->round(new Decimal('0.121325'), 5)->value);
        self::assertEquals('0.12134', $calc->round(new Decimal('0.121335'), 5)->value);
        // not half
        self::assertEquals('0.12133', $calc->round(new Decimal('0.1213251'), 5)->value);
        self::assertEquals('0.12134', $calc->round(new Decimal('0.1213351'), 5)->value);
    }

    public function testDivide(): void
    {
        $calc = $this->getCalculator();

        $x = new Decimal('1.11111');
        $y = new Decimal('1.23456');

        self::assertEquals('0.90000486003110440758', $calc->divide($x, $y)->value);
    }
}
