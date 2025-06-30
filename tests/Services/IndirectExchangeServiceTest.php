<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Services\ArrayService;
use Peso\Core\Services\ChainService;
use Peso\Core\Services\IndirectExchangeService;
use Peso\Core\Services\NullService;
use Peso\Core\Services\ReversibleService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(IndirectExchangeService::class)]
final class IndirectExchangeServiceTest extends TestCase
{
    public function testSupport(): void
    {
        $service = new IndirectExchangeService(new ReversibleService(new ArrayService(currentRates: [
            'EUR' => [
                'USD' => '1.12345',
                'GBP' => '0.75',
            ],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => [
                'USD' => '1.23456',
                'GBP' => '0.8',
            ]],
        ])), 'EUR');

        // direct
        self::assertTrue($service->supports(new CurrentExchangeRateRequest('EUR', 'USD')));
        self::assertTrue($service->supports(new HistoricalExchangeRateRequest('EUR', 'USD', Date::today())));

        // direct reverse
        self::assertTrue($service->supports(new CurrentExchangeRateRequest('GBP', 'EUR')));
        self::assertTrue($service->supports(new HistoricalExchangeRateRequest('GBP', 'EUR', Date::today())));

        // indirect
        self::assertTrue($service->supports(new CurrentExchangeRateRequest('GBP', 'USD')));
        self::assertTrue($service->supports(new HistoricalExchangeRateRequest('GBP', 'USD', Date::today())));

        // only requests that we know how to reverse are supported

        self::assertFalse($service->supports(new stdClass()));

        // inner that doesn't support requests
        $service = new ReversibleService(new NullService());
        self::assertFalse($service->supports(new CurrentExchangeRateRequest('GBP', 'USD')));
        self::assertFalse($service->supports(new HistoricalExchangeRateRequest('GBP', 'USD', Date::today())));
    }

    public function testDirect(): void
    {
        $service = new IndirectExchangeService(new ReversibleService(new ArrayService(currentRates: [
            'EUR' => [
                'USD' => '1.12345',
                'GBP' => '0.75',
            ],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => [
                'USD' => '1.23456',
                'GBP' => '0.8',
            ]],
        ])), 'EUR');
        $date = Calendar::parse('2015-01-02');

        $response = $service->send(new CurrentExchangeRateRequest('EUR', 'USD'));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('1.12345', $response->rate->value);

        $response = $service->send(new HistoricalExchangeRateRequest('EUR', 'USD', $date));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('1.23456', $response->rate->value);
    }

    public function testDirectReverse(): void
    {
        $service = new IndirectExchangeService(new ReversibleService(new ArrayService(currentRates: [
            'EUR' => [
                'USD' => '1.12345',
                'GBP' => '0.75',
            ],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => [
                'USD' => '1.23456',
                'GBP' => '0.8',
            ]],
        ])), 'EUR');
        $date = Calendar::parse('2015-01-02');

        $response = $service->send(new CurrentExchangeRateRequest('USD', 'EUR'));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        // assert that BcMath and Brick handle precision the same way
        self::assertEquals('0.890115269927456', $response->rate->value);

        $response = $service->send(new HistoricalExchangeRateRequest('USD', 'EUR', $date));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('0.810005184033178', $response->rate->value);
    }

    public function testIndirect(): void
    {
        $service = new IndirectExchangeService(new ReversibleService(new ArrayService(currentRates: [
            'EUR' => [
                'USD' => '1.12345',
                'GBP' => '0.75',
            ],
        ], historicalRates: [
            '2015-01-02' => ['EUR' => [
                'USD' => '1.23456',
                'GBP' => '0.8',
            ]],
        ])), 'EUR');
        $date = Calendar::parse('2015-01-02');

        $response = $service->send(new CurrentExchangeRateRequest('GBP', 'USD'));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('1.49793333333295885', $response->rate->value); // 1.333333333333 (rounded) * 1.12345

        $response = $service->send(new HistoricalExchangeRateRequest('GBP', 'USD', $date));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('1.5432000000000000', $response->rate->value);
    }

    public function testInnerServiceNotSupportsQuery(): void
    {
        $array = new ArrayService([
            'USD' => [
                'EUR' => '0.92',
                'RUB' => '0.016153'
            ],
            'EUR' => ['USD' => '1.087'],
            'RUB' => ['USD' => '61.9077'],
        ]);
        $service = new IndirectExchangeService($array, 'EUR');

        // first result fails
        $result = $service->send(new CurrentExchangeRateRequest('USD', 'RUB'));
        self::assertInstanceOf(ErrorResponse::class, $result);
        self::assertEquals('Unable to find exchange rate for EUR/RUB', $result->exception->getMessage());

        // second result fails
        $result = $service->send(new CurrentExchangeRateRequest('RUB', 'USD'));
        self::assertInstanceOf(ErrorResponse::class, $result);
        self::assertEquals('Unable to find exchange rate for RUB/EUR', $result->exception->getMessage());
    }

    public function testReturnSmallestDate(): void
    {
        $service1 = new ArrayService(
            currentRates: ['EUR' => ['USD' => '1.125']],
            currentDate: Calendar::parse('2025-06-13'),
        );
        $service2 = new ArrayService(
            currentRates: ['CZK' => ['EUR' => '0.04']],
            currentDate: Calendar::parse('2025-06-20'),
        );
        $service = new IndirectExchangeService(new ChainService($service1, $service2), 'EUR');

        $response = $service->send(new CurrentExchangeRateRequest('CZK', 'USD'));
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('0.04500', $response->rate->value);
        self::assertEquals('2025-06-13', $response->date->toString());
    }
}
