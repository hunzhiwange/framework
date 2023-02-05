<?php

declare(strict_types=1);

namespace Leevel\I18n\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\TraverseDirectory;
use Leevel\Kernel\IApp;

/**
 * 语言包缓存清理.
 */
class Clear extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'i18n:clear';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear cache of i18n';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to clear i18n.');

        TraverseDirectory::handle($app->i18nPath(), false, function (\DirectoryIterator $item) use ($app): void {
            if ($item->isDir()) {
                $i18n = $item->getFilename();
                $cachePath = $app->i18nCachedPath($i18n);
                $this->clearCache($cachePath);
                $this->info(sprintf('I18n cache files %s clear successed.', $cachePath));
            }
        });

        $this->info('I18n cache files clear successed.');

        return 0;
    }

    /**
     * 删除缓存.
     */
    protected function clearCache(string $cachePath): void
    {
        if (!is_file($cachePath)) {
            $this->warn(sprintf('I18n cache files %s have been cleaned up.', $cachePath));

            return;
        }

        unlink($cachePath);
    }
}
