<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Date;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Requests\HistoricalExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Services\NullService;
use PHPUnit\Framework\TestCase;
use stdClass;

// Cover everything it touches
final class NullServiceTest extends TestCase
{
    public function testSupportsNothing(): void
    {
        $service = new NullService();

        $request = new CurrentExchangeRateRequest('EUR', 'USD');
        self::assertFalse($service->supports($request));
        $error = $service->send($request);
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(RequestNotSupportedException::class, $error->exception);
        self::assertEquals(
            'Unsupported request type: "Peso\Core\Requests\CurrentExchangeRateRequest"',
            $error->exception->getMessage(),
        );

        $request = new HistoricalExchangeRateRequest('EUR', 'USD', Date::today());
        self::assertFalse($service->supports($request));
        $error = $service->send($request);
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(RequestNotSupportedException::class, $error->exception);
        self::assertEquals(
            'Unsupported request type: "Peso\Core\Requests\HistoricalExchangeRateRequest"',
            $error->exception->getMessage(),
        );

        $request = new stdClass();
        self::assertFalse($service->supports($request));
        $error = $service->send($request);
        self::assertInstanceOf(ErrorResponse::class, $error);
        self::assertInstanceOf(RequestNotSupportedException::class, $error->exception);
        self::assertEquals('Unsupported request type: "stdClass"', $error->exception->getMessage());
    }
}
