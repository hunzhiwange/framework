<?php

declare(strict_types=1);

namespace Leevel\Log;

use Leevel\Event\IDispatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * 日志抽象类.
 */
abstract class Log extends AbstractLogger implements ILog
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
     * 日志处理器.
     */
    protected array $logHandlers = [];

    /**
     * 配置.
     */
    protected array $option = [
        'level' => [
            ILog::DEFAULT_MESSAGE_CATEGORY => LogLevel::DEBUG,
        ],
        'channel' => 'development',
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
    public function getMonolog(): Logger
    {
        return $this->monolog;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level
     * @param mixed[] $context
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        if (!\defined(LogLevel::class.'::'.strtoupper($level))) {
            throw new \Psr\Log\InvalidArgumentException(sprintf('Level %s is invalid.', $level));
        }

        $message = (string) $message;
        $messageCategory = $this->parseMessageCategory($message);
        $minLevel = $this->getMinLevel($messageCategory, $this->option['level']);
        if (ILog::LEVEL_PRIORITY[$level] > ILog::LEVEL_PRIORITY[$minLevel]) {
            return;
        }

        // 事件派发
        if ($this->dispatch) {
            $this->dispatch->handle(ILog::LOG_EVENT, $level, $message, $context);
        }

        // 记录日志
        $this->addHandlers($level, $messageCategory);
        $this->monolog->{$level}($message, $context);
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
     * 设置默认格式化.
     */
    protected function setHandlerLineFormatter(HandlerInterface $handler): HandlerInterface
    {
        // @phpstan-ignore-next-line
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
     * 获取 Monolog 级别.
     */
    protected function normalizeMonologLevel(string $level): int
    {
        return Level::fromName($level)->value;
    }
}
