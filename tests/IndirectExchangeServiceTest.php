<?php

declare(strict_types=1);

namespace Peso\Core\Tests;

use Arokettu\Date\Calendar;
use Arokettu\Date\Date;
use Peso\Core\Helpers\Calculator;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\SuccessResponse;
use Peso\Core\Services\ArrayService;
use Peso\Core\Services\IndirectExchangeService;
use Peso\Core\Services\NullService;
use Peso\Core\Services\ReverseService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(IndirectExchangeService::class)]
#[CoversClass(Calculator::class)] // multiply
class IndirectExchangeServiceTest extends TestCase
{
    public function testSupport(): void
    {
        $service = new IndirectExchangeService(new ReverseService(new ArrayService(currentRates: [
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
        $service = new ReverseService(new NullService());
        self::assertFalse($service->supports(new CurrentExchangeRateRequest('GBP', 'USD')));
        self::assertFalse($service->supports(new HistoricalExchangeRateRequest('GBP', 'USD', Date::today())));
    }

    public function testDirect(): void
    {
        $service = new IndirectExchangeService(new ReverseService(new ArrayService(currentRates: [
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
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('1.12345', $response->rate->value);

        $response = $service->send(new HistoricalExchangeRateRequest('EUR', 'USD', $date));
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('1.23456', $response->rate->value);
    }

    public function testDirectReverse(): void
    {
        $service = new IndirectExchangeService(new ReverseService(new ArrayService(currentRates: [
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
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('0.890115269927456', $response->rate->value); // assert that BcMath and Brick handle precision the same way

        $response = $service->send(new HistoricalExchangeRateRequest('USD', 'EUR', $date));
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('0.810005184033178', $response->rate->value);
    }

    public function testIndirect(): void
    {
        $service = new IndirectExchangeService(new ReverseService(new ArrayService(currentRates: [
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
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('1.49793333333295885', $response->rate->value); // 1.333333333333 (rounded) * 1.12345

        $response = $service->send(new HistoricalExchangeRateRequest('GBP', 'USD', $date));
        self::assertInstanceOf(SuccessResponse::class, $response);
        self::assertEquals('1.5432000', $response->rate->value);
    }
}
