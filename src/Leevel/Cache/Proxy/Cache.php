<?php

declare(strict_types=1);

namespace Leevel\Cache\Proxy;

use Leevel\Cache\Manager;
use Leevel\Di\Container;

/**
 * 代理 cache.
 */
class Cache
{
    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('caches');
    }
}
