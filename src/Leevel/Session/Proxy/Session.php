<?php

declare(strict_types=1);

namespace Leevel\Session\Proxy;

use Leevel\Di\Container;
use Leevel\Session\Manager;

/**
 * 代理 session.
 */
class Session
{
    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('sessions');
    }
}
