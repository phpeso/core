<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Requests;

use Peso\Core\Requests\CurrentExchangeRateRequest;
use PHPUnit\Framework\TestCase;

class CurrentExchangeRateRequestTest extends TestCase
{
    public function testInvert(): void
    {
        $request = new CurrentExchangeRateRequest('CZK', 'EUR');

        $inverted = $request->invert();

        self::assertEquals('EUR', $inverted->baseCurrency);
        self::assertEquals('CZK', $inverted->quoteCurrency);

        // immutable
        self::assertEquals('CZK', $request->baseCurrency);
        self::assertEquals('EUR', $request->quoteCurrency);
    }

    public function testWithBaseCurrency(): void
    {
        $request = new CurrentExchangeRateRequest('CZK', 'EUR');

        $wbc = $request->withBaseCurrency('USD');

        self::assertEquals('USD', $wbc->baseCurrency);
        self::assertEquals('EUR', $wbc->quoteCurrency);

        // immutable
        self::assertEquals('CZK', $request->baseCurrency);
        self::assertEquals('EUR', $request->quoteCurrency);
    }

    public function testWithQuoteCurrency(): void
    {
        $request = new CurrentExchangeRateRequest('CZK', 'EUR');

        $wqc = $request->withQuoteCurrency('USD');

        self::assertEquals('CZK', $wqc->baseCurrency);
        self::assertEquals('USD', $wqc->quoteCurrency);

        // immutable
        self::assertEquals('CZK', $request->baseCurrency);
        self::assertEquals('EUR', $request->quoteCurrency);
    }
}
