<?php

declare(strict_types=1);

namespace Peso\Core\Tests\Deprecations;

use Peso\Core\Exceptions\ConversionRateNotFoundException;
use Peso\Core\Exceptions\ExchangeRateNotFoundException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use PHPUnit\Framework\TestCase;

final class DeprecatedExceptionTest extends TestCase
{
    public function testAlias(): void
    {
        $request = new CurrentExchangeRateRequest('PHP', 'JPY');

        $exception = ConversionRateNotFoundException::fromRequest($request);
        self::assertInstanceOf(ExchangeRateNotFoundException::class, $exception);

        $exception = ExchangeRateNotFoundException::fromRequest($request);
        self::assertInstanceOf(ConversionRateNotFoundException::class, $exception);
    }
}
