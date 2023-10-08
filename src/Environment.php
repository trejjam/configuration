<?php
declare(strict_types=1);

namespace Trejjam\Configuration;

final class Environment
{
    public function __construct(
        private readonly SiteMode $siteMode,
        private readonly bool   $isCli
    )
    {
    }

    public function getSiteMode(): SiteMode
    {
        return $this->siteMode;
    }

    public function isCli(): bool
    {
        return $this->isCli;
    }
}
