<?php

declare(strict_types=1);

namespace Leevel\Log;

use Monolog\Handler\SyslogHandler;

/**
 * 系统日志.
 */
class Syslog extends Log implements ILog
{
    /**
     * 配置.
     *
     * @see \Monolog\Handler\AbstractSyslogHandler
     */
    protected array $option = [
        'levels'   => [
            ILog::LEVEL_EMERGENCY,
            ILog::LEVEL_ALERT,
            ILog::LEVEL_CRITICAL,
            ILog::LEVEL_ERROR,
            ILog::LEVEL_WARNING,
            ILog::LEVEL_NOTICE,
            ILog::LEVEL_INFO,
            ILog::LEVEL_DEBUG,
        ],
        'buffer'      => true,
        'buffer_size' => 100,
        'channel'     => 'development',
        'facility'    => LOG_USER,
        'level'       => ILog::LEVEL_DEBUG,
        'format'      => 'Y-m-d H:i:s u',
    ];

    /**
     * 添加日志处理器到 Monolog.
     */
    protected function addHandlers(string $level, string $category): void
    {
        if (isset($this->logHandlers[$level])) {
            $logHandlers = $this->logHandlers[$level];
        } else {
            $this->logHandlers[$level] = $logHandlers = [$this->makeHandlers($level)];
        }
        $this->monolog->setHandlers($logHandlers);
    }

    /**
     * 创建日志处理器.
     */
    protected function makeHandlers(string $level): SyslogHandler
    {
        return $this->setHandlerLineFormatter(
            new SyslogHandler(
                $this->option['channel'],
                $this->option['facility'],
                $this->normalizeMonologLevel($level),
            ),
        );
    }
}
