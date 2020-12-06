<?php

declare(strict_types=1);

namespace Leevel\Protocol\Process;

use function Leevel\Filesystem\Helper\traverse_directory;
use Leevel\Filesystem\Helper\traverse_directory;
use Leevel\Option\IOption;
use Leevel\Protocol\IServer;
use Swoole\Coroutine;

/**
 * Swoole 热重载.
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

    /**
     * 配置.
     */
    protected IOption $option;

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
     *
     * @see https://www.swoft.org 参考 Swoft 热更新
     */
    public function __construct(IOption $option)
    {
        $this->option = $option;
        $this->delayCount = (int) $this->option->get('protocol\\hotoverload_delay_count', 2);
        $this->timeInterval = (int) $this->option->get('protocol\\hotoverload_time_interval', 20);
    }

    /**
     * 响应句柄.
     */
    public function handle(IServer $server): void
    {
        Coroutine::create(function () use ($server) {
            while (true) {
                Coroutine::sleep($this->timeInterval / 1000);
                if (true === $this->serverNeedReload()) {
                    $this->reload($server);
                }
            }
        });
    }

    /**
     * 服务是否需要重启.
     */
    protected function serverNeedReload(): bool
    {
        $newMd5Hash = $this->md5Hash();
        $this->count++;

        if ($this->md5Hash && $newMd5Hash !== $this->md5Hash) {
            $this->count = 0;
            $this->reloading = true;
        }

        if (true === $this->reloading && $this->count > $this->delayCount) {
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

        return md5(json_encode($files));
    }

    /**
     * 扫描文件.
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

            traverse_directory($dir, true, function ($file) use (&$files): void {
                if ($file->isFile() && in_array($file->getExtension(), ['php'], true)) {
                    $files[] = $file->getPath().'/'.$file->getFilename();
                }
            });
        }

        return $files;
    }

    /**
     * 重启.
     */
    protected function reload(IServer $server): void
    {
        $this->reloading = false;
        $this->log('The Swoole server is start reloading.');
        $server->getServer()->reload();
    }

    /**
     * 记录日志.
     */
    protected function log(string $log): void
    {
        fwrite(STDOUT, $log.PHP_EOL);
    }
}

// import fn.
class_exists(traverse_directory::class);
