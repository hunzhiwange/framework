<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Di\Container;

/**
 * 数据库组件 lazyload.
 */
class Lazyload
{
    /**
     * 延迟载入占位符.
     *
     * - 仅仅用于占位，必要时用于唤醒数据库服务提供者.
     */
    public static function placeholder(): bool
    {
        Container::singletons()->make('database.lazyload');

        return true;
    }
}
