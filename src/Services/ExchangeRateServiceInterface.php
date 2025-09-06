<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Services;

class_alias(PesoServiceInterface::class, ExchangeRateServiceInterface::class);

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
if (false) {
    /**
     * @deprecated PesoServiceInterface
     */
    interface ExchangeRateServiceInterface extends PesoServiceInterface
    {
    }
}
