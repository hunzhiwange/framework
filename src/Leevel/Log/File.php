<?php

declare(strict_types=1);

namespace Leevel\Log;

use Monolog\Handler\StreamHandler;
use Psr\Log\LogLevel;

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
        'level' => [
            ILog::DEFAULT_MESSAGE_CATEGORY => LogLevel::DEBUG,
        ],
        'channel' => 'development',
        'name' => 'Y-m-d',
        'path' => '',
        'format' => 'Y-m-d H:i:s u',
        'file_permission' => null,
        'use_locking' => false,
    ];

    /**
     * 创建日志处理器.
     */
    protected function makeHandlers(string $level, string $category): StreamHandler
    {
        // @phpstan-ignore-next-line
        return $this->setHandlerLineFormatter(
            new StreamHandler(
                $this->normalizePath($level, $category),
                // @phpstan-ignore-next-line
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
            throw new \InvalidArgumentException('Path for log has not set.');
        }

        return $this->option['path'].'/'.$this->option['channel'].'.'.
            $level.'/'.($category ? $category.'-' : '').
            date($this->option['name']).'.log';
    }
}
