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
        'level'   => [
            ILog::DEFAULT_MESSAGE_CATEGORY => ILog::LEVEL_DEBUG,
        ],
        'buffer'          => true,
        'buffer_size'     => 100,
        'channel'         => 'development',
        'name'            => 'Y-m-d',
        'path'            => '',
        'format'          => 'Y-m-d H:i:s u',
        'file_permission' => null,
        'use_locking'     => false,
    ];

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
            $level.'/'.($category ? $category.'-' : '').
            date($this->option['name']).'.log';
    }
}
