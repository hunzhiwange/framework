<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\Link;
use Leevel\Kernel\IApp;

/**
 * 附件目录创建软连接.
 */
class LinkAttachments extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'link:attachments';

    /**
     * 命令行描述.
     */
    protected string $description = 'Create a symbolic link from `storage/attachments` to `www/attachments` and `attachments`';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $source = $app->storagePath('attachments');
        $this->createLink($source, $app->path('www/attachments'));
        $this->createLink($source, $app->path('attachments'));

        return 0;
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
