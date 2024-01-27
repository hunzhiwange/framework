<?php

declare(strict_types=1);

namespace Leevel\Server\Process;

use Leevel\Filesystem\Helper\TraverseDirectory;
use Swoole\Coroutine;

/**
 * 热重载.
 */
class HotOverload extends Process
{
    /**
     * 进程名字.
     */
    protected string $name = 'hot.overload';

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

    protected array $hotoverloadWatch = [];

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
    public function __construct(array $hotoverloadWatch, int $delayCount = 2, int $timeInterval = 20)
    {
        $this->hotoverloadWatch = $hotoverloadWatch;
        $this->delayCount = $delayCount;
        $this->timeInterval = $timeInterval;
    }

    /**
     * 响应句柄.
     */
    public function handle(\Closure $reloadCallback): void
    {
        // @phpstan-ignore-next-line
        while (true) {
            Coroutine::sleep($this->timeInterval / 1000);
            if ($this->serverNeedReload()) {
                $this->reload($reloadCallback);
            }
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
        foreach ($this->hotoverloadWatch as $dir) {
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
