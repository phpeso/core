<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Date;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Exceptions\NoSuitableServiceFoundException;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentConversionRequest;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Services\ArrayService;
use Peso\Core\Services\ChainService;
use Peso\Core\Services\ConversionService;
use Peso\Core\Services\NullService;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ValueError;

#[CoversClass(ChainService::class)]
#[CoversClass(NoSuitableServiceFoundException::class)]
final class ChainServiceTest extends TestCase
{
    public function testFound(): void
    {
        $service = new ChainService(
            new NullService(),
            new ArrayService(currentRates: [
                'EUR' => ['USD' => '1.12345']
            ]),
        );

        $request = new CurrentExchangeRateRequest('EUR', 'USD');
        self::assertTrue($service->supports($request));

        $response = $service->send($request);
        self::assertInstanceOf(ExchangeRateResponse::class, $response);
        self::assertEquals('1.12345', $response->rate->value);
    }

    public function testChainOfConverters(): void
    {
        $service = new ChainService(
            new ConversionService(new NullService()),
            new ConversionService(new ArrayService(currentRates: [
                'EUR' => ['USD' => '1.12345']
            ])),
        );

        $request = new CurrentConversionRequest(new Decimal('1'), 'EUR', 'USD');
        self::assertTrue($service->supports($request));

        $response = $service->send($request);
        self::assertInstanceOf(ConversionResponse::class, $response);
        self::assertEquals('1.12345', $response->amount->value);
    }

    public function testNotFound(): void
    {
        $service = new ChainService(
            new NullService(),
            new ArrayService(currentRates: [
                'EUR' => ['USD' => '1.12345']
            ]),
        );

        $request = new HistoricalExchangeRateRequest('EUR', 'USD', Date::today());
        self::assertFalse($service->supports($request));

        $response = $service->send($request);
        self::assertInstanceOf(ErrorResponse::class, $response);
        $exception = $response->exception;
        self::assertInstanceOf(NoSuitableServiceFoundException::class, $exception);
        self::assertEquals('No service in the chain could handle the request', $exception->getMessage());
        $exceptions = $exception->exceptions;
        self::assertInstanceOf(RequestNotSupportedException::class, $exceptions[0]);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $exceptions[1]);
        self::assertSame($exception->getPrevious(), $exceptions[0]);
    }

    public function testNoEmpty(): void
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage('List of services must be non-empty');

        new ChainService();
    }
}
