<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Exceptions;

use Throwable;

/**
 * The request class is not recognized
 */
final class RequestNotSupportedException extends PesoResponseException
{
    public static function fromRequest(object $request, Throwable|null $previous = null): self
    {
        return new self(\sprintf('Unsupported request type: "%s"', get_debug_type($request)), previous: $previous);
    }
}
