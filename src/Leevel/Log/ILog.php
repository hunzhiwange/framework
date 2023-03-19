<?php

declare(strict_types=1);

namespace Leevel\Log;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * 日志接口.
 */
interface ILog extends LoggerInterface
{
    /**
     * 关闭日志.
     */
    public const OFF = 'off';

    /**
     * 日志优先级.
     *
     * - RFC 5424 level
     */
    public const LEVEL_PRIORITY = [
        self::OFF => -1,
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    /**
     * 日志事件.
     */
    public const LOG_EVENT = 'log.log';

    /**
     * 默认日志类别.
     */
    public const DEFAULT_MESSAGE_CATEGORY = '*';

    /**
     * 取得 Monolog.
     */
    public function getMonolog(): Logger;
}
