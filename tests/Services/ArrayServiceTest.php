<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Services\ArrayService;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\TestCase;
use stdClass;

// Cover everything it touches
class ArrayServiceTest extends TestCase
{
    public function testCurrent(): void
    {
        $service = new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345']
        ]);

        // capabilities
        self::assertTrue($service->supports(new CurrentExchangeRateRequest('EUR', 'USD')));
        self::assertFalse($service->supports(new HistoricalExchangeRateRequest('EUR', 'USD', Date::today())));

        self::assertEquals(
            new ExchangeRateResponse(new Decimal('1.12345'), Date::today()),
            $service->send(new CurrentExchangeRateRequest('EUR', 'USD')),
        );

        // no quote
        $error = $service->send(new CurrentExchangeRateRequest('EUR', 'ZAR'));
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $error->exception);
        self::assertEquals('Unable to find exchange rate for EUR/ZAR', $error->exception->getMessage());

        // no base
        $error = $service->send(new CurrentExchangeRateRequest('ZAR', 'USD'));
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $error->exception);
        self::assertEquals('Unable to find exchange rate for ZAR/USD', $error->exception->getMessage());

        // unknown request
        $error = $service->send(new stdClass());
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(RequestNotSupportedException::class, $error->exception);
        self::assertEquals('Unsupported request type: "stdClass"', $error->exception->getMessage());
    }

    public function testHistorical(): void
    {
        $service = new ArrayService(historicalRates: [
            '2015-01-02' => ['EUR' => ['USD' => '1.12345']]
        ]);
        $date = Calendar::parse('2015-01-02');

        // capabilities
        self::assertFalse($service->supports(new CurrentExchangeRateRequest('EUR', 'USD')));
        self::assertTrue($service->supports(new HistoricalExchangeRateRequest('EUR', 'USD', Date::today())));

        self::assertEquals(
            new ExchangeRateResponse(new Decimal('1.12345'), $date),
            $service->send(new HistoricalExchangeRateRequest('EUR', 'USD', $date)),
        );

        // no quote
        $error = $service->send(new HistoricalExchangeRateRequest('EUR', 'ZAR', $date));
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $error->exception);
        self::assertEquals('Unable to find exchange rate for EUR/ZAR on 2015-01-02', $error->exception->getMessage());

        // no base
        $error = $service->send(new HistoricalExchangeRateRequest('ZAR', 'USD', $date));
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $error->exception);
        self::assertEquals('Unable to find exchange rate for ZAR/USD on 2015-01-02', $error->exception->getMessage());

        // unknown request
        $error = $service->send(new stdClass());
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(RequestNotSupportedException::class, $error->exception);
        self::assertEquals('Unsupported request type: "stdClass"', $error->exception->getMessage());
    }
}
