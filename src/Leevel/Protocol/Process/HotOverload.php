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

namespace Leevel\Protocol\Process;

use function Leevel\Filesystem\Fso\list_directory;
use Leevel\Filesystem\Fso\list_directory;
use Leevel\Option\IOption;
use Leevel\Protocol\IServer;
use Swoole\Coroutine;

/**
 * Swoole 热重载.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.13
 *
 * @version 1.0
 *
 * @see https://www.swoft.org 参考 Swoft 热更新
 * @codeCoverageIgnore
 */
class HotOverload extends Process
{
    /**
     * 进程名字.
     *
     * @var string
     */
    protected $name = 'hot.overload';

    /**
     * 时间记录.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * 检测延迟重启计数器.
     *
     * @var int
     */
    protected $delayCount = 2;

    /**
     * 检测间隔时间（毫秒）.
     *
     * @var int
     */
    protected $timeInterval = 20;

    /**
     * 配置.
     *
     * @var \Leevel\Option\IOption
     */
    protected $option;

    /**
     * 文件 MD5 值.
     *
     * @var string
     */
    protected $md5;

    /**
     * 正在 reload.
     *
     * @var bool
     */
    protected $reloading = false;

    /**
     * 构造函数.
     *
     * @param \Leevel\Option\IOption $option
     */
    public function __construct(IOption $option)
    {
        $this->option = $option;
        $this->delayCount = (int) $this->option->get('protocol\\hotoverload_delay_count', 2);
        $this->timeInterval = (int) $this->option->get('protocol\\hotoverload_time_interval', 20);
    }

    /**
     * 响应句柄.
     *
     * @param \Leevel\Protocol\IServer $server
     */
    public function handle(IServer $server): void
    {
        Coroutine::create(function () use ($server) {
            while (true) {
                Coroutine::sleep($this->timeInterval / 1000);

                $newMd5 = $this->md5();
                $this->count++;

                if ($this->md5 && $newMd5 !== $this->md5) {
                    $this->log('The Swoole server will reload.');
                    $this->count = 0;
                    $this->reloading = true;
                }

                if (true === $this->reloading && $this->count > $this->delayCount) {
                    $this->reload($server);
                }

                $this->md5 = $newMd5;
            }
        });
    }

    /**
     * 当前文件 MD5 值
     *
     * @return string
     */
    protected function md5(): string
    {
        $files = [];
        foreach ($this->files() as $file) {
            $files[] = md5_file($file);
        }
        sort($files);

        return md5(json_encode($files));
    }

    /**
     * 扫描文件.
     *
     * @return array
     */
    protected function files(): array
    {
        $files = [];

        foreach ((array) $this->option->get('protocol\\hotoverload_watch', []) as $dir) {
            if (is_file($dir)) {
                $files[] = $dir;

                continue;
            }

            if (!is_dir($dir)) {
                continue;
            }

            list_directory($dir, true, function ($file) use (&$files) {
                if ($file->isFile() && in_array($file->getExtension(), ['php'], true)) {
                    $files[] = $file->getPath().'/'.$file->getFilename();
                }
            });
        }

        return $files;
    }

    /**
     * 重启.
     *
     * @param \Leevel\Protocol\IServer $server
     */
    protected function reload(IServer $server): void
    {
        $this->reloading = false;
        $this->log('The Swoole server is start reloading.');
        $server->getServer()->reload();
    }

    /**
     * 记录日志.
     *
     * @param string $log
     */
    protected function log(string $log): void
    {
        fwrite(STDOUT, $log.PHP_EOL);
    }
}

// import fn.
class_exists(list_directory::class);
