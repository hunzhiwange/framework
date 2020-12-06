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
     */
    public function handle(): int
    {
        $this->line('Start to create symbolic links.');
        $this->callApis();
        $this->callPublic();
        $this->callStorage();
        $this->callDebugBar();
        $this->line('');
        $this->info('Links created successed.');

        return 0;
    }

    /**
     * 执行创建 apis 软连接.
     */
    protected function callApis(): void
    {
        $this->line('');
        $this->call('link:apis');
    }

    /**
     * 执行创建 public 软连接.
     */
    protected function callPublic(): void
    {
        $this->line('');
        $this->call('link:public');
    }

    /**
     * 执行创建 storage 软连接.
     */
    protected function callStorage(): void
    {
        $this->line('');
        $this->call('link:storage');
    }

    /**
     * 执行创建 debugbar 软连接.
     */
    protected function callDebugBar(): void
    {
        $this->line('');
        $this->call('link:debugbar');
    }
}
