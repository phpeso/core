<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Helpers;

use Peso\Core\Helpers\BcMathCalculator;
use Peso\Core\Helpers\CalculatorInterface;

class BcMathCalculatorTest extends CalculatorTestTemplate
{
    protected function setUp(): void
    {
        if (!extension_loaded('bcmath') || PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('This calculator works only in PHP 8.4+ with bcmath installed');
        }
    }

    protected function getCalculator(): CalculatorInterface
    {
        return new BcMathCalculator();
    }
}
