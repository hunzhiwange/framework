<?php

declare(strict_types=1);

namespace Leevel\Kernel\Proxy;

use Leevel\Di\Container;
use Leevel\Kernel\App as BaseApp;

/**
 * 代理 app.
 */
class App
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseApp
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('app');
    }
}
