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

namespace Leevel\Manager;

use Closure;
use InvalidArgumentException;
use Leevel\Di\IContainer;

/**
 * 管理器.
 */
abstract class Manager
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * 连接对象.
     *
     * @var array
     */
    protected array $connects = [];

    /**
     * 扩展连接.
     *
     * @var array
     */
    protected array $extendConnect = [];

    /**
     * 过滤全局配置项.
     *
     * @var array
     */
    protected array $defaultCommonOption = [
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
     * call.
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
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
     * @@throws \InvalidArgumentException
     */
    public function connect(?string $connect = null, bool $onlyNew = false): object
    {
        if (!$connect) {
            $connect = $this->getDefaultConnect();
        }

        if (false === $onlyNew && isset($this->connects[$connect])) {
            return $this->connects[$connect];
        }

        if (!is_array($options = $this->getContainerOption('connect.'.$connect))) {
            $e = sprintf('Connection %s option is not an array.', $connect);

            throw new InvalidArgumentException($e);
        }

        if (!isset($options['driver'])) {
            $e = sprintf('Connection %s driver is not set.', $connect);

            throw new InvalidArgumentException($e);
        }

        $instance = $this->makeConnect($connect, $options['driver']);
        if (true === $onlyNew) {
            return $instance;
        }

        return $this->connects[$connect] = $instance;
    }

    /**
     * 重新连接.
     */
    public function reconnect(?string $connect = null): object
    {
        $this->disconnect($connect);

        return $this->connect($connect);
    }

    /**
     * 删除连接.
     */
    public function disconnect(?string $connect = null): void
    {
        if (!$connect) {
            $connect = $this->getDefaultConnect();
        }

        if (isset($this->connects[$connect])) {
            unset($this->connects[$connect]);
        }
    }

    /**
     * 取回所有连接.
     *
     * @return object[]
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
        return $this->getContainerOption('default');
    }

    /**
     * 设置默认连接.
     */
    public function setDefaultConnect(string $name): void
    {
        $this->setContainerOption('default', $name);
    }

    /**
     * 获取容器配置值.
     *
     * @return mixed
     */
    public function getContainerOption(?string $name = null)
    {
        $name = $this->getOptionName($name);

        return $this->container['option'][$name];
    }

    /**
     * 设置容器配置值.
     *
     * @param mixed $value
     */
    public function setContainerOption(string $name, $value): void
    {
        $name = $this->getOptionName($name);
        $this->container['option'][$name] = $value;
    }

    /**
     * 扩展自定义连接.
     */
    public function extend(string $connect, Closure $callback): void
    {
        $this->extendConnect[$connect] = $callback;
    }

    /**
     * 整理连接配置.
     */
    public function normalizeConnectOption(string $connect): array
    {
        return array_merge(
            $this->getConnectOption($connect),
            $this->getCommonOption()
        );
    }

    /**
     * 取得配置命名空间.
     */
    abstract protected function getOptionNamespace(): string;

    /**
     * 取得连接名字.
     */
    protected function getOptionName(?string $name = null): string
    {
        return $this->getOptionNamespace().'\\'.$name;
    }

    /**
     * 创建连接.
     *
     * @throws \InvalidArgumentException
     */
    protected function makeConnect(string $connect, string $driver): object
    {
        if (isset($this->extendConnect[$connect])) {
            return $this->extendConnect[$connect]($this);
        }

        if (method_exists($this, $makeDriver = 'makeConnect'.ucwords($driver))) {
            return $this->{$makeDriver}($connect);
        }

        $e = sprintf('Connection %s driver `%s` is invalid.', $connect, $driver);

        throw new InvalidArgumentException($e);
    }

    /**
     * 读取连接全局配置.
     */
    protected function getCommonOption(): array
    {
        return $this->filterCommonOption($this->getContainerOption());
    }

    /**
     * 过滤全局配置.
     */
    protected function filterCommonOption(array $options): array
    {
        foreach ($this->defaultCommonOption as $item) {
            if (isset($options[$item])) {
                unset($options[$item]);
            }
        }

        return $options;
    }

    /**
     * 分析连接配置.
     */
    protected function getConnectOption(string $connect): array
    {
        return $this->getContainerOption('connect.'.$connect);
    }

    /**
     * 清除配置 null.
     */
    protected function filterNullOfOption(array $options): array
    {
        return array_filter($options, function ($value) {
            return null !== $value;
        });
    }
}
