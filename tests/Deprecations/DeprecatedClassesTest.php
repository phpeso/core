<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Deprecations;

use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Responses\ConversionResponse;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\ExchangeRateResponse;
use Peso\Core\Services\ExchangeRateServiceInterface;
use Peso\Core\Services\PesoServiceInterface;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertInstanceOf;

final class DeprecatedClassesTest extends TestCase
{
    public function testExceptionAlias(): void
    {
        $request = new CurrentExchangeRateRequest('PHP', 'JPY');

        $exception = ConversionRateNotFoundException::fromRequest($request);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $exception);

        $exception = ExchangeRateNotFoundException::fromRequest($request);
        self::assertInstanceOf(ConversionRateNotFoundException::class, $exception);
    }

    public function testInterfaceAlias(): void
    {
        $service = new class implements PesoServiceInterface
        {
            public function send(object $request): ExchangeRateResponse|ConversionResponse|ErrorResponse
            {
                throw new \LogicException('not implemented');
            }

            public function supports(object $request): bool
            {
                throw new \LogicException('not implemented');
            }
        };

        assertInstanceOf(ExchangeRateServiceInterface::class, $service);

        $service = new class implements ExchangeRateServiceInterface
        {
            public function send(object $request): ExchangeRateResponse|ConversionResponse|ErrorResponse
            {
                throw new \LogicException('not implemented');
            }

            public function supports(object $request): bool
            {
                throw new \LogicException('not implemented');
            }
        };

        assertInstanceOf(PesoServiceInterface::class, $service);
    }
}
