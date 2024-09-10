<?php

declare(strict_types=1);

namespace Leevel\Event\Proxy;

use Leevel\Di\Container;
use Leevel\Event\Dispatch;

/**
 * 代理 event.
 */
class Event
{
    /**
     * 代理服务.
     */
    public static function proxy(): Dispatch
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('event');
    }
}
