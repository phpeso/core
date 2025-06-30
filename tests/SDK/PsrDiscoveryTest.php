<?php

declare(strict_types=1);

namespace Peso\Core\Tests\SDK;

use Closure;
use Http\Discovery\ClassDiscovery;
use Http\Discovery\Psr18Client;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Mock\Client;
use Laminas\Diactoros\Request;
use Laminas\Diactoros\Response;
use Peso\Core\Services\SDK\HTTP\DiscoveredHttpClient;
use Peso\Core\Services\SDK\HTTP\DiscoveredRequestFactory;
use Peso\Core\Services\SDK\HTTP\DiscoveredUriFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

final class PsrDiscoveryTest extends TestCase
{
    private array $strategies;

    public function setUp(): void
    {
        $this->strategies = ClassDiscovery::getStrategies();
    }

    protected function tearDown(): void
    {
        ClassDiscovery::setStrategies($this->strategies);
    }

    public function testUriFactory(): void
    {
        $uri = new DiscoveredUriFactory();
        self::assertInstanceOf(UriFactoryInterface::class, $uri);
        self::assertInstanceOf(UriInterface::class, $uri->createUri('http://localhost'));
    }

    public function testRequestFactory(): void
    {
        $request = new DiscoveredRequestFactory();
        self::assertInstanceOf(RequestFactoryInterface::class, $request);
        self::assertInstanceOf(RequestInterface::class, $request->createRequest('GET', 'http://localhost'));
    }

    public function testHttpClient(): void
    {
        ClassDiscovery::prependStrategy(MockClientStrategy::class);

        $http = new DiscoveredHttpClient();
        self::assertInstanceOf(ClientInterface::class, $http);

        /** @var Psr18Client $psrClient */
        $psrClient = Closure::bind(fn () => $this->innerClient, $http, $http)();
        /** @var Client $mockClient */
        $mockClient = Closure::bind(fn () => $this->client, $psrClient, $psrClient)();

        $mockClient->setDefaultResponse(new Response());

        self::assertInstanceOf(ResponseInterface::class, $http->sendRequest(new Request()));
    }
}
