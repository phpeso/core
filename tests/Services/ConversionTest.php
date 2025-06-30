<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;
use Peso\Core\Exceptions\ConversionNotPerformedException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentConversionRequest;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalConversionRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Services\ArrayService;
use Peso\Core\Services\ConversionService;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ConversionService::class)]
#[CoversClass(CurrentConversionRequest::class)]
#[CoversClass(HistoricalConversionRequest::class)]
#[CoversClass(ConversionResponse::class)]
#[CoversClass(ConversionNotPerformedException::class)]
final class ConversionTest extends TestCase
{
    public function testCurrentConversion(): void
    {
        $service = new ConversionService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345']
        ]));

        $response = $service->send(new CurrentConversionRequest(Decimal::init(1000), 'EUR', 'USD'));

        self::assertInstanceOf(ConversionResponse::class, $response);
        self::assertEquals('1123.45000', $response->amount->value);
    }

    public function testHistoricalConversion(): void
    {
        $service = new ConversionService(new ArrayService(historicalRates: [
            '2015-01-02' => ['EUR' => ['USD' => '1.23456']]
        ]));
        $date = Calendar::parse('2015-01-02');

        $response = $service->send(
            new HistoricalConversionRequest(Decimal::init(1000), 'EUR', 'USD', $date),
        );

        self::assertInstanceOf(ConversionResponse::class, $response);
        self::assertEquals('1234.56000', $response->amount->value);
    }

    public function testNoRate(): void
    {
        $service = new ConversionService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345']
        ]));

        $response = $service->send(new CurrentConversionRequest(Decimal::init(1000), 'USD', 'EUR'));

        self::assertInstanceOf(ErrorResponse::class, $response);
        self::assertInstanceOf(ConversionNotPerformedException::class, $response->exception);
        self::assertEquals('Unable to convert 1000 USD to EUR', $response->exception->getMessage());
    }

    public function testUnsupportedQuery(): void
    {
        $service = new ConversionService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345']
        ]));

        $response = $service->send(new CurrentExchangeRateRequest('USD', 'EUR'));

        self::assertInstanceOf(ErrorResponse::class, $response);
        self::assertInstanceOf(RequestNotSupportedException::class, $response->exception);
        self::assertEquals(
            'Unsupported request type: "Peso\Core\Requests\CurrentExchangeRateRequest"',
            $response->exception->getMessage(),
        );
    }

    public function testSupports(): void
    {
        $service = new ConversionService(new ArrayService(
            currentRates: ['EUR' => ['USD' => '1.12345']],
            historicalRates: [
                '2015-01-02' => ['EUR' => ['USD' => '1.23456']]
            ],
        ));
        $date = Date::today();
        $amount = Decimal::init(1000);

        self::assertFalse($service->supports(new CurrentExchangeRateRequest('PHP', 'BTC')));
        self::assertFalse($service->supports(new HistoricalExchangeRateRequest('PHP', 'BTC', $date)));

        self::assertTrue($service->supports(new CurrentConversionRequest($amount, 'PHP', 'BTC')));
        self::assertTrue($service->supports(new HistoricalConversionRequest($amount, 'PHP', 'BTC', $date)));

        self::assertFalse($service->supports(new stdClass()));
    }
}
