<?php

declare(strict_types=1);

namespace Leevel\Server\Process;

use Leevel\Filesystem\Helper\TraverseDirectory;
use Leevel\Server\IServer;
use Leevel\Support\Str\Camelize;
use Swoole\Coroutine;
use Swoole\Process as SwooleProcess;

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
     * 文件 MD5 值.
     */
    protected ?string $md5Hash = null;

    /**
     * 正在 reload.
     */
    protected bool $reloading = false;

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

    /**
     * {@inheritDoc}
     */
    public function handle(IServer $server): void
    {
        // @phpstan-ignore-next-line
        while (true) {
            Coroutine::sleep($this->timeInterval / 1000);
            if ($this->serverNeedReload()) {
                $this->reload(function () use ($server): void {
                    $this->reloadWorkerServer($server);
                });
            }
        }
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
        $newMd5Hash = $this->md5Hash();
        ++$this->count;

        if ($this->md5Hash && $newMd5Hash !== $this->md5Hash) {
            $this->count = 0;
            $this->reloading = true;
        }

        if ($this->reloading && $this->count > $this->delayCount) {
            return true;
        }

        $this->md5Hash = $newMd5Hash;

        return false;
    }

    /**
     * 当前文件 MD5 值.
     */
    protected function md5Hash(): string
    {
        $files = [];
        foreach ($this->files() as $file) {
            $files[] = md5_file($file);
        }
        sort($files);

        return md5(json_encode($files, JSON_THROW_ON_ERROR));
    }

    /**
     * 扫描文件.
     */
    protected function files(): array
    {
        $files = [];
        foreach ($this->hotOverloadWatch as $dir) {
            if (is_file($dir)) {
                $files[] = $dir;

                continue;
            }

            if (!is_dir($dir)) {
                continue;
            }

            TraverseDirectory::handle($dir, true, function (\DirectoryIterator $file) use (&$files): void {
                if ($file->isFile() && \in_array($file->getExtension(), ['php'], true)) {
                    $files[] = $file->getPath().'/'.$file->getFilename();
                }
            });
        }

        return $files;
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
