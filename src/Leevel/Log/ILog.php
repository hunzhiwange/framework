<?php

declare(strict_types=1);

namespace Leevel\Log;

use Monolog\Logger;

/**
 * 日志接口.
 */
interface ILog
{
    /**
     * emergency.
     */
    public const LEVEL_EMERGENCY = 'emergency';

    /**
     * alert.
     */
    public const LEVEL_ALERT = 'alert';

    /**
     * critical.
     */
    public const LEVEL_CRITICAL = 'critical';

    /**
     * error.
     */
    public const LEVEL_ERROR = 'error';


    /**
     * warning.
     */
    public const LEVEL_WARNING = 'warning';

    /**
     * notice.
     */
    public const LEVEL_NOTICE = 'notice';

    /**
     * info.
     */
    public const LEVEL_INFO = 'info';

    /**
     * debug.
     */
    public const LEVEL_DEBUG = 'debug';

    /**
     * 记录全部日志.
     */
    public const LEVEL_ALL = 'all';

    /**
     * 关闭日志.
     */
    public const LEVEL_OFF = 'off';

    /**
     * 日志优先级.
     */
    const LEVEL_PRIORITY = [
        self::LEVEL_OFF => -1,
        self::LEVEL_EMERGENCY => 0,
        self::LEVEL_ALERT => 1,
        self::LEVEL_CRITICAL => 2,
        self::LEVEL_ERROR => 3,
        self::LEVEL_WARNING => 4,
        self::LEVEL_NOTICE => 5,
        self::LEVEL_INFO => 6,
        self::LEVEL_DEBUG => 7,
        self::LEVEL_ALL => 7,
    ];

    /**
     * 日志事件.
     */
    public const LOG_EVENT = 'log.log';

    /**
     * 系统无法使用.
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * 必须立即采取行动.
     *
     * 比如: 整个网站宕机，数据库不可用等等.
     * 这种错误应该通过短信通知你.
     */
    public function alert(string $message, array $context = []): void;

    /**
     * 临界条件.
     *
     * 比如: 应用程序组件不可用，意外异常.
     */
    public function critical(string $message, array $context = []): void;

    /**
     * 运行时错误，不需要立即处理.
     * 但是需要被记录和监控.
     */
    public function error(string $message, array $context = []): void;

    /**
     * 非错误的异常事件.
     *
     * 比如: 弃用的 API 接口, API 使用不足, 不良事物.
     * 它们不一定是错误的.
     */
    public function warning(string $message, array $context = []): void;

    /**
     * 正常重要事件.
     */
    public function notice(string $message, array $context = []): void;

    /**
     * 想记录的日志.
     *
     * 比如: 用户日志, SQL 日志.
     */
    public function info(string $message, array $context = []): void;

    /**
     * 调试信息.
     */
    public function debug(string $message, array $context = []): void;

    /**
     * 记录特定级别的日志信息.
     */
    public function log(string $level, string $message, array $context = []): void;

    /**
     * 保存日志信息.
     */
    public function flush(): void;

    /**
     * 清理日志记录.
     */
    public function clear(?string $level = null): void;

    /**
     * 获取当前日志记录.
     *
     * - 每次 IO 写入后会执行一次清理
     */
    public function all(?string $level = null): array;

    /**
     * 获取日志记录数量.
     */
    public function count(?string $level = null): int;

    /**
     * 取得 Monolog.
     */
    public function getMonolog(): Logger;

    /**
     * 存储日志.
     */
    public function store(array $data): void;

    /**
     * 分析日志消息分类.
     */
    public static function parseMessageCategory(string $message): string;
}
