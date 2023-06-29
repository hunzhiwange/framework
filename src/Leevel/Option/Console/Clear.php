<?php

declare(strict_types=1);

namespace Leevel\Option\Console;

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
    protected string $name = 'option:clear';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear cache of option';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear cache option.');
        $cachePath = $app->optionCachedPath();
        $this->clearCache($cachePath);
        $this->info(sprintf('Option cache files %s clear successed.', $cachePath));

        return self::SUCCESS;
    }

    /**
     * 删除缓存.
     */
    protected function clearCache(string $cachePath): void
    {
        if (!is_file($cachePath)) {
            $this->warn(sprintf('Option cache files %s have been cleaned up.', $cachePath));

            return;
        }

        unlink($cachePath);
    }
}
