<?php

declare(strict_types=1);

namespace Leevel\Debug\DataCollector;

use DebugBar\DataCollector\MessagesCollector;

/**
 * 日志收集器.
 */
class LogsCollector extends MessagesCollector
{
    /**
     * 构造函数.
     */
    public function __construct()
    {
        parent::__construct('logs');
    }
}
