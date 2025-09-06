<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Tests\SDK;

use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response;
use LogicException;
use Peso\Core\Services\SDK\Exceptions\HttpFailureException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use ValueError;

final class HttpFailureExceptionTest extends TestCase
{
    public function testCreateDirectly(): void
    {
        $e = new HttpFailureException(previous: new RuntimeException());

        self::assertEquals('', $e->getMessage());
        self::assertEquals(0, $e->getCode());
        self::assertInstanceOf(RuntimeException::class, $e->getPrevious());
        self::assertNull($e->request);
        self::assertNull($e->response);
    }

    public function testCreateFromRequestResponse(): void
    {
        $request = new Request();
        $response = new Response(status: 404);
        $response->getBody()->write('HTTP error message');

        $e = HttpFailureException::fromResponse($request, $response);
        self::assertEquals('HTTP error 404. Response is "HTTP error message"', $e->getMessage());
        self::assertEquals(404, $e->getCode());
        self::assertNull($e->getPrevious());
    }

    public function testCreateFromRequestResponseNotUnicode(): void
    {
        $request = new Request();
        $response = new Response(status: 404);
        $response->getBody()->write("\xff");
        $previous = new LogicException();

        $e = HttpFailureException::fromResponse($request, $response, $previous);
        self::assertEquals(
            'HTTP error 404. Response is "<invalid UTF-8> (please inspect the $response object)"',
            $e->getMessage(),
        );
        self::assertEquals(404, $e->getCode());
        self::assertInstanceOf(LogicException::class, $e->getPrevious());
    }

    public function testCreateFromRequestResponseLongMessage(): void
    {
        $request = new Request();
        $response = new Response(status: 500);
        $response->getBody()->write(str_repeat('long message ', 100));
        $previous = new ValueError();

        $e = HttpFailureException::fromResponse($request, $response, $previous);
        self::assertEquals('HTTP error 500. Response is "long message long message ' .
            'long message long message long message long message long message long mess..."', $e->getMessage());
        self::assertEquals(500, $e->getCode());
        self::assertInstanceOf(ValueError::class, $e->getPrevious());
    }
}
