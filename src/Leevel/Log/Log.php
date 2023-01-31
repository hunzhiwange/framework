<?php

declare(strict_types=1);

namespace Leevel\Log;

use Leevel\Event\IDispatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

/**
 * 日志抽象类.
 */
abstract class Log implements ILog
{
    /**
     * Monolog.
     */
    protected Logger $monolog;

    /**
     * 事件处理器.
     */
    protected ?IDispatch $dispatch = null;

    /**
     * 当前记录的日志信息.
     */
    protected array $logs = [];

    /**
     * 日志数量.
     */
    protected int $count = 0;

    /**
     * 日志处理器.
     */
    protected array $logHandlers = [];

    /**
     * Monolog 支持日志级别.
     */
    protected array $supportLevel = [
        ILog::LEVEL_EMERGENCY => Logger::EMERGENCY,
        ILog::LEVEL_ALERT     => Logger::ALERT,
        ILog::LEVEL_CRITICAL  => Logger::CRITICAL,
        ILog::LEVEL_ERROR     => Logger::ERROR,
        ILog::LEVEL_WARNING   => Logger::WARNING,
        ILog::LEVEL_NOTICE    => Logger::NOTICE,
        ILog::LEVEL_INFO      => Logger::INFO,
        ILog::LEVEL_DEBUG     => Logger::DEBUG,
    ];

    /**
     * 配置.
     */
    protected array $option = [
        'level'   => [
            ILog::DEFAULT_MESSAGE_CATEGORY => ILog::LEVEL_DEBUG,
        ],
        'buffer'      => true,
        'buffer_size' => 100,
        'channel'     => 'development',
    ];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [], ?IDispatch $dispatch = null)
    {
        $this->option = array_merge($this->option, $option);
        $this->dispatch = $dispatch;
        $this->createMonolog();
    }

    /**
     * {@inheritDoc}
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_EMERGENCY, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_ALERT, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_CRITICAL, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_ERROR, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_WARNING, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_NOTICE, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_INFO, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(ILog::LEVEL_DEBUG, $message, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
        foreach ($this->logs as $data) {
            $this->store($data);
        }

        $this->count = 0;
        $this->logs = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getMonolog(): Logger
    {
        return $this->monolog;
    }

    /**
     * 存储日志.
     */
    protected function store(array $data): void
    {
        foreach ($data as $messageCategory => $messages) {
            $this->addHandlers($messages[0][0], $messageCategory);
            foreach ($messages as $value) {
                $method = array_shift($value);
                $this->monolog->{$method}(...$value);
            }
        }
    }

    /**
     * 记录特定级别的日志信息.
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        $level = $this->normalizeLevel($level);
        $messageCategory = $this->parseMessageCategory($message);
        $minLevel = $this->getMinLevel($messageCategory, $this->option['level']);
        if (ILog::LEVEL_PRIORITY[$level] > ILog::LEVEL_PRIORITY[$minLevel]) {
            return;
        }

        $data = [$level, $message, $context];
        $this->handleDispatch($data);
        $this->count++;
        $this->logs[$level][$messageCategory][] = $data;

        if (false === $this->option['buffer'] ||
            ($this->option['buffer_size'] && $this->count >= $this->option['buffer_size'])) {
            $this->flush();
        }
    }

    /**
     * 分析日志消息分类.
     */
    protected function parseMessageCategory(string $message): string
    {
        if (preg_match('/^\[([a-zA-Z_0-9\-:.\/]+)\]/', $message, $matches)) {
            return $matches[1];
        }

        return ILog::DEFAULT_MESSAGE_CATEGORY;
    }

    /**
     * 获取日志最低写入级别.
     */
    protected function getMinLevel(string $messageCategory, array $defaultLevel): string
    {
        if (isset($defaultLevel[$messageCategory])) {
            return $defaultLevel[$messageCategory];
        }

        return $defaultLevel[ILog::DEFAULT_MESSAGE_CATEGORY];
    }

    /**
     * 添加日志处理器到 Monolog.
     */
    protected function addHandlers(string $level, string $category): void
    {
        if (isset($this->logHandlers[$level][$category])) {
            $logHandlers = $this->logHandlers[$level][$category];
        } else {
            $this->logHandlers[$level][$category] = $logHandlers = [$this->makeHandlers($level, $category)];
        }
        $this->monolog->setHandlers($logHandlers);
    }

    /**
     * 创建日志处理器.
     */
    abstract protected function makeHandlers(string $level, string $category): AbstractProcessingHandler;

    /**
     * 创建 monolog.
     */
    protected function createMonolog(): void
    {
        $this->monolog = new Logger($this->option['channel']);
    }

    /**
     * 事件派发.
     */
    protected function handleDispatch(array $data): void
    {
        if ($this->dispatch) {
            $this->dispatch->handle(ILog::LOG_EVENT, ...$data);
        }
    }

    /**
     * 设置默认格式化.
     */
    protected function setHandlerLineFormatter(HandlerInterface $handler): HandlerInterface
    {
        return $handler->setFormatter($this->createLineFormatter());
    }

    /**
     * 创建默认行格式化.
     */
    protected function createLineFormatter(): LineFormatter
    {
        return new LineFormatter(null, $this->option['format'], true, true);
    }

    /**
     * 格式化级别.
     *
     * - 不支持级别归并到 DEBUG.
     */
    protected function normalizeLevel(string $level): string
    {
        if (!in_array($level, array_keys($this->supportLevel), true)) {
            return ILog::LEVEL_DEBUG;
        }

        return $level;
    }

    /**
     * 获取 Monolog 级别.
     */
    protected function normalizeMonologLevel(string $level): int
    {
        return $this->supportLevel[$level];
    }
}
