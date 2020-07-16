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

namespace Leevel\Di;

/**
 * IContainer 接口.
 */
interface IContainer
{
    /**
     * 注册到容器.
     *
     * @param mixed $name
     * @param mixed $service
     *
     * @return \Leevel\Di\IContainer
     */
    public function bind($name, $service = null, bool $share = false, bool $coroutine = false): self;

    /**
     * 注册为实例.
     *
     * @param mixed $name
     * @param mixed $service
     *
     * @return \Leevel\Di\IContainer
     */
    public function instance($name, $service, bool $coroutine = false): self;

    /**
     * 注册单一实例.
     *
     * @param mixed $name
     * @param mixed $service
     *
     * @return \Leevel\Di\IContainer
     */
    public function singleton($name, $service = null, bool $coroutine = false): self;

    /**
     * 设置别名.
     *
     * @param array|string      $alias
     * @param null|array|string $value
     *
     * @return \Leevel\Di\IContainer
     */
    public function alias($alias, $value = null): self;

    /**
     * 服务容器返回对象
     *
     * @return mixed
     */
    public function make(string $name, array $args = []);

    /**
     * 回调自动依赖注入.
     *
     * @param array|callable|string $callback
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function call($callback, array $args = []);

    /**
     * 删除服务和实例.
     */
    public function remove(string $name): void;

    /**
     * 服务或者实例是否存在.
     */
    public function exists(string $name): bool;

    /**
     * 清理容器.
     */
    public function clear(): void;

    /**
     * 执行服务提供者 bootstrap.
     *
     * @param \Leevel\Di\Provider $provider
     */
    public function callProviderBootstrap(Provider $provider): void;

    /**
     * 创建服务提供者.
     *
     * @return \Leevel\Di\Provider
     */
    public function makeProvider(string $provider): Provider;

    /**
     * 注册服务提供者.
     *
     * @param \Leevel\Di\Provider|string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function register($provider): Provider;

    /**
     * 是否已经初始化引导.
     */
    public function isBootstrap(): bool;

    /**
     * 注册服务提供者.
     */
    public function registerProviders(array $providers, array $deferredProviders = [], array $deferredAlias = []): void;

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

    /**
     * 协程服务或者实例是否存在.
     */
    public function existsCoroutine(string $name): bool;

    /**
     * 删除协程上下文服务和实例.
     */
    public function removeCoroutine(?string $name = null): void;

    /**
     * 设置服务到协程上下文.
     */
    public function serviceCoroutine(string $service): void;
}
