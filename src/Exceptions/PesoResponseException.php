<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Exceptions;

use Exception;

/**
 * Base exception for valid-ish situations during the resolution (ErrorResponse)
 */
abstract class PesoResponseException extends Exception
{
}
