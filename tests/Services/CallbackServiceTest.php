<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Services;

use Arokettu\Date\Date;
use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Requests\CurrentConversionRequest;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Services\CallbackService;
use Peso\Core\Types\Decimal;
use PHPUnit\Framework\TestCase;

final class CallbackServiceTest extends TestCase
{
    public function testSomeCallback(): void
    {
        $service = new CallbackService(static function (object $request) {
            if (!$request instanceof CurrentConversionRequest) {
                return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
            }
            return new ConversionResponse($request->baseAmount, Date::today()); // always return at 1:1 rate
        }, static function (object $request) {
            return $request instanceof CurrentConversionRequest;
        });

        $convRequest = new CurrentConversionRequest(new Decimal('123'), 'USD', 'EUR');
        $rateRequest = new CurrentExchangeRateRequest('USD', 'EUR');

        self::assertTrue($service->supports($convRequest));
        self::assertFalse($service->supports($rateRequest));

        self::assertInstanceOf(ConversionResponse::class, $service->send($convRequest));
        self::assertInstanceOf(ErrorResponse::class, $service->send($rateRequest));
    }
}
