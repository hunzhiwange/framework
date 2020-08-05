<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Event\Proxy;

use Leevel\Di\Container;
use Leevel\Event\Dispatch;

/**
 * 代理 event.
 *
 * @method static void handle(object|string $event, array ...$params)                       执行一个事件.
 * @method static void register(array|object|string $event, $listener, int $priority = 500) 注册监听器.
 * @method static array get(object|string $event)                                           获取一个事件监听器.
 * @method static bool has(object|string $event)                                            判断事件监听器是否存在.
 * @method static void delete(object|string $event)                                         删除一个事件所有监听器.
 */
class Event
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Dispatch
    {
        return Container::singletons()->make('event');
    }
}
