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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.12
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Event implements IEvent
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 执行一个事件.
     *
     * @param object|string $event
     * @param array         ...$params
     */
    public static function handle($event, ...$params): void
    {
        self::proxy()->handle($event, ...$params);
    }

    /**
     * 注册监听器.
     *
     * @param array|object|string $event
     * @param mixed               $listener
     * @param int                 $priority
     */
    public static function register($event, $listener, int $priority = 500): void
    {
        self::proxy()->register($event, $listener, $priority);
    }

    /**
     * 获取一个事件监听器.
     *
     * @param object|string $event
     *
     * @return array
     */
    public static function get($event): array
    {
        return self::proxy()->get($event);
    }

    /**
     * 判断事件监听器是否存在.
     *
     * @param object|string $event
     *
     * @return bool
     */
    public static function has($event): bool
    {
        return self::proxy()->has($event);
    }

    /**
     * 删除一个事件所有监听器.
     *
     * @param object|string $event
     */
    public static function delete($event): void
    {
        self::proxy()->delete($event);
    }

    /**
     * 代理服务.
     *
     * @return \Leevel\Event\Dispatch
     */
    public static function proxy(): Dispatch
    {
        return Container::singletons()->make('event');
    }
}
