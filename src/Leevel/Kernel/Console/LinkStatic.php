<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\Link;
use Leevel\Kernel\IApp;

/**
 * 静态资源目录创建软连接.
 */
class LinkStatic extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'link:static';

    /**
     * 命令行描述.
     */
    protected string $description = 'Create a symbolic link from `assets/static` to `www/static` and `static`';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $source = $app->path('assets/static');
        $this->createLink($source, $app->path('www/static'));
        $this->createLink($source, $app->path('static'));

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
