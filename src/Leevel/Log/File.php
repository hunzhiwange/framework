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
     *
     * @var array
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
        'format'          => 'Y-m-d H:i:s',
        'file_permission' => null,
        'use_locking'     => false,
    ];

    /**
     * 添加日志处理器到 Monolog.
     */
    protected function addHandlers(string $level, string $category): void
    {
        $this->monolog->setHandlers($this->makeHandlers($level, $category));
    }

    /**
     * 创建日志处理器.
     */
    protected function makeHandlers(string $level, string $category): array
    {
        $streamHandler = new StreamHandler(
            $this->normalizePath($level, $category),
            $this->normalizeMonologLevel($level),
            true,
            $this->option['file_permission'],
            $this->option['use_locking'],
        );

        return [$streamHandler];
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
