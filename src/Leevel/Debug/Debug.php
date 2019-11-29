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
use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;
use Exception;
use Leevel\Database\IDatabase;
use Leevel\Debug\DataCollector\FilesCollector;
use Leevel\Debug\DataCollector\LeevelCollector;
use Leevel\Debug\DataCollector\LogsCollector;
use Leevel\Debug\DataCollector\SessionCollector;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Http\ApiResponse;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Throwable;

/**
 * 调试器.
 *
 * I actually copied a lot of ideas from laravel-debugbar app.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
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
class Debug implements IDebug
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * DebugBar.
     *
     * @var \DebugBar\DebugBar
     */
    protected DebugBar $debugBar;

    /**
     * 是否启用调试.
     *
     * @var bool
     */
    protected bool $enabled = true;

    /**
     * 是否已经初始化引导
     *
     * @var bool
     */
    protected bool $isBootstrap = false;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'json'       => true,
        'console'    => true,
        'javascript' => true,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     * @param array                 $option
     */
    public function __construct(IContainer $container, array $option = [])
    {
        $this->container = $container;
        $this->option = array_merge($this->option, $option);
        $this->debugBar = new DebugBar();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    public function __call(string $method, array $args)
    {
        return $this->debugBar->{$method}(...$args);
    }

    /**
     * 返回应用管理.
     *
     * @return \Leevel\Di\IContainer
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Debug\IDebug
     */
    public function setOption(string $name, $value): IDebug
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 响应.
     *
     * @param \Leevel\Http\IRequest  $request
     * @param \Leevel\Http\IResponse $response
     */
    public function handle(IRequest $request, IResponse $response): void
    {
        if (!$this->enabled) {
            return;
        }

        if ($request->isJson() ||
            $response instanceof ApiResponse ||
            $response instanceof JsonResponse ||
            $response->isJson()) {
            if ($this->option['json'] && is_array($data = $response->getData())) {
                $jsonRenderer = $this->getJsonRenderer();
                if (array_values($data) !== $data) {
                    $data[':trace'] = $jsonRenderer->render();
                } else {
                    $data[] = [':trace' => $jsonRenderer->render()];
                }
                $response->setData($data);
            }
        } elseif (!($response instanceof RedirectResponse)) {
            if ($this->option['javascript']) {
                $javascriptRenderer = $this->getJavascriptRenderer('/debugbar');
                $response->appendContent(
                    $javascriptRenderer->renderHead().$javascriptRenderer->render()
                );
            }

            if ($this->option['console']) {
                $consoleRenderer = $this->getConsoleRenderer();
                $response->appendContent($consoleRenderer->render());
            }
        }
    }

    /**
     * 关闭调试.
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * 启用调试.
     */
    public function enable(): void
    {
        $this->enabled = true;
        if (!$this->isBootstrap) {
            $this->bootstrap();
        }
    }

    /**
     * 添加一条消息.
     *
     * @param mixed  $message
     * @param string $label
     */
    public function message($message, string $label = 'info'): void
    {
        $this->getCollector('messages')->addMessage($message, $label);
    }

    /**
     * 添加一条 emergency 消息.
     *
     * @param mixed $message
     */
    public function emergency($message): void
    {
        $this->message($message, 'emergency');
    }

    /**
     * 添加一条 alert 消息.
     *
     * @param mixed $message
     */
    public function alert($message): void
    {
        $this->message($message, 'alert');
    }

    /**
     * 添加一条 critical 消息.
     *
     * @param mixed $message
     */
    public function critical($message): void
    {
        $this->message($message, 'critical');
    }

    /**
     * 添加一条 error 消息.
     *
     * @param mixed $message
     */
    public function error($message): void
    {
        $this->message($message, 'error');
    }

    /**
     * 添加一条 warning 消息.
     *
     * @param mixed $message
     */
    public function warning($message): void
    {
        $this->message($message, 'warning');
    }

    /**
     * 添加一条 notice 消息.
     *
     * @param mixed $message
     */
    public function notice($message): void
    {
        $this->message($message, 'notice');
    }

    /**
     * 添加一条 info 消息.
     *
     * @param mixed $message
     */
    public function info($message): void
    {
        $this->message($message, 'info');
    }

    /**
     * 添加一条 debug 消息.
     *
     * @param mixed $message
     */
    public function debug($message): void
    {
        $this->message($message, 'debug');
    }

    /**
     * 添加一条 log 消息.
     *
     * @param mixed $message
     */
    public function log($message): void
    {
        $this->message($message, 'log');
    }

    /**
     * 开始调试时间.
     *
     * @param string      $name
     * @param null|string $label
     */
    public function time(string $name, ?string $label = null): void
    {
        $this->getCollector('time')->startMeasure($name, $label);
    }

    /**
     * 停止调试时间.
     *
     * @param string $name
     */
    public function end(string $name): void
    {
        try {
            $this->getCollector('time')->stopMeasure($name);
        } catch (Exception $e) {
        }
    }

    /**
     * 添加一个时间调试.
     *
     * @param string $label
     * @param float  $start
     * @param float  $end
     */
    public function addTime(string $label, float $start, float $end): void
    {
        $this->getCollector('time')->addMeasure($label, $start, $end);
    }

    /**
     * 调试闭包执行时间.
     *
     * @param string   $label
     * @param \Closure $closure
     */
    public function closureTime(string $label, Closure $closure): void
    {
        $this->getCollector('time')->measure($label, $closure);
    }

    /**
     * 添加异常.
     *
     * @param \Throwable $e
     */
    public function exception(Throwable $e): void
    {
        $this->getCollector('exceptions')->addThrowable($e);
    }

    /**
     * 获取 JSON 渲染.
     *
     * @return \Leevel\Debug\JsonRenderer
     */
    public function getJsonRenderer(): JsonRenderer
    {
        return new JsonRenderer($this);
    }

    /**
     * 获取 Console 渲染.
     *
     * @return \Leevel\Debug\ConsoleRenderer
     */
    public function getConsoleRenderer(): ConsoleRenderer
    {
        return new ConsoleRenderer($this);
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
        return new JavascriptRenderer($this->debugBar, $baseUrl, $basePath);
    }

    /**
     * 初始化.
     */
    public function bootstrap(): void
    {
        if ($this->isBootstrap) {
            return;
        }

        $this->isBootstrap = true;
        $this->addCollector(new PhpInfoCollector());
        $this->addCollector(new MessagesCollector());
        $this->addCollector(new RequestDataCollector());
        $this->addCollector(new TimeDataCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new ExceptionsCollector());
        $this->addCollector(new ConfigCollector());
        $this->addCollector(new LeevelCollector($this->container->make('app')));
        $this->addCollector(new SessionCollector($this->container->make('session')));
        $this->addCollector(new FilesCollector());
        $this->addCollector(new LogsCollector());
        $this->initData();
        $this->databaseEventDispatch();
        $this->logEventDispatch();
    }

    /**
     * 是否初始化.
     *
     * @return bool
     */
    public function isBootstrap(): bool
    {
        return $this->isBootstrap;
    }

    /**
     * 初始化数据.
     */
    protected function initData(): void
    {
        $this->message('Starts from this moment with QueryPHP.', '');
        $this
            ->getCollector('config')
            ->setData($this->container->make('option')->all());
    }

    /**
     * 设置数据库 SQL 监听器.
     */
    protected function databaseEventDispatch(): void
    {
        $this
            ->getEventDispatch()
            ->register(IDatabase::SQL_EVENT, function (string $event, string $sql) {
                $this
                    ->getCollector('logs')
                    ->addMessage($sql, 'sql');
                $this->container
                    ->make('logs')
                    ->info('[SQL] '.$sql);
            });
    }

    /**
     * 设置日志监听器.
     */
    protected function logEventDispatch(): void
    {
        $this->getEventDispatch()
            ->register(ILog::LOG_EVENT, function (string $event, string $level, string $message, array $context = []) {
                $this
                    ->getCollector('logs')
                    ->addMessage(File::formatMessage($level, $message, $context), $level);
            });
    }

    /**
     * 获取事件处理器.
     *
     * @return \Leevel\Event\IDispatch
     */
    protected function getEventDispatch(): IDispatch
    {
        return $this->container->make(IDispatch::class);
    }
}
