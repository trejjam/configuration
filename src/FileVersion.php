<?php
declare(strict_types=1);

namespace Trejjam\Configuration;

use DateTimeImmutable;

final class FileVersion
{
    public const UNSPECIFIED_VERSION = 'UNSPECIFIED_VERSION';

    public function __construct(
        private readonly string            $version,
        private readonly DateTimeImmutable $buildTime
    )
    {
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getBuildTime(): DateTimeImmutable
    {
        return $this->buildTime;
    }
}
