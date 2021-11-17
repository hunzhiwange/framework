<?php

declare(strict_types=1);

namespace Leevel\View\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Helper\delete_directory;
use Leevel\Filesystem\Helper\delete_directory;
use Leevel\Kernel\IApp;

/**
 * 视图缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'view:clear';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear cache of view';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache view.');
        delete_directory($cachePath = $app->storagePath('theme'));
        $message = sprintf('View cache files in path %s clear successed.', $cachePath);
        $this->info($message);

        return 0;
    }
}

// import fn.
class_exists(delete_directory::class);
