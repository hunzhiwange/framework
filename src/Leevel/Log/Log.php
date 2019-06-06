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

namespace Leevel\Log;

use Leevel\Event\IDispatch;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

/**
 * 日志驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.01
 *
 * @version 1.0
 */
abstract class Log
{
    /**
     * Monolog.
     *
     * @var \Monolog\Logger
     */
    protected $monolog;

    /**
     * 事件处理器.
     *
     * @var \Leevel\Event\IDispatch
     */
    protected $dispatch;

    /**
     * 当前记录的日志信息.
     *
     * @var array
     */
    protected $logs = [];

    /**
     * 日志数量.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * Monolog 支持日志级别.
     *
     * @var array
     */
    protected $supportLevel = [
        ILog::DEBUG     => Logger::DEBUG,
        ILog::INFO      => Logger::INFO,
        ILog::NOTICE    => Logger::NOTICE,
        ILog::WARNING   => Logger::WARNING,
        ILog::ERROR     => Logger::ERROR,
        ILog::CRITICAL  => Logger::CRITICAL,
        ILog::ALERT     => Logger::ALERT,
        ILog::EMERGENCY => Logger::EMERGENCY,
    ];

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'levels'   => [
            ILog::DEBUG,
            ILog::INFO,
            ILog::NOTICE,
            ILog::WARNING,
            ILog::ERROR,
            ILog::CRITICAL,
            ILog::ALERT,
            ILog::EMERGENCY,
        ],
        'buffer'      => true,
        'buffer_size' => 100,
        'channel'     => 'development',
    ];

    /**
     * 构造函数.
     *
     * @param array                   $option
     * @param \Leevel\Event\IDispatch $dispatch
     */
    public function __construct(array $option = [], IDispatch $dispatch = null)
    {
        $this->option = array_merge($this->option, $option);
        $this->dispatch = $dispatch;
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Log\ILog
     */
    public function setOption(string $name, $value): ILog
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 系统无法使用.
     *
     * @param string $message
     * @param array  $context
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(ILog::EMERGENCY, $message, $context);
    }

    /**
     * 必须立即采取行动.
     *
     * 比如: 整个网站宕机，数据库不可用等等.
     * 这种错误应该通过短信通知你.
     *
     * @param string $message
     * @param array  $context
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(ILog::ALERT, $message, $context);
    }

    /**
     * 临界条件.
     *
     * 比如: 应用程序组件不可用，意外异常.
     *
     * @param string $message
     * @param array  $context
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(ILog::CRITICAL, $message, $context);
    }

    /**
     * 运行时错误，不需要立即处理.
     * 但是需要被记录和监控.
     *
     * @param string $message
     * @param array  $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(ILog::ERROR, $message, $context);
    }

    /**
     * 非错误的异常事件.
     *
     * 比如: 弃用的 API 接口, API 使用不足, 不良事物.
     * 它们不一定是错误的.
     *
     * @param string $message
     * @param array  $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(ILog::WARNING, $message, $context);
    }

    /**
     * 正常重要事件.
     *
     * @param string $message
     * @param array  $context
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(ILog::NOTICE, $message, $context);
    }

    /**
     * 想记录的日志.
     *
     * 比如: 用户日志, SQL 日志.
     *
     * @param string $message
     * @param array  $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(ILog::INFO, $message, $context);
    }

    /**
     * 调试信息.
     *
     * @param string $message
     * @param array  $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(ILog::DEBUG, $message, $context);
    }

    /**
     * 记录特定级别的日志信息.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log(string $level, string $message, array $context = []): void
    {
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
     * 保存日志信息.
     */
    public function flush(): void
    {
        foreach ($this->logs as $data) {
            $this->store($data);
        }

        $this->clear();
    }

    /**
     * 清理日志记录.
     *
     * @param string $level
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
     * 获取日志记录.
     *
     * @param string $level
     *
     * @return array
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
     * 获取日志记录数量.
     *
     * @param string $level
     *
     * @return int
     */
    public function count(?string $level = null): int
    {
        if (null === $level) {
            return $this->count;
        }

        return count($this->all($level));
    }

    /**
     * 是否为 Monolog.
     *
     * @return bool
     */
    public function isMonolog(): bool
    {
        return null !== $this->monolog;
    }

    /**
     * 取得 Monolog.
     *
     * @return null|\Monolog\Logger
     */
    public function getMonolog(): ?Logger
    {
        return $this->monolog;
    }

    /**
     * 存储日志.
     *
     * @param array $data
     */
    public function store(array $data): void
    {
        foreach ($data as $value) {
            $method = $this->normalizeLevel(array_shift($value));

            $this->monolog->{$method}(...$value);
        }
    }

    /**
     * 创建 monolog.
     */
    protected function createMonolog(): void
    {
        $this->monolog = new Logger($this->option['channel']);
    }

    /**
     * 事件派发.
     *
     * @param array $data
     */
    protected function handleDispatch(array $data): void
    {
        if ($this->dispatch) {
            $this->dispatch->handle(ILog::LOG_EVENT, ...$data);
        }
    }

    /**
     * 设置默认格式化.
     *
     * @param \Monolog\Handler\HandlerInterface $handler
     *
     * @return \Monolog\Handler\HandlerInterface
     */
    protected function normalizeHandler(HandlerInterface $handler): HandlerInterface
    {
        return $handler->setFormatter($this->lineFormatter());
    }

    /**
     * 默认行格式化.
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function lineFormatter(): LineFormatter
    {
        return new LineFormatter(null, null, true, true);
    }

    /**
     * 格式化级别
     * 不支持级别归并到 DEBUG.
     *
     * @param string $level
     *
     * @return string
     */
    protected function normalizeLevel(string $level): string
    {
        if (!in_array($level, array_keys($this->supportLevel), true)) {
            return ILog::DEBUG;
        }

        return $level;
    }

    /**
     * 获取 Monolog 级别
     * 不支持级别归并到 DEBUG.
     *
     * @param string $level
     *
     * @return int
     */
    protected function normalizeMonologLevel(string $level): int
    {
        if (isset($this->supportLevel[$level])) {
            return $this->supportLevel[$level];
        }

        return $this->supportLevel[ILog::DEBUG];
    }
}
