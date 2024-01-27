<?php

declare(strict_types=1);

namespace Leevel\Config\Console;

use Leevel\Config\Load;
use Leevel\Console\Command;
use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Kernel\IApp;

/**
 * 配置缓存.
 */
class Cache extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'config:cache';

    /**
     * 命令行描述.
     */
    protected string $description = 'Merge all config file to a file';

    /**
     * 基础路径.
     */
    protected string $basePath = '';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to cache config.');

        $load = new Load($app->configPath());
        $data = $load->loadData($app);
        $cachePath = $app->configCachedPath();
        $this->basePath = $app->path();
        $this->writeCache($cachePath, $data);

        $this->info(sprintf('Config cache succeed at %s.', $cachePath));

        return self::SUCCESS;
    }

    /**
     * 计算相对路径
     * 忽略未包含在基础路径中的缓存相对路径.
     */
    protected function computeRelativePath(string $cachePath): int
    {
        if (!str_contains($cachePath, $this->basePath)) {
            return -1;
        }

        $relativePath = str_replace($this->basePath.'/', '', $cachePath);
        $relativePath = \dirname($relativePath);

        return \count(explode('/', $relativePath));
    }

    /**
     * 替换相对路径.
     */
    protected function replaceRelativePath(string $data): string
    {
        return str_replace("'".$this->basePath, '$baseDir.\'', $data);
    }

    /**
     * 写入缓存.
     */
    protected function writeCache(string $cachePath, array $data): void
    {
        $relativePathLevel = $this->computeRelativePath($cachePath);
        $isRelativePath = $relativePathLevel > -1;
        $content = '<?php /* '.date('Y-m-d H:i:s').' */ ?>';

        if ($isRelativePath) {
            $content .= PHP_EOL.'<?'.'php $baseDir = dirname(__DIR__, '.$relativePathLevel.'); ?>';
        }

        $content .= PHP_EOL.'<?php return '.
            var_export($data, true).'; ?>';

        if ($isRelativePath) {
            $content = $this->replaceRelativePath($content);
        }

        CreateFile::handle($cachePath, $content);
    }
}
