<?php

declare(strict_types=1);

namespace Peso\Core\Services\SDK\Exceptions;

use Exception;
use Peso\Core\Exceptions\RuntimeException;

/**
 * HTTP request failed
 */
final class HttpFailureException extends Exception implements RuntimeException
{
}
