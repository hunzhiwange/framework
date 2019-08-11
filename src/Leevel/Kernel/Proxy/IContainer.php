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

namespace Leevel\Kernel\Proxy;

use Leevel\Di\IContainer as IBaseContainer;
use Leevel\Di\ICoroutine;
use Leevel\Di\Provider;

/**
 * 代理 container 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.11
 *
 * @version 1.0
 *
 * @see \Leevel\Di\IContainer 请保持接口设计的一致
 */
interface IContainer
{
    /**
     * 注册到容器.
     *
     * @param mixed      $name
     * @param null|mixed $service
     * @param bool       $share
     * @param bool       $coroutine
     *
     * @return \Leevel\Di\IContainer
     */
    public static function bind($name, $service = null, bool $share = false, bool $coroutine = false): IBaseContainer;

    /**
     * 注册为实例.
     *
     * @param mixed $name
     * @param mixed $service
     * @param bool  $coroutine
     *
     * @return \Leevel\Di\IContainer
     */
    public static function instance($name, $service, bool $coroutine = false): IBaseContainer;

    /**
     * 注册单一实例.
     *
     * @param array|scalar $name
     * @param null|mixed   $service
     * @param bool         $coroutine
     *
     * @return \Leevel\Di\IContainer
     */
    public static function singleton($name, $service = null, bool $coroutine = false): IBaseContainer;

    /**
     * 设置别名.
     *
     * @param array|string      $alias
     * @param null|array|string $value
     *
     * @return \Leevel\Di\IContainer
     */
    public static function alias($alias, $value = null): IBaseContainer;

    /**
     * 服务容器返回对象
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public static function make(string $name, array $args = []);

    /**
     * 实例回调自动注入.
     *
     * @param array|callable|string $callback
     * @param array                 $args
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public static function call($callback, array $args = []);

    /**
     * 删除服务和实例.
     *
     * @param string $name
     */
    public static function remove(string $name): void;

    /**
     * 服务或者实例是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function exists(string $name): bool;

    /**
     * 清理容器.
     */
    public static function clear(): void;

    /**
     * 执行 bootstrap.
     *
     * @param \Leevel\Di\Provider $provider
     */
    public static function callProviderBootstrap(Provider $provider): void;

    /**
     * 创建服务提供者.
     *
     * @param string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public static function makeProvider(string $provider): Provider;

    /**
     * 注册服务提供者.
     *
     * @param \Leevel\Di\Provider|string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public static function register($provider): Provider;

    /**
     * 是否已经初始化引导.
     *
     * @return bool
     */
    public static function isBootstrap(): bool;

    /**
     * 注册服务提供者.
     */
    public static function registerProviders(array $providers, array $deferredProviders = [], array $deferredAlias = []): void;

    /**
     * 设置协程.
     *
     * @param \Leevel\Di\ICoroutine $coroutine
     */
    public static function setCoroutine(ICoroutine $coroutine): void;

    /**
     * 返回协程.
     *
     * @return \Leevel\Di\ICoroutine
     */
    public static function getCoroutine(): ?ICoroutine;

    /**
     * 协程服务或者实例是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function existsCoroutine(string $name): bool;

    /**
     * 删除协程上下文服务和实例.
     *
     * @param null|string $name
     */
    public static function removeCoroutine(?string $name = null): void;

    /**
     * 设置服务到协程上下文.
     *
     * @param string $service
     */
    public static function serviceCoroutine(string $service): void;
}
