<?php

declare(strict_types=1);

namespace Leevel\Auth\Proxy;

use Leevel\Auth\Manager;
use Leevel\Di\Container;

/**
 * 代理 auth.
 */
class Auth
{
    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('auths');
    }
}
