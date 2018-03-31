<?php
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
namespace Queryyetsimple\Swoole\ToolKit;

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
     * @var resource
     */
    protected $inotify;
    protected $pid;

    protected $reloadFileTypes = array('.php' => true);
    protected $watchFiles = array();
    protected $afterNSeconds = 10;

    /**
     * 正在reload
     */
    protected $reloading = false;

    protected $events;

    /**
     * 根目录
     * @var array
     */
    protected $rootDirs = array();

    function putLog($log)
    {
        $_log = "[".date('Y-m-d H:i:s')."]\t".$log."\n";
        echo $_log;
    }

    /**
     * 构造函数
     * 
     * @param int $serverPid
     * @return void
     * @throws \Queryyetsimple\Swoole\ToolKit\AutoReloadException
     */
    public function __construct(int $serverPid)
    {
        $this->pid = $serverPid;

        // if (posix_kill($serverPid, 0) === false) {
        //     throw new AutoReloadException("Process#$serverPid not found.");
        // }

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
                ]) {
                    $fileType = strrchr($ev['name'], '.');

                    // 非重启类型
                    if (! isset($this->reloadFileTypes[$fileType])) {
                        continue;
                    }
                }

                // 正在 reload，不再接受任何事件，冻结 10 秒
                if (! $this->reloading) {
                    $this->putLog('after 10 seconds reload the server');

                    // 有事件发生了，进行重启
                    swoole_timer_after($this->afterNSeconds * 1000, [$this, 'reload']);

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
     * 添加文件类型
     * @param $type
     */
    function addFileType($type)
    {
        $type = trim($type, '.');
        $this->reloadFileTypes['.' . $type] = true;
    }

    /**
     * 添加事件
     * @param $inotifyEvent
     */
    function addEvent($inotifyEvent)
    {
        $this->events |= $inotifyEvent;
    }

    /**
     * 清理所有inotify监听
     */
    function clearWatch()
    {
        foreach($this->watchFiles as $wd)
        {
            inotify_rm_watch($this->inotify, $wd);
        }
        $this->watchFiles = array();
    }

    /**
     * @param $dir
     * @param bool $root
     * @return bool
     * @throws \Queryyetsimple\Swoole\ToolKit\AutoReloadException
     */
    function watch($dir, $root = true)
    {
        //目录不存在
        if (!is_dir($dir))
        {
            throw new AutoReloadException("[$dir] is not a directory.");
        }
        //避免重复监听
        if (isset($this->watchFiles[$dir]))
        {
            return false;
        }
        //根目录
        if ($root)
        {
            $this->rootDirs[] = $dir;
        }

        $wd = inotify_add_watch($this->inotify, $dir, $this->events);
        $this->watchFiles[$dir] = $wd;

        $files = scandir($dir);
        foreach ($files as $f)
        {
            if ($f == '.' or $f == '..')
            {
                continue;
            }
            $path = $dir . '/' . $f;
            //递归目录
            if (is_dir($path))
            {
                $this->watch($path, false);
            }
            //检测文件类型
            $fileType = strrchr($f, '.');
            if (isset($this->reloadFileTypes[$fileType]))
            {
                $wd = inotify_add_watch($this->inotify, $path, $this->events);
                $this->watchFiles[$path] = $wd;
            }
        }
        return true;
    }



        function reload()
    {
        $this->putLog("reloading");
        //向主进程发送信号
        posix_kill($this->pid, SIGUSR1);
        //清理所有监听
        $this->clearWatch();
        //重新监听
        foreach($this->rootDirs as $root)
        {
            $this->watch($root);
        }
        //继续进行reload
        $this->reloading = false;
    }
}
