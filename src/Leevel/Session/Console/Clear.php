<?php

declare(strict_types=1);

namespace Leevel\Session\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Leevel\Kernel\IApp;

/**
 * session 文件缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'session:clear';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear cache of session';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache session.');
        DeleteDirectory::handle($cachePath = $app->storagePath('session'));
        $this->info(sprintf('Session cache files in path %s clear successed.', $cachePath));

        return 0;
    }
}
