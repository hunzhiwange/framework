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
     * 默认协程 ID 标识.
     */
    const DEFAULT_COROUTINE_ID = 0;

    /**
     * 非协程 ID 标识.
     */
    const NOT_COROUTINE_ID = -1;

    /**
     * 注册到容器.
     */
    public function bind(mixed $name, mixed $service = null, bool $share = false, bool $coroutine = false): self;

    /**
     * 注册为实例.
     */
    public function instance(mixed $name, mixed $service = null, int $cid = self::NOT_COROUTINE_ID): self;

    /**
     * 注册单一实例.
     */
    public function singleton(mixed $name, mixed $service = null, bool $coroutine = false): self;

    /**
     * 设置别名.
     */
    public function alias(array|string $alias, null|array|string $value = null): self;

    /**
     * 创建容器服务并返回.
     */
    public function make(string $name, array $args = [], int $cid = self::DEFAULT_COROUTINE_ID): mixed;

    /**
     * 回调自动依赖注入.
     *
     * @throws \InvalidArgumentException
     */
    public function call(array|callable|string $callback, array $args = []): mixed;

    /**
     * 删除服务和实例.
     */
    public function remove(string $name, int $cid = self::DEFAULT_COROUTINE_ID): void;

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
     */
    public function callProviderBootstrap(Provider $provider): void;

    /**
     * 创建服务提供者.
     */
    public function makeProvider(string $provider): Provider;

    /**
     * 注册服务提供者.
     */
    public function register(Provider|string $provider): Provider;

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
     */
    public function setCoroutine(ICoroutine $coroutine): void;

    /**
     * 返回协程.
     */
    public function getCoroutine(): ?ICoroutine;

    /**
     * 协程服务或者实例是否存在.
     */
    public function existsCoroutine(string $name, int $cid = self::DEFAULT_COROUTINE_ID): bool;

    /**
     * 删除协程上下文服务和实例.
     */
    public function removeCoroutine(?string $name = null, int $cid = self::DEFAULT_COROUTINE_ID): void;

    /**
     * 设置服务到协程上下文.
     */
    public function serviceCoroutine(string $service): void;
}
