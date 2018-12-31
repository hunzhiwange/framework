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

use Leevel\Filesystem\Fso;
use Leevel\Option\IOption;
use Leevel\Protocol\IServer;

/**
 * Swoole 热重载.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.13
 *
 * @version 1.0
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
     * 重启延迟时间.
     *
     * @var int
     */
    protected $after = 2;

    /**
     * 配置.
     *
     * @var \Leevel\Option\IOption
     */
    protected $option;

    /**
     * 构造函数.
     *
     * @param \Leevel\Option\IOption $option
     */
    public function __construct(IOption $option)
    {
        $this->option = $option;

        $this->after = (int) $this->option->get('swoole\\hotoverload_after');
    }

    /**
     * 响应句柄.
     *
     * @param \Leevel\Protocol\IServer $server
     */
    public function handle(IServer $server): void
    {
        while (true) {
            sleep(1);

            $newMd5 = $this->md5();
            $this->count++;

            if ($this->md5 && $newMd5 !== $this->md5) {
                $this->log('The Swoole server will reload.');

                $this->count = 0;
                $this->reloading = true;
            }

            if (true === $this->reloading && $this->count > $this->after) {
                $this->reload($server);
            }

            $this->md5 = $newMd5;
        }
    }

    /**
     * 当前文件 MD5 值
     *
     * @return string
     */
    protected function md5(): string
    {
        $md5File = [];

        foreach ($this->files() as $file) {
            $md5File[$file] = md5_file($file);
        }

        ksort($md5File);

        return md5(json_encode($md5File));
    }

    /**
     * 扫描文件.
     *
     * @return array
     */
    protected function files(): array
    {
        $files = [];

        foreach ((array) $this->option->get('swoole\\hotoverload_watch') as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            Fso::listDirectory($dir, true, function ($file) use (&$files) {
                if ($file->isFile()) {
                    if (in_array($file->getExtension(), ['php'], true)) {
                        $files[] = $file->getPath().'/'.$file->getFilename();
                    }
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

        $this->log('The Swoole server has reloaded.');
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
