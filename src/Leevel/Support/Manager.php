<?php

declare(strict_types=1);

namespace Leevel\Support;

use Leevel\Di\IContainer;

/**
 * 管理器.
 */
abstract class Manager
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 连接对象.
     */
    protected array $connects = [];

    /**
     * 扩展连接.
     */
    protected array $extendConnect = [];

    /**
     * 过滤全局配置项.
     */
    protected array $defaultCommonConfig = [
        'default',
        'connect',
    ];

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->connect()->{$method}(...$args);
    }

    /**
     * 返回 IOC 容器.
     */
    public function container(): IContainer
    {
        return $this->container;
    }

    /**
     * 连接并返回连接对象.
     *
     * @throws \InvalidArgumentException
     */
    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): object
    {
        if (!$connect) {
            $connect = $this->getDefaultConnect();
        }

        $connect = $this->normalizeConnect($connect);

        if (false === $newConnect && isset($this->connects[$connect])) {
            return $this->connects[$connect];
        }

        if (isset($this->extendConnect[$connect])) {
            $instance = $this->extendConnect[$connect]($this, ...$arguments);
            if ($newConnect) {
                return $instance;
            }

            return $this->connects[$connect] = $instance;
        }

        if (!\is_array($configs = $this->getContainerConfig('connect.'.$connect))) {
            throw new \InvalidArgumentException(sprintf('Connection %s config is not an array.', $connect));
        }

        if (!isset($configs['driver'])) {
            throw new \InvalidArgumentException(sprintf('Connection %s driver is not set.', $connect));
        }

        $instance = $this->makeConnect($connect, $configs['driver'], $configs['driver_class'] ?? null, ...$arguments);
        if ($newConnect) {
            return $instance;
        }

        return $this->connects[$connect] = $instance;
    }

    /**
     * 重新连接.
     */
    public function reconnect(?string $connect = null, ...$arguments): object
    {
        $this->disconnect($connect);

        return $this->connect($connect, ...$arguments);
    }

    /**
     * 删除连接.
     */
    public function disconnect(?string $connect = null): void
    {
        if (!$connect) {
            $connect = $this->getDefaultConnect();
        }

        $connect = $this->normalizeConnect($connect);

        if (isset($this->connects[$connect])) {
            unset($this->connects[$connect]);
        }
    }

    /**
     * 取回所有连接.
     */
    public function getConnects(): array
    {
        return $this->connects;
    }

    /**
     * 返回默认连接.
     */
    public function getDefaultConnect(): string
    {
        return (string) $this->getContainerConfig('default');
    }

    /**
     * 设置默认连接.
     */
    public function setDefaultConnect(string $name): void
    {
        $this->setContainerConfig('default', $name);
    }

    /**
     * 获取容器配置值.
     */
    public function getContainerConfig(?string $name = null): mixed
    {
        $name = $this->getConfigName($name);

        return $this->container['config'][$name];
    }

    /**
     * 设置容器配置值.
     */
    public function setContainerConfig(string $name, mixed $value): void
    {
        $name = $this->getConfigName($name);
        $this->container['config'][$name] = $value;
    }

    /**
     * 扩展自定义连接.
     */
    public function extend(string $connect, \Closure $callback): void
    {
        $this->extendConnect[$connect] = $callback;
    }

    /**
     * 整理连接配置.
     */
    public function normalizeConnectConfig(string $connect): array
    {
        $configs = $this->getConnectConfig($connect);
        $configs = array_merge($this->getConnectConfig($configs['driver']), $configs);
        foreach ($this->getCommonConfig() as $k => $v) {
            if (!isset($configs[$k])) {
                $configs[$k] = $v;
            }
        }

        return $configs;
    }

    /**
     * 取得配置命名空间.
     */
    abstract protected function getConfigNamespace(): string;

    /**
     * 取得连接名字.
     */
    protected function getConfigName(?string $name = null): string
    {
        return $this->getConfigNamespace().'\\'.$name;
    }

    /**
     * 创建连接.
     *
     * @throws \InvalidArgumentException
     */
    protected function makeConnect(string $connect, string $driver, ?string $driverClass = null, ...$arguments): object
    {
        if (method_exists($this, $makeDriver = 'makeConnect'.ucwords($driver))) {
            return $this->{$makeDriver}($connect, $driverClass, ...$arguments);
        }

        throw new \InvalidArgumentException(sprintf('Connection %s driver `%s` is invalid.', $connect, $driver));
    }

    /**
     * 读取连接全局配置.
     */
    protected function getCommonConfig(): array
    {
        return $this->filterCommonConfig($this->getContainerConfig());
    }

    /**
     * 过滤全局配置.
     */
    protected function filterCommonConfig(array $configs): array
    {
        foreach ($this->defaultCommonConfig as $item) {
            if (isset($configs[$item])) {
                unset($configs[$item]);
            }
        }

        return $configs;
    }

    /**
     * 分析连接配置.
     */
    protected function getConnectConfig(string $connect): array
    {
        return (array) $this->getContainerConfig('connect.'.$connect);
    }

    /**
     * 清除配置 NULL 值.
     */
    protected function filterNullOfConfig(array $configs): array
    {
        return array_filter($configs, fn ($value): bool => null !== $value);
    }

    /**
     * 获取驱动类.
     */
    protected function getDriverClass(string $defaultDriverClass, ?string $driverClass = null): string
    {
        return $driverClass ?? $defaultDriverClass;
    }

    /**
     * 整理驱动.
     *
     * @throw new \InvalidArgumentException
     */
    protected function normalizeConnect(string $connect): string
    {
        if (!str_starts_with($connect, ':')) {
            return $connect;
        }

        // 动态链接支持，第一个字符为:开头作为标识
        // 动态链接字符串本身是一个普通函数，字符串返回值作为生成的动态链接
        $dynamicConnectFunction = substr($connect, 1);
        if (!\function_exists($dynamicConnectFunction)) {
            throw new \InvalidArgumentException(sprintf('Dynamic connect function `%s` was not found.', $dynamicConnectFunction));
        }

        $connect = $dynamicConnectFunction();
        if (!\is_string($connect)) {
            throw new \InvalidArgumentException(sprintf('Dynamic connect function `%s` must return string.', $dynamicConnectFunction));
        }

        return $connect;
    }
}
