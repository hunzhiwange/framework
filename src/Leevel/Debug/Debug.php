<?php

declare(strict_types=1);

namespace Leevel\Debug;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\ExceptionsCollector;
use DebugBar\DataCollector\MemoryCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PhpInfoCollector;
use DebugBar\DataCollector\RequestDataCollector;
use DebugBar\DataCollector\TimeDataCollector;
use DebugBar\DebugBar;
use Leevel\Config\IConfig;
use Leevel\Database\IDatabase;
use Leevel\Debug\DataCollector\FilesCollector;
use Leevel\Debug\DataCollector\LeevelCollector;
use Leevel\Debug\DataCollector\LogsCollector;
use Leevel\Debug\DataCollector\SessionCollector;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Http\Request;
use Leevel\Kernel\IApp;
use Leevel\Log\ILog;
use Leevel\Session\ISession;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * 调试器.
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
    protected array $config = [
        'json' => true,
        'console' => true,
    ];

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container, array $config = [])
    {
        $this->container = $container;
        $this->config = array_merge($this->config, $config);
        $this->debugBar = new DebugBar();
    }

    /**
     * 获取 DebugBar.
     */
    public function getDebugBar(): DebugBar
    {
        return $this->debugBar;
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
            // 空数组或者非索引数组直接附加到数组 `:trace` 键上
            // 索引数组不支持返回调试信息，防止破坏数据的完整性
            if ($this->config['json']
                && \is_array($data = $this->jsonStringToArray($response->getContent()))
                && !array_is_list($data)) {
                $data[':trace'] = $this->getJsonRenderer()->render();
                $response->setData($data);
            }
        } elseif (!$response instanceof RedirectResponse) {
            if ($this->config['console']) {
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
     * 获取调试状态.
     */
    public function getDebugStatus(): bool
    {
        return $this->enabled;
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
        } catch (\Throwable) {
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
    public function closureTime(string $label, \Closure $closure): void
    {
        $this->getTimeDataCollector()->measure($label, $closure);
    }

    /**
     * 添加异常.
     */
    public function exception(\Throwable $e): void
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
     * 初始化.
     */
    public function bootstrap(): void
    {
        if ($this->isBootstrap) {
            return;
        }

        $this->isBootstrap = true;
        $this->getDebugBar()->addCollector(new PhpInfoCollector());
        $this->getDebugBar()->addCollector(new MessagesCollector());
        $this->getDebugBar()->addCollector(new RequestDataCollector());
        $this->getDebugBar()->addCollector(new TimeDataCollector());
        $this->getDebugBar()->addCollector(new MemoryCollector());
        $this->getDebugBar()->addCollector(new ExceptionsCollector());
        $this->getDebugBar()->addCollector(new ConfigCollector());

        /** @var IApp $app */
        $app = $this->container->make('app');
        $this->getDebugBar()->addCollector(new LeevelCollector($app));

        /** @var ISession $session */
        $session = $this->container->make('session');
        $this->getDebugBar()->addCollector(new SessionCollector($session));
        $this->getDebugBar()->addCollector(new FilesCollector());
        $this->getDebugBar()->addCollector(new LogsCollector());
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
        if (!\is_string($value)) {
            return false;
        }

        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * 初始化数据.
     */
    protected function initData(): void
    {
        $this->message('Starts from this moment with QueryPHP.', '');

        /** @var IConfig $config */
        $config = $this->container->make('config');
        $this->getConfigCollector()->setData($config->all());
    }

    /**
     * 设置数据库 SQL 监听器.
     */
    protected function databaseEventDispatch(): void
    {
        $this
            ->getEventDispatch()
            ->register(IDatabase::SQL_EVENT, function (string $event, string $sql): void {
                /** @var ILog $log */
                $log = $this->container->make('logs');
                $log->debug($sql);
            })
        ;
    }

    /**
     * 设置日志监听器.
     */
    protected function logEventDispatch(): void
    {
        $this->getEventDispatch()
            ->register(ILog::LOG_EVENT, function (string $event, string $level, string $message, array $context = []): void {
                $this
                    ->getLogsCollector()
                    ->addMessage($this->formatMessage($level, $message, $context), $level)
                ;
            })
        ;
    }

    /**
     * 格式化日志信息.
     */
    protected function formatMessage(string $level, string $message, array $context = []): string
    {
        return sprintf(
            '[%s] %s %s: %s'.PHP_EOL,
            (new \DateTime())->format('Y-m-d H:i:s u'),
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
        // @phpstan-ignore-next-line
        return $this->container->make(IDispatch::class);
    }

    /**
     * 获取 time 收集器.
     */
    protected function getTimeDataCollector(): TimeDataCollector
    {
        // @phpstan-ignore-next-line
        return $this->getCollector('time');
    }

    /**
     * 获取 messages 收集器.
     */
    protected function getMessagesCollector(): MessagesCollector
    {
        // @phpstan-ignore-next-line
        return $this->getCollector('messages');
    }

    /**
     * 获取 exceptions 收集器.
     */
    protected function getExceptionsCollector(): ExceptionsCollector
    {
        // @phpstan-ignore-next-line
        return $this->getCollector('exceptions');
    }

    /**
     * 获取 config 收集器.
     */
    protected function getConfigCollector(): ConfigCollector
    {
        // @phpstan-ignore-next-line
        return $this->getCollector('config');
    }

    /**
     * 获取 logs 收集器.
     */
    protected function getLogsCollector(): LogsCollector
    {
        // @phpstan-ignore-next-line
        return $this->getCollector('logs');
    }
}
