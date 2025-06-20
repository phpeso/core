<?php

declare(strict_types=1);

namespace Peso\Core\Responses;

use Arokettu\Date\Date;
use Peso\Core\Types\Decimal;

final readonly class SuccessResponse
{
    public function __construct(
        public Decimal $rate,
        public Date $date,
    ) {
    }
}
