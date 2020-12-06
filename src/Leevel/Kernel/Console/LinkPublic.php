<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use function Leevel\Filesystem\Helper\link;
use Leevel\Filesystem\Helper\link;
use Leevel\Kernel\IApp;

/**
 * public 资源目录创建软连接到 www.
 */
class LinkPublic extends Command
{
    /**
     * 命令名字.
    */
    protected string $name = 'link:public';

    /**
     * 命令行描述.
    */
    protected string $description = 'Create a symbolic link from `public` to `www/public`';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        if (file_exists($link = $app->path('www/public'))) {
            $this->error(sprintf('The `%s` directory already exists.', $link));

            return -1;
        }

        link($path = $app->publicPath(), $link);
        $this->info(sprintf('Linked `%s` directory to `%s` successed.', $path, $link));

        return 0;
    }
}

// import fn.
class_exists(link::class);
