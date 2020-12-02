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

namespace Leevel\Debug\Proxy;

use Leevel\Debug\Debug as BaseDebug;
use Leevel\Di\Container;

/**
 * 代理 debug.
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
 * @method static \DebugBar\JavascriptRenderer getJavascriptRenderer(?string $baseUrl = null, ?string $basePath = null)                               返回此实例的 \DebugBar\JavascriptRenderer.
 * @method static \Leevel\Di\IContainer getContainer()                                                                                                返回应用.
 * @method static void handle(\Leevel\Http\Request $request, \Symfony\Component\HttpFoundation\Response $response)                                    响应.
 * @method static void disable()                                                                                                                      关闭调试.
 * @method static void enable()                                                                                                                       启用调试.
 * @method static void message($message, string $label = 'info')                                                                                      添加一条消息.
 * @method static void emergency($message)                                                                                                            添加一条 emergency 消息.
 * @method static void alert($message)                                                                                                                添加一条 alert 消息.
 * @method static void critical($message)                                                                                                             添加一条 critical 消息.
 * @method static void error($message)                                                                                                                添加一条 error 消息.
 * @method static void warning($message)                                                                                                              添加一条 warning 消息.
 * @method static void notice($message)                                                                                                               添加一条 notice 消息.
 * @method static void info($message)                                                                                                                 添加一条 info 消息.
 * @method static void debug($message)                                                                                                                添加一条 debug 消息.
 * @method static void log($message)                                                                                                                  添加一条 log 消息.
 * @method static void time(string $name, ?string $label = null)                                                                                      开始调试时间.
 * @method static void end(string $name)                                                                                                              停止调试时间.
 * @method static void addTime(string $label, float $start, float $end)                                                                               添加一个时间调试.
 * @method static void closureTime(string $label, \Closure $closure)                                                                                  调试闭包执行时间.
 * @method static void exception(\Throwable $e)                                                                                                       添加异常.
 * @method static \Leevel\Debug\JsonRenderer getJsonRenderer()                                                                                        获取 JSON 渲染.
 * @method static \Leevel\Debug\ConsoleRenderer getConsoleRenderer()                                                                                  获取 Console 渲染.
 * @method static void bootstrap()                                                                                                                    初始化.
 * @method static bool isBootstrap()                                                                                                                  是否初始化.
 */
class Debug
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseDebug
    {
        return Container::singletons()->make('debug');
    }
}
