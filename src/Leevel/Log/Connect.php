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

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

/**
 * connect 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.01
 *
 * @version 1.0
 */
abstract class Connect implements IConnect
{
    /**
     * Monolog.
     *
     * @var \Monolog\Logger
     */
    protected $monolog;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'channel' => 'development',
    ];

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
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);

        $this->monolog = new Logger($this->option['channel']);
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
     * 日志写入接口.
     *
     * @param array $data
     */
    public function flush(array $data): void
    {
        foreach ($data as $value) {
            $method = $this->normalizeLevel(array_shift($value));

            $this->monolog->{$method}(...$value);
        }
    }

    /**
     * 取得 Monolog.
     *
     * @return \Monolog\Logger
     */
    public function getMonolog(): Logger
    {
        return $this->monolog;
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
