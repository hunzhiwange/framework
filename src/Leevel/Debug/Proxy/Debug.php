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

namespace Leevel\Debug\Proxy;

use Closure;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DebugBar;
use DebugBar\HttpDriverInterface;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;
use DebugBar\RequestIdGeneratorInterface;
use DebugBar\Storage\StorageInterface;
use Leevel\Debug\ConsoleRenderer;
use Leevel\Debug\Debug as BaseDebug;
use Leevel\Debug\IDebug as BaseIDebug;
use Leevel\Debug\JsonRenderer;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Throwable;

/**
 * 代理 debug.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.09.20
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Debug implements IDebug
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 添加数据收集器.
     *
     * @param \DebugBar\DataCollector\DataCollectorInterface $collector
     *
     * @throws \DebugBar\DebugBarException
     *
     * @return \DebugBar\DebugBar
     */
    public static function addCollector(DataCollectorInterface $collector): DebugBar
    {
        return self::proxy()->addCollector($collector);
    }

    /**
     * 检查是否已添加数据收集器.
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasCollector(string $name): bool
    {
        return self::proxy()->hasCollector($name);
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
    public static function getCollector(string $name): DataCollectorInterface
    {
        return self::proxy()->getCollector($name);
    }

    /**
     * 返回所有数据收集器的数组.
     *
     * @return \DebugBar\DataCollector\DataCollectorInterface[]
     */
    public static function getCollectors(): array
    {
        return self::proxy()->getCollectors();
    }

    /**
     * 设置请求 ID 生成器.
     *
     * @param \DebugBar\RequestIdGeneratorInterface $generator
     *
     * @return \DebugBar\DebugBar
     */
    public static function setRequestIdGenerator(RequestIdGeneratorInterface $generator): DebugBar
    {
        return self::proxy()->setRequestIdGenerator($generator);
    }

    /**
     * 返回请求 ID 生成器.
     *
     * @return \DebugBar\RequestIdGeneratorInterface
     */
    public static function getRequestIdGenerator(): RequestIdGeneratorInterface
    {
        return self::proxy()->getRequestIdGenerator();
    }

    /**
     * 返回当前请求的 ID.
     *
     * @return string
     */
    public static function getCurrentRequestId(): string
    {
        return self::proxy()->getCurrentRequestId();
    }

    /**
     * 设置用于存储收集数据的存储后端.
     *
     * @param null|\DebugBar\Storage\StorageInterface $storage
     *
     * @return \DebugBar\DebugBar
     */
    public static function setStorage(?StorageInterface $storage = null): DebugBar
    {
        return self::proxy()->setStorage($storage);
    }

    /**
     * 返回用于存储收集数据的存储后端.
     *
     * @return \DebugBar\Storage\StorageInterface
     */
    public static function getStorage(): StorageInterface
    {
        return self::proxy()->getStorage();
    }

    /**
     * 检查是否保持数据.
     *
     * @return bool
     */
    public static function isDataPersisted(): bool
    {
        return self::proxy()->isDataPersisted();
    }

    /**
     * 设置 HTTP 驱动.
     *
     * @param \DebugBar\HttpDriverInterface $driver
     *
     * @return \DebugBar\DebugBar
     */
    public static function setHttpDriver(HttpDriverInterface $driver): DebugBar
    {
        return self::proxy()->setHttpDriver($driver);
    }

    /**
     * 返回 HTTP 驱动.
     *
     * 如果没有定义 HTTP 驱动，则会自动创建 \DebugBar\PhpHttpDriver.
     *
     * @return \DebugBar\HttpDriverInterface
     */
    public static function getHttpDriver(): HttpDriverInterface
    {
        return self::proxy()->getHttpDriver();
    }

    /**
     * 从收集器收集数据.
     *
     * @return array
     */
    public static function collect(): array
    {
        return self::proxy()->collect();
    }

    /**
     * 返回收集的数据.
     *
     * 如果尚未收集到数据，将收集数据.
     *
     * @return array
     */
    public static function getData(): array
    {
        return self::proxy()->getData();
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
    public static function getDataAsHeaders(string $headerName = 'phpdebugbar', int $maxHeaderLength = 4096, int $maxTotalHeaderLength = 250000): array
    {
        return self::proxy()->getDataAsHeaders($headerName, $maxHeaderLength, $maxTotalHeaderLength);
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
    public static function sendDataInHeaders(?bool $useOpenHandler = null, string $headerName = 'phpdebugbar', int $maxHeaderLength = 4096): DebugBar
    {
        return self::proxy()->sendDataInHeaders($useOpenHandler, $headerName, $maxHeaderLength);
    }

    /**
     * 将数据存在 session 中.
     *
     * @return \DebugBar\DebugBar
     */
    public static function stackData(): DebugBar
    {
        return self::proxy()->stackData();
    }

    /**
     * 检查 session 中是否存在数据.
     *
     * @return bool
     */
    public static function hasStackedData(): bool
    {
        return self::proxy()->hasStackedData();
    }

    /**
     * 返回 session 中保存的数据.
     *
     * @param bool $delete
     *
     * @return array
     */
    public static function getStackedData(bool $delete = true): array
    {
        return self::proxy()->getStackedData($delete);
    }

    /**
     * 设置 session 中保存数据的 key.
     *
     * @param string $ns
     *
     * @return \DebugBar\DebugBar
     */
    public static function setStackDataSessionNamespace(string $ns): DebugBar
    {
        return self::proxy()->setStackDataSessionNamespace($ns);
    }

    /**
     * 获取 session 中保存数据的 key.
     *
     * @return string
     */
    public static function getStackDataSessionNamespace(): string
    {
        return self::proxy()->getStackDataSessionNamespace();
    }

    /**
     * 设置是否仅使用 session 来保存数据，即使已启用存储.
     *
     * @param bool $enabled
     *
     * @return \DebugBar\DebugBar
     */
    public static function setStackAlwaysUseSessionStorage(bool $enabled = true): DebugBar
    {
        return self::proxy()->setStackAlwaysUseSessionStorage($enabled);
    }

    /**
     * 检查 session 是否始终用于保存数据，即使已启用存储.
     *
     * @return bool
     */
    public static function isStackAlwaysUseSessionStorage(): bool
    {
        return self::proxy()->isStackAlwaysUseSessionStorage();
    }

    /**
     * 返回此实例的 \DebugBar\JavascriptRenderer.
     *
     * @param null|string $baseUrl
     * @param null|string $basePath
     *
     * @return \DebugBar\JavascriptRenderer
     */
    public static function getJavascriptRenderer(?string $baseUrl = null, ?string $basePath = null): BaseJavascriptRenderer
    {
        return self::proxy()->getJavascriptRenderer($baseUrl, $basePath);
    }

    /**
     * 返回应用管理.
     *
     * @return \Leevel\Di\IContainer
     */
    public static function getContainer(): IContainer
    {
        return self::proxy()->getContainer();
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Debug\IDebug
     */
    public static function setOption(string $name, $value): BaseIDebug
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 响应.
     *
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     */
    public static function handle(IRequest $request, IResponse $response): void
    {
        self::proxy()->handle($request, $response);
    }

    /**
     * 关闭调试.
     */
    public static function disable(): void
    {
        self::proxy()->disable();
    }

    /**
     * 启用调试.
     */
    public static function enable(): void
    {
        self::proxy()->enable();
    }

    /**
     * 添加一条消息.
     *
     * @param mixed  $message
     * @param string $label
     */
    public static function message($message, string $label = 'info'): void
    {
        self::proxy()->message($message, $label);
    }

    /**
     * 添加一条 emergency 消息.
     *
     * @param mixed $message
     */
    public static function emergency($message): void
    {
        self::proxy()->emergency($message);
    }

    /**
     * 添加一条 alert 消息.
     *
     * @param mixed $message
     */
    public static function alert($message): void
    {
        self::proxy()->alert($message);
    }

    /**
     * 添加一条 critical 消息.
     *
     * @param mixed $message
     */
    public static function critical($message): void
    {
        self::proxy()->critical($message);
    }

    /**
     * 添加一条 error 消息.
     *
     * @param mixed $message
     */
    public static function error($message): void
    {
        self::proxy()->error($message);
    }

    /**
     * 添加一条 warning 消息.
     *
     * @param mixed $message
     */
    public static function warning($message): void
    {
        self::proxy()->warning($message);
    }

    /**
     * 添加一条 notice 消息.
     *
     * @param mixed $message
     */
    public static function notice($message): void
    {
        self::proxy()->notice($message);
    }

    /**
     * 添加一条 info 消息.
     *
     * @param mixed $message
     */
    public static function info($message): void
    {
        self::proxy()->info($message);
    }

    /**
     * 添加一条 debug 消息.
     *
     * @param mixed $message
     */
    public static function debug($message): void
    {
        self::proxy()->debug($message);
    }

    /**
     * 添加一条 log 消息.
     *
     * @param mixed $message
     */
    public static function log($message): void
    {
        self::proxy()->log($message);
    }

    /**
     * 开始调试时间.
     *
     * @param string      $name
     * @param null|string $label
     */
    public static function time(string $name, ?string $label = null): void
    {
        self::proxy()->time($name, $label);
    }

    /**
     * 停止调试时间.
     *
     * @param string $name
     */
    public static function end(string $name): void
    {
        self::proxy()->end($name);
    }

    /**
     * 添加一个时间调试.
     *
     * @param string $label
     * @param float  $start
     * @param float  $end
     */
    public static function addTime(string $label, float $start, float $end): void
    {
        self::proxy()->addTime($label, $start, $end);
    }

    /**
     * 调试闭包执行时间.
     *
     * @param string   $label
     * @param \Closure $closure
     */
    public static function closureTime(string $label, Closure $closure): void
    {
        self::proxy()->closureTime($label, $closure);
    }

    /**
     * 添加异常.
     *
     * @param \Throwable $e
     */
    public static function exception(Throwable $e): void
    {
        self::proxy()->exception($e);
    }

    /**
     * 获取 JSON 渲染.
     *
     * @return \Leevel\Debug\JsonRenderer
     */
    public static function getJsonRenderer(): JsonRenderer
    {
        return self::proxy()->getJsonRenderer();
    }

    /**
     * 获取 Console 渲染.
     *
     * @return \Leevel\Debug\ConsoleRenderer
     */
    public static function getConsoleRenderer(): ConsoleRenderer
    {
        return self::proxy()->getConsoleRenderer();
    }

    /**
     * 初始化.
     */
    public static function bootstrap(): void
    {
        self::proxy()->bootstrap();
    }

    /**
     * 是否初始化.
     *
     * @return bool
     */
    public static function isBootstrap(): bool
    {
        return self::proxy()->isBootstrap();
    }

    /**
     * 代理服务.
     *
     * @return \Leevel\Debug\Debug
     */
    public static function proxy(): BaseDebug
    {
        return Container::singletons()->make('debug');
    }
}
