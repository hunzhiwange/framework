<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Helper\link;
use Leevel\Filesystem\Helper\link;
use Leevel\Kernel\IApp;

/**
 * Swagger UI 文档目录创建软连接.
 */
class LinkApis extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'link:apis';

    /**
     * 命令行描述.
     */
    protected string $description = 'Create a symbolic link from `assets/apis` to `www/apis` and `apis`';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $source = $app->path('assets/apis');
        $this->createLink($source, $app->path('www/apis'));
        $this->createLink($source, $app->path('apis'));

        return 0;
    }

    /**
     * 创建软连接.
     */
    protected function createLink(string $source, string $target): void
    {
        link($source, $target);
        $this->info(sprintf('Linked `%s` directory to `%s` successed.', $source, $target));
    }
}

// import fn.
class_exists(link::class);
