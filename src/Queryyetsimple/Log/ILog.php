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

/**
 * ILog 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.11
 *
 * @version 1.0
 */
interface ILog
{
    /**
     * debug.
     *
     * @var string
     */
    const DEBUG = 'debug';

    /**
     * info.
     *
     * @var string
     */
    const INFO = 'info';

    /**
     * notice.
     *
     * @var string
     */
    const NOTICE = 'notice';

    /**
     * warning.
     *
     * @var string
     */
    const WARNING = 'warning';

    /**
     * error.
     *
     * @var string
     */
    const ERROR = 'error';

    /**
     * critical.
     *
     * @var string
     */
    const CRITICAL = 'critical';

    /**
     * alert.
     *
     * @var string
     */
    const ALERT = 'alert';

    /**
     * emergency.
     *
     * @var string
     */
    const EMERGENCY = 'emergency';

    /**
     * sql.
     *
     * @var string
     */
    const SQL = 'sql';

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value);

    /**
     * 记录 emergency 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function emergency($message, array $context = [], bool $write = false);

    /**
     * 记录 alert 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function alert($message, array $context = [], bool $write = false);

    /**
     * 记录 critical 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function critical($message, array $context = [], bool $write = false);

    /**
     * 记录 error 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function error($message, array $context = [], bool $write = false);

    /**
     * 记录 warning 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function warning($message, array $context = [], bool $write = false);

    /**
     * 记录 notice 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function notice($message, array $context = [], bool $write = false);

    /**
     * 记录 info 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function info($message, array $context = [], bool $write = false);

    /**
     * 记录 debug 日志.
     *
     * @param string $message
     * @param array  $context
     * @param bool   $write
     *
     * @return array
     */
    public function debug($message, array $context = [], bool $write = false);

    /**
     * 记录日志.
     *
     * @param string $level
     * @param mixed  $message
     * @param array  $context
     *
     * @return array
     */
    public function log($level, $message, array $context = []);

    /**
     * 记录错误消息并写入.
     *
     * @param string $level   日志类型
     * @param string $message 应该被记录的错误信息
     * @param array  $context
     */
    public function write($level, $message, array $context = []);

    /**
     * 保存日志信息.
     */
    public function save();

    /**
     * 注册日志过滤器.
     *
     * @param callable $filter
     */
    public function registerFilter(callable $filter);

    /**
     * 注册日志处理器.
     *
     * @param callable $processor
     */
    public function registerProcessor(callable $processor);

    /**
     * 清理日志记录.
     *
     * @param string $level
     *
     * @return int
     */
    public function clear($level = null);

    /**
     * 获取日志记录.
     *
     * @param string $level
     *
     * @return array
     */
    public function get($level = null);

    /**
     * 获取日志记录数量.
     *
     * @param string $level
     *
     * @return int
     */
    public function count($level = null);
}
