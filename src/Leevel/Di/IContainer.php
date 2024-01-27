<?php

declare(strict_types=1);

namespace Leevel\Di;

use Psr\Container\ContainerInterface;

/**
 * IOC 容器接口.
 */
interface IContainer extends ContainerInterface
{
    /**
     * 是否起用协程.
     */
    public const ENABLED_COROUTINE = 'enabledCoroutine';

    /**
     * 注册的服务.
     *
     * - Registered services.
     */
    public const SERVICES = 'services';

    /**
     * 注册的实例.
     *
     * - Registered instances.
     */
    public const INSTANCES = 'instances';

    /**
     * 注册的单一实例.
     *
     * - Registered singletons.
     */
    public const SINGLETONS = 'singletons';

    /**
     * 生成 IOC 容器.
     */
    public static function singletons(): self;

    /**
     * 注册到容器.
     */
    public function bind(array|string $name, mixed $service = null, bool $share = false): self;

    /**
     * 注册为实例.
     */
    public function instance(array|string $name, mixed $service = null): self;

    /**
     * 注册单一实例.
     */
    public function singleton(array|string $name, mixed $service = null): self;

    /**
     * 设置别名.
     */
    public function alias(array|string $alias, null|array|string $value = null): self;

    /**
     * 创建容器服务并返回.
     */
    public function make(string $name, array $args = [], bool $throw = true): mixed;

    /**
     * 回调自动依赖注入.
     */
    public function call(array|callable|string $callback, array $args = []): mixed;

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
     * 添加上下文键值.
     */
    public function addContextKeys(string ...$contextKeys): void;

    /**
     * 清理上下文键值.
     */
    public function removeContextKeys(string ...$contextKeys): void;

    /**
     * 设置上下文解析回调.
     */
    public function setContextResolver(?\Closure $contextResolver = null): void;

    /**
     * 是否处于上下文中.
     */
    public function inContext(?string $name = null): bool;

    /**
     * 是否起用协程.
     */
    public function enabledCoroutine(): bool;

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
}
