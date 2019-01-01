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

namespace Leevel\Manager;

use Exception;
use Leevel\Di\IContainer;

/**
 * manager 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
 */
abstract class Manager
{
    /**
     * IOC Container.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 连接对象
     *
     * @var object[]
     */
    protected $connects = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->connect()->{$method}(...$args);
    }

    /**
     * 返回 IOC 容器.
     *
     * @return \Leevel\Di\IContainer
     */
    public function container(): IContainer
    {
        return $this->container;
    }

    /**
     * 连接 connect 并返回连接对象
     *
     * @param null|array|string $options
     *
     * @return object
     */
    public function connect($options = null)
    {
        list($options, $unique) = $this->parseOptionAndUnique($options);

        if (isset($this->connects[$unique])) {
            return $this->connects[$unique];
        }

        $driver = $options['driver'] ?? $this->getDefaultDriver();

        return $this->connects[$unique] = $this->makeConnect(
            $driver, $options
        );
    }

    /**
     * 重新连接.
     *
     * @param array|string $options
     *
     * @return object
     */
    public function reconnect($options = [])
    {
        $this->disconnect($options);

        return $this->connect($options);
    }

    /**
     * 删除连接.
     *
     * @param array|string $options
     */
    public function disconnect($options = []): void
    {
        list($options, $unique) = $this->parseOptionAndUnique($options);

        if (isset($this->connects[$unique])) {
            unset($this->connects[$unique]);
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
     * 返回默认驱动.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->getContainerOption('default');
    }

    /**
     * 设置默认驱动.
     *
     * @param string $name
     */
    public function setDefaultDriver(string $name): void
    {
        $this->setContainerOption('default', $name);
    }

    /**
     * 获取容器配置值.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getContainerOption(?string $name = null)
    {
        $name = $this->normalizeOptionName($name);

        return $this->container['option'][$name];
    }

    /**
     * 设置容器配置值.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setContainerOption(string $name, $value): void
    {
        $name = $this->normalizeOptionName($name);

        $this->container['option'][$name] = $value;
    }

    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    abstract protected function normalizeOptionNamespace(): string;

    /**
     * 创建连接对象
     *
     * @param object $connect
     *
     * @return object
     */
    abstract protected function createConnect($connect);

    /**
     * 取得连接名字.
     *
     * @param string $name
     *
     * @return string
     */
    protected function normalizeOptionName(?string $name = null): string
    {
        return $this->normalizeOptionNamespace().'\\'.$name;
    }

    /**
     * 创建连接.
     *
     * @param string $connect
     * @param array  $options
     *
     * @return object
     */
    protected function makeConnect(string $connect, array $options = [])
    {
        if (null === $this->getContainerOption('connect.'.$connect)) {
            throw new Exception(
                sprintf(
                    'Connect driver %s not exits.', $connect
                )
            );
        }

        return $this->createConnect(
            $this->createConnectCommon($connect, $options)
        );
    }

    /**
     * 创建连接对象公共入口.
     *
     * @param string $connect
     * @param array  $options
     *
     * @return object
     */
    protected function createConnectCommon(string $connect, array $options = [])
    {
        return $this->{'makeConnect'.ucwords($connect)}($options);
    }

    /**
     * 分析连接参数以及其唯一值
     *
     * @param array|string $options
     *
     * @return array
     */
    protected function parseOptionAndUnique($options = []): array
    {
        return [
            $options = $this->parseOptionParameter($options),
            $this->normalizeUnique($options),
        ];
    }

    /**
     * 分析连接参数.
     *
     * @param array|string $options
     *
     * @return array
     */
    protected function parseOptionParameter($options = []): array
    {
        if (null === $options) {
            return [];
        }

        if (is_string($options)) {
            $options = $this->getContainerOption('connect.'.$options);

            if (!is_array($options)) {
                return [];
            }
        }

        return $options;
    }

    /**
     * 取得唯一值
     *
     * @param array $options
     *
     * @return string
     */
    protected function normalizeUnique(array $options): string
    {
        return md5(serialize($options));
    }

    /**
     * 整理连接配置.
     *
     * @param string $connect
     * @param array  $extendOption
     *
     * @return array
     */
    protected function normalizeConnectOption(string $connect, array $extendOption = []): array
    {
        return array_merge(
            $this->getConnectOption($connect),
            $this->getCommonOption(),
            $extendOption
        );
    }

    /**
     * 读取连接全局配置.
     *
     * @return array
     */
    protected function getCommonOption(): array
    {
        return $this->filterCommonOption(
            $this->getContainerOption()
        );
    }

    /**
     * 过滤全局配置.
     *
     * @param array $options
     *
     * @return array
     */
    protected function filterCommonOption(array $options): array
    {
        foreach ($this->defaultCommonOption() as $item) {
            if (isset($options[$item])) {
                unset($options[$item]);
            }
        }

        return $options;
    }

    /**
     * 过滤全局配置项.
     *
     * @return array
     */
    protected function defaultCommonOption(): array
    {
        return [
            'default',
            'connect',
        ];
    }

    /**
     * 分析连接配置.
     *
     * @param string $connect
     *
     * @return array
     */
    protected function getConnectOption(string $connect): array
    {
        return $this->getContainerOption('connect.'.$connect);
    }

    /**
     * 清除配置 null.
     *
     * @param array $options
     *
     * @return array
     */
    protected function filterNullOfOption(array $options): array
    {
        return array_filter($options, function ($value) {
            return null !== $value;
        });
    }
}
