<?php declare(strict_types=1);
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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\Protocol\ToolKit;

/**
 * swoole 自动重启
 * This class borrows heavily from the swoole auto_reload and is part of the swoole package
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.31
 * @version 1.0
 * @see swoole/auto_reload (https://github.com/swoole/auto_reload)
 */
class AutoReload
{

    /**
     * inotify
     * 
     * @var resource
     */
    protected $inotify;

    /**
     * 监听的进程 ID
     * 
     * @var int
     */
    protected $pid;

    /**
     * 监听文件类型
     * 
     * @var array
     */
    protected $reloadFileTypes = [
        '.php' => true
    ];

    /**
     * 监听的文件
     * 
     * @var array
     */
    protected $watchFiles = [];

    /**
     * 多少秒后重启
     * 
     * @var integer
     */
    protected $afterSeconds = 10;

    /**
     * 正在 reload
     *
     * @var bool
     */
    protected $reloading = false;

    /**
     * 事件
     * 
     * @var int
     */
    protected $events;

    /**
     * 根目录
     * 
     * @var array
     */
    protected $rootDirs = [];

    /**
     * 构造函数
     * 
     * @param int $pid
     * @return void
     * @throws \Leevel\Protocol\ToolKit\AutoReloadException
     */
    public function __construct(int $pid)
    {
        if (! extension_loaded('inotify')) {
           throw new AutoReloadException('PHP extension inotify is not install.'); 
        }

        $this->pid = $pid;

        if (posix_getsid($pid) === false) {
            throw new AutoReloadException(sprintf('Process %d was not found.', $pid));
        }

        $this->inotify = inotify_init();
        $this->events = IN_MODIFY | IN_DELETE | IN_CREATE | IN_MOVE;

        swoole_event_add($this->inotify, function ($ifd) {
            $events = inotify_read($this->inotify);

            if (! $events) {
                return;
            }

            foreach ($events as $ev) {
                if ($ev['mask'] == IN_IGNORED) {
                    continue;
                } elseif (in_array($ev['mask'], [
                    IN_CREATE,
                    IN_DELETE,
                    IN_MODIFY,
                    IN_MOVED_TO,
                    IN_MOVED_FROM
                ])) {
                    $fileType = strrchr($ev['name'], '.');

                    // 非重启类型
                    if (! isset($this->reloadFileTypes[$fileType])) {
                        continue;
                    }
                }

                // 正在 reload，不再接受任何事件，冻结 10 秒
                if (! $this->reloading) {
                    $this->putLog('After 10 seconds reload the server');

                    // 有事件发生了，进行重启
                    swoole_timer_after($this->afterSeconds * 1000, [$this, 'reload']);

                    $this->reloading = true;
                }
            }
        });
    }

    /**
     * 执行句柄
     * 
     * @return void
     */
    public function run()
    {
        swoole_event_wait();
    }

    /**
     * 监听目录
     * 
     * @param string $dir
     * @param bool $root
     * @return bool
     * @throws \Leevel\Protocol\ToolKit\AutoReloadException
     */
    public function watch(string $dir, bool $root = true)
    {
        // 目录不存在
        if (! is_dir($dir)) {
            throw new AutoReloadException(sprintf('%s is not a directory.', $dir));
        }

        // 避免重复监听
        if (isset($this->watchFiles[$dir])) {
            return false;
        }

        // 根目录
        if ($root) {
            $this->rootDirs[] = $dir;
        }

        $wd = inotify_add_watch($this->inotify, $dir, $this->events);
        $this->watchFiles[$dir] = $wd;

        $files = scandir($dir);
        foreach ($files as $f) {
            if ($f == '.' or $f == '..') {
                continue;
            }

            $path = $dir . '/' . $f;

            // 递归目录
            if (is_dir($path)) {
                $this->watch($path, false);
            }

            // 检测文件类型
            $fileType = strrchr($f, '.');

            if (isset($this->reloadFileTypes[$fileType])) {
                $wd = inotify_add_watch($this->inotify, $path, $this->events);
                $this->watchFiles[$path] = $wd;
            }
        }

        return true;
    }

    /**
     * 添加文件类型
     * 
     * @param string $type
     * @return void
     */
    public function addFileType(string $type)
    {
        $type = trim($type, '.');
        $this->reloadFileTypes['.' . $type] = true;
    }

    /**
     * 设置多少秒后重启时间
     * 
     * @param int $seconds
     * @return void
     */
    public function setAfterSeconds(int $seconds)
    {
        $this->afterSeconds = $seconds;
    }

    /**
     * 添加事件
     * 
     * @param int $inotifyEvent
     * @return void
     */
    public function addEvent(int $inotifyEvent)
    {
        $this->events |= $inotifyEvent;
    }

    /**
     * 清理所有 inotify 监听
     *
     * @return void
     */
    public function clearWatch()
    {
        foreach ($this->watchFiles as $wd) {
            inotify_rm_watch($this->inotify, $wd);
        }

        $this->watchFiles = [];
    }

    /**
     * 重启
     * 
     * @return void
     */
    protected function reload()
    {
        $this->putLog('The swoole is reloading.');

        // 向主进程发送信号
        $result = posix_kill($this->pid, SIGUSR1);
        if ($result) {
            $this->putLog('The swoole reload success.');
        } else {
            $this->putLog('The swoole reload failed.');
        }

        // 清理所有监听
        $this->clearWatch();

        // 重新监听
        foreach ($this->rootDirs as $root) {
            $this->watch($root);
        }

        // 继续进行 reload
        $this->reloading = false;
    }

    /**
     * 记录日志
     * 
     * @param string $log
     * @return void
     */
    protected function putLog(string $log)
    {
        fwrite(STDOUT, $log . PHP_EOL);
    }
}
