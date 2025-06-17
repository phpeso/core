<?php

declare(strict_types=1);

namespace Peso\Core\Services\SDK\HTTP;

use Composer\InstalledVersions;

final readonly class UserAgentHelper
{
    public static function buildUserAgentString(
        string|null $clientTitle = 'Client',
        string|null $clientPackage = null,
        string|null $extra = null,
        bool $versions = true,
    ): string {
        $pesoVersion = null;
        $clientVersion = null;
        if ($versions && class_exists(InstalledVersions::class)) {
            $pesoVersion = InstalledVersions::getPrettyVersion('peso/core');
            if ($clientPackage) {
                $clientVersion = InstalledVersions::getPrettyVersion($clientPackage);
            }
        }

        $peso = $pesoVersion === null ? 'Peso' : "Peso/$pesoVersion";
        if ($clientTitle !== null) {
            $client = $clientVersion === null ? " $clientTitle" : " $clientTitle/$clientVersion";
        } else {
            $client = '';
        }
        if ($extra !== null) {
            $extra = " $extra";
        }

        return "$peso$client$extra";
    }
}
