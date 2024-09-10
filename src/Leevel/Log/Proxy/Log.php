<?php

declare(strict_types=1);

namespace Leevel\Log\Proxy;

use Leevel\Di\Container;
use Leevel\Log\Manager;

/**
 * 代理 log.
 */
class Log
{
    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('logs');
    }
}
