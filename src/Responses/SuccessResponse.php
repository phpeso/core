<?php

declare(strict_types=1);

namespace Peso\Core\Responses;

class_alias(ExchangeRateResponse::class, SuccessResponse::class);

if (false) {
    /**
     * @deprecated Replaced by ExchangeRateResponse
     */
    final class SuccessResponse extends ExchangeRateResponse
    {
    }
}
