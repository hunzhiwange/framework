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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Log\Proxy;

use Leevel\Di\Container;
use Leevel\Log\ILog as IBaseLog;
use Leevel\Log\Manager;
use Monolog\Logger;

/**
 * 代理 log.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Log
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 设置配置.
     *
     * @param mixed $value
     */
    public static function setOption(string $name, $value): IBaseLog
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 系统无法使用.
     */
    public static function emergency(string $message, array $context = []): void
    {
        self::proxy()->emergency($message, $context);
    }

    /**
     * 必须立即采取行动.
     *
     * - 比如: 整个网站宕机，数据库不可用等等.
     * - 这种错误应该通过短信通知你.
     */
    public static function alert(string $message, array $context = []): void
    {
        self::proxy()->alert($message, $context);
    }

    /**
     * 临界条件.
     *
     * - 比如: 应用程序组件不可用，意外异常.
     */
    public static function critical(string $message, array $context = []): void
    {
        self::proxy()->critical($message, $context);
    }

    /**
     * 运行时错误，不需要立即处理.
     *
     * - 但是需要被记录和监控.
     */
    public static function error(string $message, array $context = []): void
    {
        self::proxy()->error($message, $context);
    }

    /**
     * 非错误的异常事件.
     *
     * - 比如: 弃用的 API 接口, API 使用不足, 不良事物.
     * - 它们不一定是错误的.
     */
    public static function warning(string $message, array $context = []): void
    {
        self::proxy()->warning($message, $context);
    }

    /**
     * 正常重要事件.
     */
    public static function notice(string $message, array $context = []): void
    {
        self::proxy()->notice($message, $context);
    }

    /**
     * 想记录的日志.
     *
     * - 比如: 用户日志, SQL 日志.
     */
    public static function info(string $message, array $context = []): void
    {
        self::proxy()->info($message, $context);
    }

    /**
     * 调试信息.
     */
    public static function debug(string $message, array $context = []): void
    {
        self::proxy()->debug($message, $context);
    }

    /**
     * 记录特定级别的日志信息.
     *
     * @param mixed $level
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        self::proxy()->log($level, $message, $context);
    }

    /**
     * 保存日志信息.
     */
    public static function flush(): void
    {
        self::proxy()->flush();
    }

    /**
     * 清理日志记录.
     */
    public static function clear(?string $level = null): void
    {
        self::proxy()->clear($level);
    }

    /**
     * 获取日志记录.
     */
    public static function all(?string $level = null): array
    {
        return self::proxy()->all($level);
    }

    /**
     * 获取日志记录数量.
     */
    public static function count(?string $level = null): int
    {
        return self::proxy()->count($level);
    }

    /**
     * 是否为 Monolog.
     */
    public static function isMonolog(): bool
    {
        return self::proxy()->isMonolog();
    }

    /**
     * 取得 Monolog.
     */
    public static function getMonolog(): ?Logger
    {
        return self::proxy()->getMonolog();
    }

    /**
     * 存储日志.
     */
    public static function store(array $data): void
    {
        self::proxy()->store($data);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('logs');
    }
}
