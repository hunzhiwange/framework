<?php

declare(strict_types=1);

namespace Leevel\View\Proxy;

use Leevel\Di\Container;
use Leevel\View\Manager;

/**
 * 代理 view.
 */
class View
{
    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('views');
    }
}
