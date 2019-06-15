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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Log;

use InvalidArgumentException;
use Leevel\Filesystem\Fso\create_directory;
use function Leevel\Filesystem\Fso\create_directory;

/**
 * 文件日志.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.05
 *
 * @version 1.0
 */
class File extends Log implements ILog
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
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
        'buffer'      => true,
        'buffer_size' => 100,
        'channel'     => 'development',
        'name'        => 'Y-m-d H',
        'size'        => 2097152,
        'path'        => '',
    ];

    /**
     * 存储日志.
     *
     * @param array $data
     */
    public function store(array $data): void
    {
        $level = $data[0][0];

        $this->checkSize($filepath = $this->normalizePath($level));

        foreach ($data as $value) {
            error_log(self::formatMessage(...$value), 3, $filepath);
        }
    }

    /**
     * 格式化日志信息.
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return string
     */
    public static function formatMessage(string $level, string $message, array $context = []): string
    {
        return sprintf(
            '[%s] %s %s: %s'.PHP_EOL,
            date('Y-m-d H:i:s'), $message, $level,
            json_encode($context, JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * 验证日志文件大小.
     *
     * @param string $filePath
     */
    protected function checkSize(string $filePath): void
    {
        $dirname = dirname($filePath);

        if (!is_file($filePath)) {
            create_directory($dirname);
        }

        // 清理文件状态缓存 http://php.net/manual/zh/function.clearstatcache.php
        clearstatcache();

        if (is_file($filePath) &&
            floor($this->option['size']) <= filesize($filePath)) {
            rename($filePath,
                $dirname.'/'.basename($filePath, '.log').'_'.
                (time() - filemtime($filePath)).'.log');
        }
    }

    /**
     * 格式化日志路径.
     *
     * @param string $level
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function normalizePath(string $level): string
    {
        if (!$this->option['path']) {
            $e = 'Path for log has not set.';

            throw new InvalidArgumentException($e);
        }

        return $this->option['path'].'/'.$this->option['channel'].'.'.
            ($level ? $level.'/' : '').date($this->option['name']).'.log';
    }
}

// import fn.
class_exists(create_directory::class);
