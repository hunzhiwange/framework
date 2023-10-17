<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * 开发模式清理系统缓存.
 */
class Development extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'development';

    /**
     * 命令行描述.
     */
    protected string $description = 'Clear all caches for development mode';

    /**
     * 响应命令.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    public function handle(): int
    {
        $this->line('Start to clears caches.');
        $this->callI18n();
        $this->callLog();
        $this->callOption();
        $this->callRouter();
        $this->callSession();
        $this->callView();
        $this->callAutoload();
        $this->line('');
        $this->info('Caches cleared succeed.');

        return self::SUCCESS;
    }

    /**
     * 执行清理 i18n 缓存.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callI18n(): void
    {
        $this->line('');
        $this->call('i18n:clear');
    }

    /**
     * 执行清理 log 缓存.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callLog(): void
    {
        $this->line('');
        $this->call('log:clear');
    }

    /**
     * 执行清理 option 缓存.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callOption(): void
    {
        $this->line('');
        $this->call('option:clear');
    }

    /**
     * 执行清理 router 缓存.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callRouter(): void
    {
        $this->line('');
        $this->call('router:clear');
    }

    /**
     * 执行清理 session 缓存.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callSession(): void
    {
        $this->line('');
        $this->call('session:clear');
    }

    /**
     * 执行清理 view 缓存.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callView(): void
    {
        $this->line('');
        $this->call('view:clear');
    }

    /**
     * 执行 autoload 缓存.
     *
     * @throws \Symfony\Component\Console\Exception\ExceptionInterface
     */
    protected function callAutoload(): void
    {
        $this->line('');
        $this->call('autoload', [
            '--composer' => (string) $this->getOption('composer'),
            '--dev' => true,
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
