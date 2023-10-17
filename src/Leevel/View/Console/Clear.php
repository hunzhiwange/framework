<?php

declare(strict_types=1);

namespace Leevel\View\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\DeleteDirectory;
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
        DeleteDirectory::handle($cachePath = $app->storagePath('theme'));
        $message = sprintf('View cache files in path %s clear succeed.', $cachePath);
        $this->info($message);

        return self::SUCCESS;
    }
}
