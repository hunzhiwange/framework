<?php

declare(strict_types=1);

namespace Leevel\Log\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Leevel\Kernel\IApp;

/**
 * log 文件缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'log:clear';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear cache of log';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache log.');
        DeleteDirectory::handle($cacheDir = $app->storagePath('logs'));
        $this->info(sprintf('Log cache files in path %s clear successed.', $cacheDir));

        return 0;
    }
}
