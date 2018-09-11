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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Log;

use Closure;

/**
 * 日志仓储.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.03.03
 *
 * @version 1.0
 */
class Log implements ILog
{
    /**
     * 存储连接对象
     *
     * @var \Leevel\Log\IConnect
     */
    protected $connect;

    /**
     * 当前记录的日志信息.
     *
     * @var array
     */
    protected $logs = [];

    /**
     * 日志过滤器.
     *
     * @var \Closure
     */
    protected $filter;

    /**
     * 日志处理器.
     *
     * @var \Closure
     */
    protected $processor;

    /**
     * 日志数量.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'levels'   => [
            self::DEBUG,
            self::INFO,
            self::NOTICE,
            self::WARNING,
            self::ERROR,
            self::CRITICAL,
            self::ALERT,
            self::EMERGENCY,
        ],
        'buffer'      => true,
        'buffer_size' => 100,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Log\IConnect $connect
     * @param array                $option
     */
    public function __construct(IConnect $connect, array $option = [])
    {
        $this->connect = $connect;

        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value)
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
        $this->log(static::EMERGENCY, $message, $context);
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
        $this->log(static::ALERT, $message, $context);
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
        $this->log(static::CRITICAL, $message, $context);
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
        $this->log(static::ERROR, $message, $context);
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
        $this->log(static::WARNING, $message, $context);
    }

    /**
     * 正常重要事件.
     *
     * @param string $message
     * @param array  $context
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(static::NOTICE, $message, $context);
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
        $this->log(static::INFO, $message, $context);
    }

    /**
     * 调试信息.
     *
     * @param string $message
     * @param array  $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(static::DEBUG, $message, $context);
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

        $data = [
            $level,
            $message,
            $context,
        ];

        if (null !== ($filter = $this->filter) &&
            false === $filter(...$data)) {
            return;
        }

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
    public function flush()
    {
        foreach ($this->logs as $data) {
            $this->saveStore($data);
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
     * 注册日志过滤器.
     *
     * @param \Closure $filter
     */
    public function filter(Closure $filter)
    {
        $this->filter = $filter;
    }

    /**
     * 注册日志处理器.
     *
     * @param \Closure $processor
     */
    public function processor(Closure $processor)
    {
        $this->processor = $processor;
    }

    /**
     * 是否为 Monolog.
     *
     * @return bool
     */
    public function isMonolog(): bool
    {
        return method_exists($this->connect, 'getMonolog');
    }

    /**
     * 取得 Monolog.
     *
     * @return null|\Monolog\Logger
     */
    public function getMonolog()
    {
        if (!$this->isMonolog()) {
            return;
        }

        return $this->connect->getMonolog();
    }

    /**
     * 返回连接.
     *
     * @return \Leevel\Log\IConnect
     */
    public function getConnect(): IConnect
    {
        return $this->connect;
    }

    /**
     * 存储日志.
     *
     * @param array $data
     */
    protected function saveStore(array $data)
    {
        if (null !== ($processor = $this->processor)) {
            foreach ($data as $value) {
                $processor(...$value);
            }
        }

        $this->connect->flush($data);
    }
}
