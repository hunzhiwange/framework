<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;

/**
 * 创建系统支持的软连接.
 */
class Links extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'links';

    /**
     * 命令行描述.
     */
    protected string $description = 'Create all symbolic links';

    /**
     * 响应命令.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function handle(): int
    {
        $this->line('Start to create symbolic links.');
        $this->callApis();
        $this->callStatic();
        $this->callAttachments();
        $this->callDebugBar();
        $this->line('');
        $this->info('Links created successed.');

        return self::SUCCESS;
    }

    /**
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callApis(): void
    {
        $this->line('');
        $this->call('link:apis');
    }

    /**
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callStatic(): void
    {
        $this->line('');
        $this->call('link:static');
    }

    /**
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callAttachments(): void
    {
        $this->line('');
        $this->call('link:attachments');
    }

    /**
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callDebugBar(): void
    {
        $this->line('');
        $this->call('link:debugbar');
    }
}
