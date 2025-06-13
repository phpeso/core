<?php

declare(strict_types=1);

namespace Peso\Core\Services;

use Peso\Core\Exceptions\RequestNotSupportedException;
use Peso\Core\Responses\ErrorResponse;

final readonly class NullService implements ExchangeRateServiceInterface
{
    public function send(object $request): ErrorResponse
    {
        return new ErrorResponse(RequestNotSupportedException::fromRequest($request));
    }

    public function supports(object $request): bool
    {
        return false;
    }
}
