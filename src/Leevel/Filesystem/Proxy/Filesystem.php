<?php

declare(strict_types=1);

namespace Leevel\Filesystem\Proxy;

use Leevel\Di\Container;
use Leevel\Filesystem\Manager;

/**
 * 代理 filesystem.
 */
class Filesystem
{
    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('filesystems');
    }
}
