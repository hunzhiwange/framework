<?php

declare(strict_types=1);

namespace Leevel\Debug\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\Link;
use Leevel\Kernel\IApp;

/**
 * 调试资源目录创建软连接.
 */
class LinkDebugBar extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'link:debugbar';

    /**
     * 命令行描述.
     */
    protected string $description = 'Create a symbolic link from `vendor/maximebf/debugbar/src/DebugBar/Resources` to `www/debugbar` and `debugbar`';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $source = $app->path('vendor/maximebf/debugbar/src/DebugBar/Resources');
        $this->createLink($source, $app->path('www/debugbar'));
        $this->createLink($source, $app->path('debugbar'));

        return self::SUCCESS;
    }

    /**
     * 创建软连接.
     */
    protected function createLink(string $source, string $target): void
    {
        Link::handle($source, $target);
        $this->info(sprintf('Linked `%s` directory to `%s` successed.', $source, $target));
    }
}
