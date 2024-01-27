<?php

declare(strict_types=1);

namespace Leevel\Di;

/**
 * 服务提供者.
 */
abstract class Provider
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 创建一个服务容器提供者实例.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
        $this->registerAlias();

        if ($this->container->enabledCoroutine()) {
            $this->registerContextKeys();
        }
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $args): void
    {
        if ('bootstrap' === $method) {
            return;
        }

        throw new \BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }

    /**
     * 注册服务.
     */
    abstract public function register(): void;

    /**
     * 注册服务别名.
     */
    public function registerAlias(): void
    {
        if (!static::isDeferred() && $providers = static::providers()) {
            $this->container->alias($providers);
        }
    }

    /**
     * 是否延迟载入.
     */
    public static function isDeferred(): bool
    {
        return false;
    }

    /**
     * 可用服务提供者.
     */
    public static function providers(): array
    {
        return [];
    }

    /**
     * 返回 IOC 容器.
     */
    public function container(): IContainer
    {
        return $this->container;
    }

    /**
     * 协程上下文键值.
     */
    public static function contextKeys(): array
    {
        return [];
    }

    /**
     * 注册服务别名.
     */
    protected function registerContextKeys(): void
    {
        if ($contextKeys = static::contextKeys()) {
            $this->container->addContextKeys(...$contextKeys);
        }
    }
}
