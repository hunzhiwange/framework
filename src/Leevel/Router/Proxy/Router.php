<?php

declare(strict_types=1);

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Router\Router as BaseRouter;

/**
 * 代理 router.
 */
class Router
{
    /**
     * 代理服务.
     */
    public static function proxy(): BaseRouter
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('router');
    }
}
