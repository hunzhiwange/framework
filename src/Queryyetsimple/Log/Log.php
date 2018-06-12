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

use Leevel\Option\TClass;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use RuntimeException;

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
    use TClass;

    /**
     * 存储连接对象
     *
     * @var \Leevel\Log\icooption,nnect
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
     * @var callable
     */
    protected $filter;

    /**
     * 日志处理器.
     *
     * @var callable
     */
    protected $processor;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'enabled' => true,
        'level' => [
            self::DEBUG,
            self::INFO,
            self::NOTICE,
            self::WARNING,
            self::ERROR,
            self::CRITICAL,
            self::ALERT,
            self::EMERGENCY,
            self::SQL,
        ],
        'time_format' => '[Y-m-d H:i]',
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
        $this->options($option);
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->connect->{$method}(...$args);
    }

    /**
     * 记录 emergency 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function emergency($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::EMERGENCY, $message, $context);
    }

    /**
     * 记录 alert 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function alert($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::ALERT, $message, $context);
    }

    /**
     * 记录 critical 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function critical($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::CRITICAL, $message, $context);
    }

    /**
     * 记录 error 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function error($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::ERROR, $message, $context);
    }

    /**
     * 记录 warning 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function warning($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::WARNING, $message, $context);
    }

    /**
     * 记录 notice 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function notice($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::NOTICE, $message, $context);
    }

    /**
     * 记录 info 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function info($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::INFO, $message, $context);
    }

    /**
     * 记录 debug 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function debug($message, array $context = [], $write = false)
    {
        return $this->{$write ? 'write' : 'log'}(static::DEBUG, $message, $context);
    }

    /**
     * 记录日志.
     *
     * @param string $level
     * @param mixed  $message
     * @param array  $context
     *
     * @return array
     */
    public function log($level, $message, array $context = [])
    {
        // 是否开启日志
        if (!$this->getOption('enabled')) {
            return;
        }

        // 只记录系统允许的日志级别
        if (!in_array($level, $this->getOption('level'), true)) {
            return;
        }

        $message = date($this->getOption('time_format')).$this->formatMessage($message);

        $data = [
            $level,
            $message,
            $context,
        ];

        // 执行过滤器
        if (null !== $this->filter && false === call_user_func_array($this->filter, $data)) {
            return;
        }

        // 记录到内存方便后期调用
        if (!isset($this->logs[$level])) {
            $this->logs[$level] = [];
        }
        $this->logs[$level][] = $data;

        return $data;
    }

    /**
     * 记录错误消息并写入.
     *
     * @param string $level   日志类型
     * @param string $message 应该被记录的错误信息
     * @param array  $context
     */
    public function write($level, $message, array $context = [])
    {
        $this->saveStore([
            $this->log($level, $message, $context),
        ]);
    }

    /**
     * 保存日志信息.
     */
    public function save()
    {
        if (!$this->logs) {
            return;
        }

        foreach ($this->logs as $data) {
            $this->saveStore($data);
        }

        $this->clear();
    }

    /**
     * 注册日志过滤器.
     *
     * @param callable $filter
     */
    public function registerFilter(callable $filter)
    {
        $this->filter = $filter;
    }

    /**
     * 注册日志处理器.
     *
     * @param callable $processor
     */
    public function registerProcessor(callable $processor)
    {
        $this->processor = $processor;
    }

    /**
     * 清理日志记录.
     *
     * @param string $level
     *
     * @return int
     */
    public function clear($level = null)
    {
        if ($level && isset($this->logs[$level])) {
            $count = count($this->logs[$level]);
            $this->logs[$level] = [];
        } else {
            $count = count($this->logs);
            $this->logs = [];
        }

        return $count;
    }

    /**
     * 获取日志记录.
     *
     * @param string $level
     *
     * @return array
     */
    public function get($level = null)
    {
        if ($level && isset($this->logs[$level])) {
            return $this->logs[$level];
        }

        return $this->logs;
    }

    /**
     * 获取日志记录数量.
     *
     * @param string $level
     *
     * @return int
     */
    public function count($level = null)
    {
        if ($level && isset($this->logs[$level])) {
            return count($this->logs[$level]);
        }

        return count($this->logs);
    }

    /**
     * 存储日志.
     *
     * @param array $data
     */
    protected function saveStore($data)
    {
        // 执行处理器
        if (null !== $this->processor) {
            call_user_func_array($this->processor, $data);
        }
        $this->connect->save($data);
    }

    /**
     * 格式化日志消息.
     *
     * @param mixed $message
     *
     * @return mixed
     */
    protected function formatMessage($message)
    {
        if (is_array($message)) {
            return var_export($message, true);
        }
        if ($message instanceof IJson) {
            return $message->toJson();
        }
        if ($message instanceof IArray) {
            return var_export($message->toArray(), true);
        }
        if (is_scalar($message)) {
            return $message;
        }

        throw new RuntimeException('Message is invalid.');
    }
}
