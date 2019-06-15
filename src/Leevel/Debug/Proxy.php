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

namespace Leevel\Debug;

use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DebugBar;
use DebugBar\HttpDriverInterface;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;
use DebugBar\RequestIdGeneratorInterface;
use DebugBar\Storage\StorageInterface;

/**
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.27
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait Proxy
{
    /**
     * 添加数据收集器.
     *
     * @param \DebugBar\DataCollector\DataCollectorInterface $collector
     *
     * @throws \DebugBar\DebugBarException
     *
     * @return \DebugBar\DebugBar
     */
    public function addCollector(DataCollectorInterface $collector): DebugBar
    {
        return $this->proxy()->addCollector($collector);
    }

    /**
     * 检查是否已添加数据收集器.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasCollector(string $name): bool
    {
        return $this->proxy()->hasCollector($name);
    }

    /**
     * 返回数据收集器.
     *
     * @param string $name
     *
     * @throws \DebugBar\DebugBarException
     *
     * @return \DebugBar\DataCollector\DataCollectorInterface
     */
    public function getCollector(string $name): DataCollectorInterface
    {
        return $this->proxy()->getCollector($name);
    }

    /**
     * 返回所有数据收集器的数组.
     *
     * @return \DebugBar\DataCollector\DataCollectorInterface[]
     */
    public function getCollectors(): array
    {
        return $this->proxy()->getCollectors();
    }

    /**
     * 设置请求 ID 生成器.
     *
     * @param \DebugBar\RequestIdGeneratorInterface $generator
     *
     * @return \DebugBar\DebugBar
     */
    public function setRequestIdGenerator(RequestIdGeneratorInterface $generator): DebugBar
    {
        return $this->proxy()->setRequestIdGenerator($generator);
    }

    /**
     * 返回请求 ID 生成器.
     *
     * @return \DebugBar\RequestIdGeneratorInterface
     */
    public function getRequestIdGenerator(): RequestIdGeneratorInterface
    {
        return $this->proxy()->getRequestIdGenerator();
    }

    /**
     * 返回当前请求的 ID.
     *
     * @return string
     */
    public function getCurrentRequestId(): string
    {
        return $this->proxy()->getCurrentRequestId();
    }

    /**
     * 设置用于存储收集数据的存储后端.
     *
     * @param null|\DebugBar\Storage\StorageInterface $storage
     *
     * @return \DebugBar\DebugBar
     */
    public function setStorage(StorageInterface $storage = null): DebugBar
    {
        return $this->proxy()->setStorage($storage);
    }

    /**
     * 返回用于存储收集数据的存储后端.
     *
     * @return \DebugBar\Storage\StorageInterface
     */
    public function getStorage(): StorageInterface
    {
        return $this->proxy()->getStorage();
    }

    /**
     * 检查是否保持数据.
     *
     * @return bool
     */
    public function isDataPersisted(): bool
    {
        return $this->proxy()->isDataPersisted();
    }

    /**
     * 设置 HTTP 驱动.
     *
     * @param \DebugBar\HttpDriverInterface $driver
     *
     * @return \DebugBar\DebugBar
     */
    public function setHttpDriver(HttpDriverInterface $driver): DebugBar
    {
        return $this->proxy()->setHttpDriver($driver);
    }

    /**
     * 返回 HTTP 驱动.
     *
     * 如果没有定义 HTTP 驱动，则会自动创建 \DebugBar\PhpHttpDriver.
     *
     * @return \DebugBar\HttpDriverInterface
     */
    public function getHttpDriver(): HttpDriverInterface
    {
        return $this->proxy()->getHttpDriver();
    }

    /**
     * 从收集器收集数据.
     *
     * @return array
     */
    public function collect(): array
    {
        return $this->proxy()->collect();
    }

    /**
     * 返回收集的数据.
     *
     * 如果尚未收集到数据，将收集数据.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->proxy()->getData();
    }

    /**
     * 返回包含数据的 HTTP 头数组.
     *
     * @param string $headerName
     * @param int    $maxHeaderLength
     * @param int    $maxTotalHeaderLength
     *
     * @return array
     */
    public function getDataAsHeaders(string $headerName = 'phpdebugbar', int $maxHeaderLength = 4096, int $maxTotalHeaderLength = 250000): array
    {
        return $this->proxy()->getDataAsHeaders($headerName, $maxHeaderLength, $maxTotalHeaderLength);
    }

    /**
     * 通过 HTTP 头数组发送数据.
     *
     * @param null|bool $useOpenHandler
     * @param string    $headerName
     * @param int       $maxHeaderLength
     *
     * @return \DebugBar\DebugBar
     */
    public function sendDataInHeaders(?bool $useOpenHandler = null, string $headerName = 'phpdebugbar', int $maxHeaderLength = 4096): DebugBar
    {
        return $this->proxy()->sendDataInHeaders($useOpenHandler, $headerName, $maxHeaderLength);
    }

    /**
     * 将数据存在 session 中.
     *
     * @return \DebugBar\DebugBar
     */
    public function stackData(): DebugBar
    {
        return $this->proxy()->stackData();
    }

    /**
     * 检查 session 中是否存在数据.
     *
     * @return bool
     */
    public function hasStackedData(): bool
    {
        return $this->proxy()->hasStackedData();
    }

    /**
     * 返回 session 中保存的数据.
     *
     * @param bool $delete
     *
     * @return array
     */
    public function getStackedData(bool $delete = true): array
    {
        return $this->proxy()->getStackedData($delete);
    }

    /**
     * 设置 session 中保存数据的 key.
     *
     * @param string $ns
     *
     * @return \DebugBar\DebugBar
     */
    public function setStackDataSessionNamespace(string $ns): DebugBar
    {
        return $this->proxy()->setStackDataSessionNamespace($ns);
    }

    /**
     * 获取 session 中保存数据的 key.
     *
     * @return string
     */
    public function getStackDataSessionNamespace(): string
    {
        return $this->proxy()->getStackDataSessionNamespace();
    }

    /**
     * 设置是否仅使用 session 来保存数据，即使已启用存储.
     *
     * @param bool $enabled
     *
     * @return \DebugBar\DebugBar
     */
    public function setStackAlwaysUseSessionStorage(bool $enabled = true): DebugBar
    {
        return $this->proxy()->setStackAlwaysUseSessionStorage($enabled);
    }

    /**
     * 检查 session 是否始终用于保存数据，即使已启用存储.
     *
     * @return bool
     */
    public function isStackAlwaysUseSessionStorage(): bool
    {
        return $this->proxy()->isStackAlwaysUseSessionStorage();
    }

    /**
     * 返回此实例的 \DebugBar\JavascriptRenderer.
     *
     * @param null|string $baseUrl
     * @param null|string $basePath
     *
     * @return \DebugBar\JavascriptRenderer
     */
    public function getJavascriptRenderer(?string $baseUrl = null, ?string $basePath = null): BaseJavascriptRenderer
    {
        return $this->proxy()->getJavascriptRenderer($baseUrl, $basePath);
    }
}
