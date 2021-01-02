<?php

declare(strict_types=1);

namespace Leevel\Log;

use InvalidArgumentException;
use Monolog\Handler\StreamHandler;

/**
 * 文件日志.
 */
class File extends Log implements ILog
{
    /**
     * 配置.
     *
     * @see \Monolog\Handler\StreamHandler
     */
    protected array $option = [
        'levels'   => [
            ILog::DEBUG,
            ILog::INFO,
            ILog::NOTICE,
            ILog::WARNING,
            ILog::ERROR,
            ILog::CRITICAL,
            ILog::ALERT,
            ILog::EMERGENCY,
        ],
        'level'           => ILog::DEBUG,
        'buffer'          => true,
        'buffer_size'     => 100,
        'channel'         => 'development',
        'name'            => 'Y-m-d H',
        'path'            => '',
        'format'          => 'Y-m-d H:i:s u',
        'file_permission' => null,
        'use_locking'     => false,
    ];

    /**
     * 添加日志处理器到 Monolog.
     */
    protected function addHandlers(string $level, string $category): void
    {
        if (isset($this->logHandlers[$level][$category])) {
            $logHandlers = $this->logHandlers[$level][$category];
        } else {
            $this->logHandlers[$level][$category] = $logHandlers = [$this->makeHandlers($level, $category)];
        }
        $this->monolog->setHandlers($logHandlers);
    }

    /**
     * 创建日志处理器.
     */
    protected function makeHandlers(string $level, string $category): StreamHandler
    {
        return $this->setHandlerLineFormatter(
            new StreamHandler(
                $this->normalizePath($level, $category),
                $this->normalizeMonologLevel($level),
                true,
                $this->option['file_permission'],
                $this->option['use_locking'],
            ),
        );
    }

    /**
     * 格式化日志路径.
     *
     * @throws \InvalidArgumentException
     */
    protected function normalizePath(string $level, string $category): string
    {
        if (!$this->option['path']) {
            $e = 'Path for log has not set.';

            throw new InvalidArgumentException($e);
        }

        return $this->option['path'].'/'.$this->option['channel'].'.'.
            $level.'/'.($category ? $category.'/' : '').
            date($this->option['name']).'.log';
    }
}
