<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * 生产环境性能一键优化.
 */
class Production extends Command
{
    /**
     * 命令名字.
    */
    protected string $name = 'production';

    /**
     * 命令行描述.
    */
    protected string $description = 'Let your app run faster in production';

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->line('Start to optimize you app.');
        $this->callRouter();
        $this->callOption();
        $this->callI18n();
        $this->callView();
        $this->callAutoload();
        $this->line('');
        $this->info('Optimize successed.');

        return 0;
    }

    /**
     * 执行路由缓存.
     */
    protected function callRouter(): void
    {
        $this->line('');
        $this->call('router:cache');
    }

    /**
     * 执行配置缓存.
     */
    protected function callOption(): void
    {
        $this->line('');
        $this->call('option:cache');
    }

    /**
     * 执行语言缓存.
     */
    protected function callI18n(): void
    {
        $this->line('');
        $this->call('i18n:cache');
    }

    /**
     * 执行视图缓存.
     */
    protected function callView(): void
    {
        $this->line('');
        $this->call('view:cache');
    }

    /**
     * 执行自动载入缓存.
     */
    protected function callAutoload(): void
    {
        $this->line('');
        $this->call('autoload', [
            '--composer' => (string) $this->getOption('composer'),
        ]);
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [
            [
                'composer',
                null,
                InputOption::VALUE_OPTIONAL,
                'Where is composer.',
                'composer',
            ],
        ];
    }
}
