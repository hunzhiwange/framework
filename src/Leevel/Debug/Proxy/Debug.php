<?php

declare(strict_types=1);

namespace Leevel\Debug\Proxy;

use Leevel\Debug\Debug as BaseDebug;
use Leevel\Di\Container;

/**
 * 代理 debug.
 */
class Debug
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseDebug
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('debug');
    }
}
