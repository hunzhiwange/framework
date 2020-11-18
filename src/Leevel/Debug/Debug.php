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

namespace Leevel\Debug;

use Closure;
use DateTime;
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
use Leevel\Http\Request;
use Leevel\Log\ILog;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * 调试器.
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
class Debug
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * DebugBar.
     */
    protected DebugBar $debugBar;

    /**
     * 是否启用调试.
    */
    protected bool $enabled = true;

    /**
     * 是否已经初始化引导
    */
    protected bool $isBootstrap = false;

    /**
     * 配置.
     */
    protected array $option = [
        'json'       => true,
        'console'    => true,
        'javascript' => true,
    ];

    /**
     * 构造函数.
     *
     * - I actually copied a lot of ideas from laravel-debugbar app.
     */
    public function __construct(IContainer $container, array $option = [])
    {
        $this->container = $container;
        $this->option = array_merge($this->option, $option);
        $this->debugBar = new DebugBar();
    }

    /**
     * call.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->debugBar->{$method}(...$args);
    }

    /**
     * 返回应用.
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * 响应.
     */
    public function handle(Request $request, Response $response): void
    {
        if (!$this->enabled) {
            return;
        }

        if ($response instanceof JsonResponse) {
            if ($this->option['json'] &&
                is_array($data = $this->jsonStringToArray($response->getContent()))) {
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
                $response->setContent(
                    $response->getContent().
                    $javascriptRenderer->renderHead().
                    $javascriptRenderer->render()
                );
            }

            if ($this->option['console']) {
                $consoleRenderer = $this->getConsoleRenderer();
                $response->setContent($response->getContent().$consoleRenderer->render());
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
     */
    public function message(mixed $message, string $label = 'info'): void
    {
        $this->getMessagesCollector()->addMessage($message, $label);
    }

    /**
     * 添加一条 emergency 消息.
     */
    public function emergency(mixed $message): void
    {
        $this->message($message, 'emergency');
    }

    /**
     * 添加一条 alert 消息.
     */
    public function alert(mixed $message): void
    {
        $this->message($message, 'alert');
    }

    /**
     * 添加一条 critical 消息.
     */
    public function critical(mixed $message): void
    {
        $this->message($message, 'critical');
    }

    /**
     * 添加一条 error 消息.
     */
    public function error(mixed $message): void
    {
        $this->message($message, 'error');
    }

    /**
     * 添加一条 warning 消息.
     */
    public function warning(mixed $message): void
    {
        $this->message($message, 'warning');
    }

    /**
     * 添加一条 notice 消息.
     */
    public function notice(mixed $message): void
    {
        $this->message($message, 'notice');
    }

    /**
     * 添加一条 info 消息.
     */
    public function info(mixed $message): void
    {
        $this->message($message, 'info');
    }

    /**
     * 添加一条 debug 消息.
     */
    public function debug(mixed $message): void
    {
        $this->message($message, 'debug');
    }

    /**
     * 添加一条 log 消息.
     */
    public function log(mixed $message): void
    {
        $this->message($message, 'log');
    }

    /**
     * 开始调试时间.
     */
    public function time(string $name, ?string $label = null): void
    {
        $this->getTimeDataCollector()->startMeasure($name, $label);
    }

    /**
     * 停止调试时间.
     */
    public function end(string $name): void
    {
        try {
            $this->getTimeDataCollector()->stopMeasure($name);
        } catch (Exception $e) {
        }
    }

    /**
     * 添加一个时间调试.
     */
    public function addTime(string $label, float $start, float $end): void
    {
        $this->getTimeDataCollector()->addMeasure($label, $start, $end);
    }

    /**
     * 调试闭包执行时间.
     */
    public function closureTime(string $label, Closure $closure): void
    {
        $this->getTimeDataCollector()->measure($label, $closure);
    }

    /**
     * 添加异常.
     */
    public function exception(Throwable $e): void
    {
        $this->getExceptionsCollector()->addThrowable($e);
    }

    /**
     * 获取 JSON 渲染.
     */
    public function getJsonRenderer(): JsonRenderer
    {
        return new JsonRenderer($this);
    }

    /**
     * 获取 Console 渲染.
     */
    public function getConsoleRenderer(): ConsoleRenderer
    {
        return new ConsoleRenderer($this);
    }

    /**
     * 返回此实例的 \DebugBar\JavascriptRenderer.
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
     */
    public function isBootstrap(): bool
    {
        return $this->isBootstrap;
    }

    /**
     * JSON 字符串转为数组.
     */
    protected function jsonStringToArray(false|string $value): mixed
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 初始化数据.
     */
    protected function initData(): void
    {
        $this->message('Starts from this moment with QueryPHP.', '');
        $this->getConfigCollector()->setData($this->container->make('option')->all());
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
                    ->getLogsCollector()
                    ->addMessage($sql, 'sql');
                $this->container
                    ->make('logs')
                    ->info($sql);
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
                    ->getLogsCollector()
                    ->addMessage($this->formatMessage($level, $message, $context), $level);
            });
    }

    /**
     * 格式化日志信息.
     */
    protected function formatMessage(string $level, string $message, array $context = []): string
    {
        return sprintf(
            '[%s] %s %s: %s'.PHP_EOL,
            (new DateTime())->format('Y-m-d H:i:s u'),
            $message,
            $level,
            json_encode($context, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * 获取事件处理器.
     */
    protected function getEventDispatch(): IDispatch
    {
        return $this->container->make(IDispatch::class);
    }

    /**
     * 获取 time 收集器.
     */
    protected function getTimeDataCollector(): TimeDataCollector
    {
        return $this->getCollector('time');
    }

    /**
     * 获取 messages 收集器.
     */
    protected function getMessagesCollector(): MessagesCollector
    {
        return $this->getCollector('messages');
    }

    /**
     * 获取 exceptions 收集器.
     */
    protected function getExceptionsCollector(): ExceptionsCollector
    {
        return $this->getCollector('exceptions');
    }

    /**
     * 获取 config 收集器.
     */
    protected function getConfigCollector(): ConfigCollector
    {
        return $this->getCollector('config');
    }

    /**
     * 获取 logs 收集器.
     */
    protected function getLogsCollector(): LogsCollector
    {
        return $this->getCollector('logs');
    }
}
