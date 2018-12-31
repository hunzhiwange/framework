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

namespace Leevel\Di;

/**
 * IContainer 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.13
 *
 * @version 1.0
 */
interface IContainer
{
    /**
     * 注册到容器.
     *
     * @param mixed $name
     * @param mixed $service
     * @param bool  $share
     *
     * @return $this
     */
    public function bind($name, $service = null, bool $share = false);

    /**
     * 注册为实例.
     *
     * @param mixed $name
     * @param mixed $service
     */
    public function instance($name, $service);

    /**
     * 注册单一实例.
     *
     * @param string $name
     * @param mixed  $service
     */
    public function singleton($name, $service = null);

    /**
     * 设置别名.
     *
     * @param array|string      $alias
     * @param null|array|string $value
     */
    public function alias($alias, $value = null);

    /**
     * 服务容器返回对象
     *
     * @param string $name
     * @param array  $args
     *
     * @return false|object
     */
    public function make($name, array $args = []);

    /**
     * 实例回调自动注入.
     *
     * @param array|callable|string $callback
     * @param array                 $args
     *
     * @return mixed
     */
    public function call($callback, array $args = []);

    /**
     * 删除服务和实例.
     *
     * @param string $name
     */
    public function remove(string $name);

    /**
     * 删除协程上下文服务和实例.
     *
     * @param string $name
     */
    public function removeCoroutine(?string $name = null): void;

    /**
     * 服务或者实例是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists(string $name): bool;

    /**
     * 设置协程.
     *
     * @param \Leevel\Di\ICoroutine $coroutine
     */
    public function setCoroutine(ICoroutine $coroutine): void;

    /**
     * 返回协程.
     *
     * @return \Leevel\Di\ICoroutine
     */
    public function getCoroutine(): ?ICoroutine;
}
