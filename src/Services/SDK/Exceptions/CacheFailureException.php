<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Services\SDK\Exceptions;

use Exception;
use Peso\Core\Exceptions\RuntimeException;

final class CacheFailureException extends Exception implements RuntimeException
{
}
