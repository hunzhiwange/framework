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

namespace Leevel\Event;

/**
 * IDispatch 接口.
 */
interface IDispatch
{
    /**
     * 执行一个事件.
     *
     * @param object|string $event
     * @param array         ...$params
     */
    public function handle($event, ...$params): void;

    /**
     * 注册监听器.
     *
     * @param array|object|string $event
     * @param mixed               $listener
     */
    public function register($event, $listener, int $priority = 500): void;

    /**
     * 获取一个事件监听器.
     *
     * @param object|string $event
     */
    public function get($event): array;

    /**
     * 判断事件监听器是否存在.
     *
     * @param object|string $event
     */
    public function has($event): bool;

    /**
     * 删除事件所有监听器.
     *
     * @param object|string $event
     */
    public function delete($event): void;
}
