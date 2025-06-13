<?php

namespace Peso\Core\Responses;

use Peso\Core\Types\Decimal;

final readonly class SuccessResponse
{
    public function __construct(public Decimal $rate)
    {
    }
}
