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
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value);

    /**
     * 系统无法使用.
     *
     * @param string $message
     * @param array  $context
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * 必须立即采取行动.
     *
     * 比如: 整个网站宕机，数据库不可用等等.
     * 这种错误应该通过短信通知你.
     *
     * @param string $message
     * @param array  $context
     */
    public function alert(string $message, array $context = []): void;

    /**
     * 临界条件.
     *
     * 比如: 应用程序组件不可用，意外异常.
     *
     * @param string $message
     * @param array  $context
     */
    public function critical(string $message, array $context = []): void;

    /**
     * 运行时错误，不需要立即处理.
     * 但是需要被记录和监控.
     *
     * @param string $message
     * @param array  $context
     */
    public function error(string $message, array $context = []): void;

    /**
     * 非错误的异常事件.
     *
     * 比如: 弃用的 API 接口, API 使用不足, 不良事物.
     * 它们不一定是错误的.
     *
     * @param string $message
     * @param array  $context
     */
    public function warning(string $message, array $context = []): void;

    /**
     * 正常重要事件.
     *
     * @param string $message
     * @param array  $context
     */
    public function notice(string $message, array $context = []): void;

    /**
     * 想记录的日志.
     *
     * 比如: 用户日志, SQL 日志.
     *
     * @param string $message
     * @param array  $context
     */
    public function info(string $message, array $context = []): void;

    /**
     * 调试信息.
     *
     * @param string $message
     * @param array  $context
     */
    public function debug(string $message, array $context = []): void;

    /**
     * 记录特定级别的日志信息.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log(string $level, string $message, array $context = []): void;

    /**
     * 保存日志信息.
     */
    public function flush();

    /**
     * 清理日志记录.
     *
     * @param string $level
     */
    public function clear(?string $level = null): void;

    /**
     * 获取日志记录.
     *
     * @param string $level
     *
     * @return array
     */
    public function all(?string $level = null): array;

    /**
     * 获取日志记录数量.
     *
     * @param string $level
     *
     * @return int
     */
    public function count(?string $level = null);

    /**
     * 注册日志过滤器.
     *
     * @param \Closure $filter
     */
    public function filter(Closure $filter);

    /**
     * 注册日志处理器.
     *
     * @param \Closure $processor
     */
    public function processor(Closure $processor);

    /**
     * 是否为 Monolog.
     *
     * @return bool
     */
    public function isMonolog(): bool;

    /**
     * 取得 Monolog.
     *
     * @return null|\Monolog\Logger
     */
    public function getMonolog();

    /**
     * 返回连接.
     *
     * @return \Leevel\Log\IConnect
     */
    public function getConnect(): IConnect;
}
