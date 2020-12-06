<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Helper\link;
use Leevel\Filesystem\Helper\link;
use Leevel\Kernel\IApp;

/**
 * apis 文档目录创建软连接到 apis.
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
    protected string $description = 'Create a symbolic link from `apis` to `www/apis`';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        if (file_exists($link = $app->path('www/apis'))) {
            $this->error(sprintf('The `%s` directory already exists.', $link));

            return -1;
        }

        link($path = $app->path('apis'), $link);
        $this->info(sprintf('Linked `%s` directory to `%s` successed.', $path, $link));

        return 0;
    }
}

// import fn.
class_exists(link::class);
