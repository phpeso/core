<?php

namespace Peso\Core\Tests\Helpers;

use Peso\Core\Helpers\BrickCalculator;
use Peso\Core\Helpers\CalculatorInterface;

class BrickCalculatorTest extends CalculatorTestTemplate
{
    protected function getCalculator(): CalculatorInterface
    {
        return new BrickCalculator();
    }
}
