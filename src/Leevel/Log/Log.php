<?php

declare(strict_types=1);

namespace Leevel\Log;

use Leevel\Event\IDispatch;
use Monolog\Formatter\LineFormatter;
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
        ILog::LEVEL_DEBUG     => Logger::DEBUG,
        ILog::LEVEL_INFO      => Logger::INFO,
        ILog::LEVEL_NOTICE    => Logger::NOTICE,
        ILog::LEVEL_WARNING   => Logger::WARNING,
        ILog::LEVEL_ERROR     => Logger::ERROR,
        ILog::LEVEL_CRITICAL  => Logger::CRITICAL,
        ILog::LEVEL_ALERT     => Logger::ALERT,
        ILog::LEVEL_EMERGENCY => Logger::EMERGENCY,
    ];

    /**
     * 配置.
     */
    protected array $option = [
        'levels'   => [
            ILog::LEVEL_DEBUG,
            ILog::LEVEL_INFO,
            ILog::LEVEL_NOTICE,
            ILog::LEVEL_WARNING,
            ILog::LEVEL_ERROR,
            ILog::LEVEL_CRITICAL,
            ILog::LEVEL_ALERT,
            ILog::LEVEL_EMERGENCY,
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
    public function log(string $level, string $message, array $context = []): void
    {
        $level = $this->normalizeLevel($level);
        if (!in_array($level, $this->option['levels'], true)) {
            return;
        }

        $data = [$level, $message, $context];
        $this->handleDispatch($data);
        $this->count++;
        $this->logs[$level][] = $data;

        if (false === $this->option['buffer'] ||
            ($this->option['buffer_size'] && $this->count >= $this->option['buffer_size'])) {
            $this->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function flush(): void
    {
        foreach ($this->logs as $data) {
            $this->store($data);
        }

        $this->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function clear(?string $level = null): void
    {
        if (null === $level) {
            $this->count = 0;
            $this->logs = [];
        }

        if (isset($this->logs[$level])) {
            $this->count -= count($this->logs[$level]);
            $this->logs[$level] = [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function all(?string $level = null): array
    {
        if (null === $level) {
            return $this->logs;
        }

        if (isset($this->logs[$level])) {
            return $this->logs[$level];
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function count(?string $level = null): int
    {
        if (null === $level) {
            return $this->count;
        }

        return count($this->all($level));
    }

    /**
     * {@inheritDoc}
     */
    public function getMonolog(): Logger
    {
        return $this->monolog;
    }

    /**
     * {@inheritDoc}
     */
    public function store(array $data): void
    {
        $categoryData = [];
        foreach ($data as $value) {
            $categoryData[static::parseMessageCategory($value[1])][] = $value;
        }
        foreach ($categoryData as $category => $messages) {
            $this->addHandlers($messages[0][0], $category);
            foreach ($messages as $value) {
                $method = array_shift($value);
                $this->monolog->{$method}(...$value);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function parseMessageCategory(string $message): string
    {
        if (preg_match('/^\[([a-zA-Z_0-9\-:.\/]+)\]/', $message, $matches)) {
            return str_replace(':', '/', $matches[1]);
        }

        return '';
    }

    /**
     * 添加日志处理器到 Monolog.
     */
    abstract protected function addHandlers(string $level, string $category): void;

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
     *
     * - 不支持级别归并到 DEBUG.
     */
    protected function normalizeMonologLevel(string $level): int
    {
        return $this->supportLevel[$level];
    }
}
