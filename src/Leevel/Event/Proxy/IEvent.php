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

/**
 * 代理 event 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.26
 *
 * @version 1.0
 *
 * @see \Leevel\Event\IDispatch 请保持接口设计的一致
 */
interface IEvent
{
    /**
     * 执行一个事件.
     *
     * @param object|string $event
     * @param array         ...$params
     */
    public static function handle($event, ...$params): void;

    /**
     * 注册监听器.
     *
     * @param array|object|string $event
     * @param mixed               $listener
     * @param int                 $priority
     */
    public static function register($event, $listener, int $priority = 500): void;

    /**
     * 获取一个事件监听器.
     *
     * @param object|string $event
     *
     * @return array
     */
    public static function get($event): array;

    /**
     * 判断事件监听器是否存在.
     *
     * @param object|string $event
     *
     * @return bool
     */
    public static function has($event): bool;

    /**
     * 删除一个事件所有监听器.
     *
     * @param object|string $event
     */
    public static function delete($event): void;
}
