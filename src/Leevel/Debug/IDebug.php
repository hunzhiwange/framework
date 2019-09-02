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

use Closure;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Throwable;

/**
 * IDebug 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.28
 *
 * @version 1.0
 *
 * @method static \DebugBar\DebugBar addCollector(\DebugBar\DataCollector\DataCollectorInterface $collector)                                          添加数据收集器.
 * @method static bool hasCollector(string $name)                                                                                                     检查是否已添加数据收集器.
 * @method static \DebugBar\DataCollector\DataCollectorInterface getCollector(string $name)                                                           返回数据收集器.
 * @method static array getCollectors()                                                                                                               返回所有数据收集器的数组.
 * @method static \DebugBar\DebugBar setRequestIdGenerator(\DebugBar\RequestIdGeneratorInterface $generator)                                          设置请求 ID 生成器.
 * @method static \DebugBar\RequestIdGeneratorInterface getRequestIdGenerator()                                                                       返回请求 ID 生成器.
 * @method static string getCurrentRequestId()                                                                                                        返回当前请求的 ID.
 * @method static \DebugBar\DebugBar setStorage(?\DebugBar\Storage\StorageInterface $storage = null)                                                  设置用于存储收集数据的存储后端.
 * @method static \DebugBar\Storage\StorageInterface getStorage()                                                                                     返回用于存储收集数据的存储后端.
 * @method static bool isDataPersisted()                                                                                                              检查是否保持数据.
 * @method static \DebugBar\DebugBar setHttpDriver(\DebugBar\HttpDriverInterface $driver)                                                             设置 HTTP 驱动.
 * @method static \DebugBar\HttpDriverInterface getHttpDriver()                                                                                       返回 HTTP 驱动.
 * @method static array collect()                                                                                                                     从收集器收集数据.
 * @method static array getData()                                                                                                                     返回收集的数据.
 * @method static array getDataAsHeaders(string $headerName = 'phpdebugbar', int $maxHeaderLength = 4096, int $maxTotalHeaderLength = 250000)         返回包含数据的 HTTP 头数组.
 * @method static \DebugBar\DebugBar sendDataInHeaders(?bool $useOpenHandler = null, string $headerName = 'phpdebugbar', int $maxHeaderLength = 4096) 通过 HTTP 头数组发送数据.
 * @method static \DebugBar\DebugBar stackData()                                                                                                      将数据存在 session 中.
 * @method static bool hasStackedData()                                                                                                               检查 session 中是否存在数据.
 * @method static array getStackedData(bool $delete = true)                                                                                           返回 session 中保存的数据.
 * @method static \DebugBar\DebugBar setStackDataSessionNamespace(string $ns)                                                                         设置 session 中保存数据的 key.
 * @method static string getStackDataSessionNamespace()                                                                                               获取 session 中保存数据的 key.
 * @method static \DebugBar\DebugBar setStackAlwaysUseSessionStorage(bool $enabled = true)                                                            设置是否仅使用 session 来保存数据，即使已启用存储.
 * @method static bool isStackAlwaysUseSessionStorage()                                                                                               检查 session 是否始终用于保存数据，即使已启用存储.
 */
interface IDebug
{
    /**
     * 返回此实例的 \DebugBar\JavascriptRenderer.
     *
     * @param null|string $baseUrl
     * @param null|string $basePath
     *
     * @return \DebugBar\JavascriptRenderer
     */
    public function getJavascriptRenderer(?string $baseUrl = null, ?string $basePath = null): BaseJavascriptRenderer;

    /**
     * 返回应用管理.
     *
     * @return \Leevel\Di\IContainer
     */
    public function getContainer(): IContainer;

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Debug\IDebug
     */
    public function setOption(string $name, $value): self;

    /**
     * 响应.
     *
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     */
    public function handle(IRequest $request, IResponse $response): void;

    /**
     * 关闭调试.
     */
    public function disable(): void;

    /**
     * 启用调试.
     */
    public function enable(): void;

    /**
     * 添加一条消息.
     *
     * @param mixed  $message
     * @param string $label
     */
    public function message($message, string $label = 'info'): void;

    /**
     * 添加一条 emergency 消息.
     *
     * @param mixed $message
     */
    public function emergency($message): void;

    /**
     * 添加一条 alert 消息.
     *
     * @param mixed $message
     */
    public function alert($message): void;

    /**
     * 添加一条 critical 消息.
     *
     * @param mixed $message
     */
    public function critical($message): void;

    /**
     * 添加一条 error 消息.
     *
     * @param mixed $message
     */
    public function error($message): void;

    /**
     * 添加一条 warning 消息.
     *
     * @param mixed $message
     */
    public function warning($message): void;

    /**
     * 添加一条 notice 消息.
     *
     * @param mixed $message
     */
    public function notice($message): void;

    /**
     * 添加一条 info 消息.
     *
     * @param mixed $message
     */
    public function info($message): void;

    /**
     * 添加一条 debug 消息.
     *
     * @param mixed $message
     */
    public function debug($message): void;

    /**
     * 添加一条 log 消息.
     *
     * @param mixed $message
     */
    public function log($message): void;

    /**
     * 开始调试时间.
     *
     * @param string      $name
     * @param null|string $label
     */
    public function time(string $name, ?string $label = null): void;

    /**
     * 停止调试时间.
     *
     * @param string $name
     */
    public function end(string $name): void;

    /**
     * 添加一个时间调试.
     *
     * @param string $label
     * @param float  $start
     * @param float  $end
     */
    public function addTime(string $label, float $start, float $end): void;

    /**
     * 调试闭包执行时间.
     *
     * @param string   $label
     * @param \Closure $closure
     */
    public function closureTime(string $label, Closure $closure): void;

    /**
     * 添加异常.
     *
     * @param \Throwable $e
     */
    public function exception(Throwable $e): void;

    /**
     * 获取 JSON 渲染.
     *
     * @return \Leevel\Debug\JsonRenderer
     */
    public function getJsonRenderer(): JsonRenderer;

    /**
     * 获取 Console 渲染.
     *
     * @return \Leevel\Debug\ConsoleRenderer
     */
    public function getConsoleRenderer(): ConsoleRenderer;

    /**
     * 初始化.
     */
    public function bootstrap(): void;

    /**
     * 是否初始化.
     *
     * @return bool
     */
    public function isBootstrap(): bool;
}
