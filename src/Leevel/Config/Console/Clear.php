<?php

declare(strict_types=1);

namespace Leevel\Config\Console;

use Leevel\Console\Command;
use Leevel\Kernel\IApp;

/**
 * 配置缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'config:clear';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear cache of config';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache config.');
        $cachePath = $app->configCachedPath();
        $this->clearCache($cachePath);
        $this->info(sprintf('Config cache files %s clear succeed.', $cachePath));

        return self::SUCCESS;
    }

    /**
     * 删除缓存.
     */
    protected function clearCache(string $cachePath): void
    {
        if (!is_file($cachePath)) {
            $this->warn(sprintf('Config cache files %s have been cleaned up.', $cachePath));

            return;
        }

        unlink($cachePath);
    }
}
