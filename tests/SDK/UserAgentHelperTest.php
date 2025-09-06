<?php

/**
 * @copyright 2025 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Peso\Core\Tests\SDK;

use Composer\InstalledVersions;
use Peso\Core\Services\SDK\HTTP\UserAgentHelper;
use PHPUnit\Framework\TestCase;

final class UserAgentHelperTest extends TestCase
{
    public function testUAStrings(): void
    {
        if (!class_exists(InstalledVersions::class)) {
            self::markTestSkipped('Not composer v2?');
        }

        $pesoVersion = InstalledVersions::getPrettyVersion('peso/core');
        $clientVersion = InstalledVersions::getPrettyVersion('phpunit/phpunit'); // some always present package

        self::assertEquals(
            "Peso/$pesoVersion PHPUnit/$clientVersion",
            UserAgentHelper::buildUserAgentString('PHPUnit', 'phpunit/phpunit'),
        );
        self::assertEquals(
            "Peso/$pesoVersion PHPUnit/$clientVersion SomeExtra",
            UserAgentHelper::buildUserAgentString('PHPUnit', 'phpunit/phpunit', 'SomeExtra'),
        );

        // no package = no client version
        self::assertEquals(
            "Peso/$pesoVersion PHPUnit SomeExtra",
            UserAgentHelper::buildUserAgentString('PHPUnit', extra: 'SomeExtra'),
        );

        // force no client
        self::assertEquals(
            "Peso/$pesoVersion",
            UserAgentHelper::buildUserAgentString(null, 'phpunit/phpunit'),
        );
        self::assertEquals(
            "Peso/$pesoVersion SomeExtra",
            UserAgentHelper::buildUserAgentString(null, 'phpunit/phpunit', 'SomeExtra'),
        );
    }

    public function testUAStringsWithoutVersions(): void
    {
        self::assertEquals(
            'Peso PHPUnit',
            UserAgentHelper::buildUserAgentString('PHPUnit', 'phpunit/phpunit', versions: false),
        );
        self::assertEquals(
            'Peso PHPUnit SomeExtra',
            UserAgentHelper::buildUserAgentString('PHPUnit', 'phpunit/phpunit', 'SomeExtra', versions: false),
        );

        // no package = no client version
        self::assertEquals(
            'Peso PHPUnit SomeExtra',
            UserAgentHelper::buildUserAgentString('PHPUnit', extra: 'SomeExtra', versions: false),
        );

        // force no client
        self::assertEquals(
            'Peso',
            UserAgentHelper::buildUserAgentString(null, 'phpunit/phpunit', versions: false),
        );
        self::assertEquals(
            'Peso SomeExtra',
            UserAgentHelper::buildUserAgentString(null, 'phpunit/phpunit', 'SomeExtra', versions: false),
        );
    }
}
