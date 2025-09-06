<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentConversionRequest;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Services\TrivialService;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\TestCase;

final class TrivialServiceTest extends TestCase
{
    public function testSupports(): void
    {
        $service = new TrivialService();

        self::assertTrue($service->supports(new CurrentExchangeRateRequest('EUR', 'EUR')));
        self::assertTrue($service->supports(new HistoricalExchangeRateRequest('PHP', 'PHP', Date::today())));
        self::assertFalse($service->supports(new CurrentExchangeRateRequest('TRY', 'RUB')));
        self::assertFalse($service->supports(new HistoricalExchangeRateRequest('HUF', 'ZAR', Date::today())));
        self::assertFalse($service->supports(new CurrentConversionRequest(Decimal::init(1), 'EUR', 'EUR')));
    }

    public function testRate(): void
    {
        $service = new TrivialService();

        $response = $service->send(new CurrentExchangeRateRequest('EUR', 'EUR'));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('1', $response->rate->value);

        $response = $service->send(new HistoricalExchangeRateRequest('HUF', 'HUF', Calendar::parse('2025-06-13')));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('1', $response->rate->value);

        $response = $service->send(new CurrentExchangeRateRequest('USD', 'CAD'));
        self::assertInstanceOf(ErrorResponse::class, $response);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $response->exception);
        self::assertEquals('Unable to find exchange rate for USD/CAD', $response->exception->getMessage());

        // does not perform conversions
        $response = $service->send(new CurrentConversionRequest(Decimal::init('1.00'), 'USD', 'USD'));
        self::assertInstanceOf(ErrorResponse::class, $response);
        self::assertInstanceOf(RequestNotSupportedException::class, $response->exception);
        self::assertEquals(
            'Unsupported request type: "Peso\Core\Requests\CurrentConversionRequest"',
            $response->exception->getMessage(),
        );
    }
}
