<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;
use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\SuccessResponse;
use Peso\Core\Services\ArrayService;
use Peso\Core\Services\NullService;
use Peso\Core\Services\ReversibleService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(ReversibleService::class)]
#[CoversClass(CurrentExchangeRateRequest::class)] // inversion
#[CoversClass(HistoricalExchangeRateRequest::class)] // inversion
class ReversibleServiceTest extends TestCase
{
    public function testSupport(): void
    {
        $service = new ReversibleService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345'],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => ['USD' => '1.23456']],
        ]));

        self::assertTrue($service->supports(new CurrentExchangeRateRequest('EUR', 'USD')));
        self::assertTrue($service->supports(new HistoricalExchangeRateRequest('EUR', 'USD', Date::today())));

        // only requests that we know how to reverse are supported

        self::assertFalse($service->supports(new stdClass()));

        // inner that doesn't support requests
        $service = new ReversibleService(new NullService());
        self::assertFalse($service->supports(new CurrentExchangeRateRequest('EUR', 'USD')));
        self::assertFalse($service->supports(new HistoricalExchangeRateRequest('EUR', 'USD', Date::today())));
    }

    public function testDirect(): void
    {
        $service = new ReversibleService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345'],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => ['USD' => '1.23456']],
        ]));
        $date = Calendar::parse('2015-01-02');

        $response = $service->send(new CurrentExchangeRateRequest('EUR', 'USD'));
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('1.12345', $response->rate->value);

        $response = $service->send(new HistoricalExchangeRateRequest('EUR', 'USD', $date));
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('1.23456', $response->rate->value);
    }

    public function testReverse(): void
    {
        $service = new ReversibleService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345'],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => ['USD' => '1.23456']],
        ]));
        $date = Calendar::parse('2015-01-02');

        $response = $service->send(new CurrentExchangeRateRequest('USD', 'EUR'));
        self::assertInstanceOf(SuccessResponse::class, $response);
        // assert that BcMath and Brick handle precision the same way
        self::assertEquals('0.890115269927456', $response->rate->value);

        $response = $service->send(new HistoricalExchangeRateRequest('USD', 'EUR', $date));
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('0.810005184033178', $response->rate->value);
    }

    public function testNotFound(): void
    {
        $service = new ReversibleService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345'],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => ['USD' => '1.23456']],
        ]));

        $response = $service->send(new CurrentExchangeRateRequest('ZAR', 'BYN'));
        self::assertInstanceOf(ErrorResponse::class, $response);
        self::assertInstanceOf(ConversionRateNotFoundException::class, $response->exception);
        // shows the error from the direct conversion
        self::assertEquals('Unable to find exchange rate for ZAR/BYN', $response->exception->getMessage());
    }

    public function testOnlyKnownRequests(): void
    {
        $service = new ReversibleService(new ArrayService(currentRates: [
            'EUR' => ['USD' => '1.12345'],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => ['USD' => '1.23456']],
        ]));

        $response = $service->send(new stdClass());
        self::assertInstanceOf(ErrorResponse::class, $response);
        self::assertInstanceOf(RequestNotSupportedException::class, $response->exception);
        // shows the error from the direct conversion
        self::assertEquals('Unrecognized request type: "stdClass"', $response->exception->getMessage());
    }
}
