<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\PesoException;
use Peso\Core\Requests\CurrentExchangeRateRequest;
use Peso\Core\Responses\ErrorResponse;
use Peso\Core\Responses\SuccessResponse;
use Peso\Core\Types\Decimal;

interface ExchangeRateServiceInterface
{
    public function send(object $request): SuccessResponse|ErrorResponse;

    public function supports(object $request): bool;
}
