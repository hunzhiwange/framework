<?php

declare(strict_types=1);

namespace Leevel\Log;

use Monolog\Handler\SyslogHandler;
use Psr\Log\LogLevel;

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
    protected array $config = [
        'level' => [
            ILog::DEFAULT_MESSAGE_CATEGORY => LogLevel::DEBUG,
        ],
        'channel' => 'development',
        'facility' => LOG_USER,
        'format' => 'Y-m-d H:i:s u',
    ];

    /**
     * 创建日志处理器.
     */
    protected function makeHandlers(string $level, string $category): SyslogHandler
    {
        // @phpstan-ignore-next-line
        return $this->setHandlerLineFormatter(
            new SyslogHandler(
                $this->config['channel'],
                $this->config['facility'],
                // @phpstan-ignore-next-line
                $this->normalizeMonologLevel($level),
            ),
        );
    }
}
