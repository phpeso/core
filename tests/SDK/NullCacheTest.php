<?php

declare(strict_types=1);

namespace Peso\Core\Tests\SDK;

use Peso\Core\Services\SDK\Cache\NullCache;
use PHPUnit\Framework\TestCase;

final class NullCacheTest extends TestCase
{
    public function testNullCache(): void
    {
        $cache = new NullCache();

        self::assertTrue($cache->set('anything', 'value', new \DateInterval('P1D')));
        self::assertTrue($cache->setMultiple(['a' => 123, 'b' => 456]));
        self::assertFalse($cache->has('anything'));
        self::assertNan($cache->get('anything', NAN));
        self::assertEquals(['a' => false, 'b' => false], iterator_to_array($cache->getMultiple(['a', 'b'], false)));
        self::assertTrue($cache->delete('anything'));
        self::assertTrue($cache->deleteMultiple(['a', 'b']));
        self::assertTrue($cache->clear());
    }
}
