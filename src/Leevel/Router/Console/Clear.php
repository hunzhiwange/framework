<?php

declare(strict_types=1);

namespace Leevel\Router\Console;

use Leevel\Console\Command;
use Leevel\Kernel\IApp;

/**
 * 路由缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'router:clear';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear cache of router';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache router.');
        $cachePath = $app->routerCachedPath();
        $this->clearCache($cachePath);
        $this->info(sprintf('Router cache files %s clear succeed.', $cachePath));

        return self::SUCCESS;
    }

    /**
     * 删除缓存.
     */
    protected function clearCache(string $cachePath): void
    {
        if (!is_file($cachePath)) {
            $this->warn(sprintf('Router cache file %s have been cleaned up.', $cachePath));

            return;
        }

        unlink($cachePath);
    }
}
