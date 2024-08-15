<?php

declare(strict_types=1);

namespace Leevel\Server\Process;

use Leevel\Filesystem\Helper\TraverseDirectory;
use Leevel\Server\IServer;
use Leevel\Support\Str\Camelize;
use Swoole\Process as SwooleProcess;
use Swoole\Timer;

/**
 * 热重载.
 */
class HotOverload extends Process
{
    /**
     * 进程名字.
     */
    protected string $name = 'HOT.OVERLOAD';

    /**
     * 时间记录.
     */
    protected int $count = 0;

    /**
     * 检测延迟重启计数器.
     */
    protected int $delayCount = 2;

    /**
     * 检测间隔时间（毫秒）.
     */
    protected int $timeInterval = 20;

    /**
     * 源代码监听目录.
     *
     * - 该目录的文件发生变化工作进程会重启
     */
    protected array $hotOverloadWatch = [];

    /**
     * 文件信息表.
     */
    protected array $fileTable = [];

    /**
     * 正在 reload.
     */
    protected bool $reloading = false;

    /**
     * 是否需要重启.
     */
    protected bool $shouldReload = false;

    /**
     * 是否为首次检测.
     */
    protected bool $firstCheckReady = false;

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        $props = [
            'hot_overload_watch',
            'delay_count',
            'time_interval',
        ];

        foreach ($props as $k) {
            if (isset($config[$k])) {
                $prop = Camelize::handle($k);
                $this->{$prop} = $config[$k];
            }
        }
    }

    public function handle(IServer $server): void
    {
        Timer::tick($this->timeInterval, function () use ($server): void {
            if ($this->serverNeedReload()) {
                $this->reload(function () use ($server): void {
                    $this->reloadWorkerServer($server);
                });
            }
        });
    }

    /**
     * 重启工作服务.
     */
    protected function reloadWorkerServer(IServer $server): void
    {
        // 开发阶段,热重启立刻强制杀掉所有进程,全部重启拉起新的进程
        foreach ($server->getProcessStatisticTable() as $v) {
            SwooleProcess::kill($v['pid'], SIGKILL);
        }
    }

    /**
     * 服务是否需要重启.
     */
    protected function serverNeedReload(): bool
    {
        $this->checkShouldReload();
        ++$this->count;

        if ($this->shouldReload) {
            $this->count = 0;
            $this->reloading = true;
        }

        if ($this->reloading && $this->count > $this->delayCount) {
            return true;
        }

        return false;
    }

    protected function checkShouldReload(): void
    {
        $this->shouldReload = false;

        foreach ($this->hotOverloadWatch as $dir) {
            if (is_file($dir)) {
                $file = new \SplFileInfo($dir);
                $this->checkFileShouldReload($file);

                continue;
            }

            if (!is_dir($dir)) {
                continue;
            }

            TraverseDirectory::handle(
                $dir,
                true,
                function (\DirectoryIterator $file): void {
                    if ($file->isFile()
                        && \in_array($file->getExtension(), ['php'], true)) {
                        $this->checkFileShouldReload($file);
                    }
                }
            );
        }

        if (!$this->firstCheckReady) {
            $this->firstCheckReady = true;
        }
    }

    protected function checkFileShouldReload(\SplFileInfo $file): void
    {
        $inode = $file->getInode();
        $time = $file->getMTime();

        // 删除文件不会检测
        if (isset($this->fileTable[$inode])) {
            // 修改文件
            $oldTime = $this->fileTable[$inode];
            if ($oldTime !== $time) {
                $this->fileTable[$inode] = $time;
                $this->shouldReload = true;
            }
        } else {
            // 新增文件
            $this->fileTable[$inode] = $time;
            // 第一次检测不重启
            if ($this->firstCheckReady) {
                $this->shouldReload = true;
            }
        }
    }

    /**
     * 重启.
     */
    protected function reload(\Closure $reloadCallback): void
    {
        $this->reloading = false;
        $reloadCallback();
    }
}
