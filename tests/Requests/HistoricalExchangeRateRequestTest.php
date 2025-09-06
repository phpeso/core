<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Tests\Requests;

use Arokettu\Date\Date;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use PHPUnit\Framework\TestCase;

final class HistoricalExchangeRateRequestTest extends TestCase
{
    public function testInvert(): void
    {
        $request = new HistoricalExchangeRateRequest('CZK', 'EUR', new Date(2460845));

        $inverted = $request->invert();

        self::assertEquals('EUR', $inverted->baseCurrency);
        self::assertEquals('CZK', $inverted->quoteCurrency);
        self::assertEquals(new Date(2460845), $inverted->date);

        // immutable
        self::assertEquals('CZK', $request->baseCurrency);
        self::assertEquals('EUR', $request->quoteCurrency);
        self::assertEquals(new Date(2460845), $request->date);
    }

    public function testWithBaseCurrency(): void
    {
        $request = new HistoricalExchangeRateRequest('CZK', 'EUR', new Date(2460845));

        $wbc = $request->withBaseCurrency('USD');

        self::assertEquals('USD', $wbc->baseCurrency);
        self::assertEquals('EUR', $wbc->quoteCurrency);
        self::assertEquals(new Date(2460845), $wbc->date);

        // immutable
        self::assertEquals('CZK', $request->baseCurrency);
        self::assertEquals('EUR', $request->quoteCurrency);
        self::assertEquals(new Date(2460845), $request->date);
    }

    public function testWithQuoteCurrency(): void
    {
        $request = new HistoricalExchangeRateRequest('CZK', 'EUR', new Date(2460845));

        $wqc = $request->withQuoteCurrency('USD');

        self::assertEquals('CZK', $wqc->baseCurrency);
        self::assertEquals('USD', $wqc->quoteCurrency);
        self::assertEquals(new Date(2460845), $wqc->date);

        // immutable
        self::assertEquals('CZK', $request->baseCurrency);
        self::assertEquals('EUR', $request->quoteCurrency);
        self::assertEquals(new Date(2460845), $request->date);
    }

    public function testWithDate(): void
    {
        $request = new HistoricalExchangeRateRequest('CZK', 'EUR', new Date(2460845));

        $wd = $request->withDate(new Date(0));

        self::assertEquals('CZK', $wd->baseCurrency);
        self::assertEquals('EUR', $wd->quoteCurrency);
        self::assertEquals(new Date(0), $wd->date);

        // immutable
        self::assertEquals('CZK', $request->baseCurrency);
        self::assertEquals('EUR', $request->quoteCurrency);
        self::assertEquals(new Date(2460845), $request->date);
    }
}
