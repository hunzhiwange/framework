<?php

declare(strict_types=1);

namespace Leevel\Config\Proxy;

use Leevel\Config\Config as BaseConfig;
use Leevel\Di\Container;

/**
 * 代理 config.
 */
class Config
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseConfig
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('config');
    }
}
