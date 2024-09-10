<?php

declare(strict_types=1);

namespace Leevel\Database\Proxy;

use Leevel\Database\Manager;
use Leevel\Di\Container;

/**
 * 代理 database.
 */
class Db
{
    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('databases');
    }
}
