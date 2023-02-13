<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Kernel\IApp;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * PHP 自带的服务器端.
 *
 * @codeCoverageIgnore
 */
class Server extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'server';

    /**
     * 命令行描述.
     */
    protected string $description = 'Start php build-in server';

    /**
     * 应用.
     */
    protected IApp $app; /** @phpstan-ignore-line */

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->app = $app;
        // @phpstan-ignore-next-line
        $this->line("<info>The QueryPHP server started:</info> <http://{$this->getOption('host')}:{$this->getOption('port')}>");
        $this->table(['key', 'value'], [
            ['php', (string) $this->getOption('php')],
            ['server', $this->getServer()],
        ]);
        passthru($this->normalizeCommand(), $status);

        return 0;
    }

    /**
     * 取得命令.
     */
    protected function normalizeCommand(): string
    {
        return sprintf(
            '%s -S %s:%d -t %s',
            escapeshellarg((string) $this->getOption('php')),
            (string) $this->getOption('host'),
            (int) $this->getOption('port'),
            escapeshellarg($this->getServer())
        );
    }

    /**
     * 取得服务入口.
     */
    protected function getServer(): string
    {
        // @phpstan-ignore-next-line
        return $this->getOption('server') ?: $this->app->path('www');
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [
            [
                'host',
                null,
                InputOption::VALUE_OPTIONAL,
                'The host address to be listening on.',
                '127.0.0.1',
            ],
            [
                'port',
                null,
                InputOption::VALUE_OPTIONAL,
                'The port to be listening on.',
                '9527',
            ],
            [
                'server',
                null,
                InputOption::VALUE_OPTIONAL,
                'The server enter.',
                null,
            ],
            [
                'php',
                null,
                InputOption::VALUE_OPTIONAL,
                'Where is php.',
                (new PhpExecutableFinder())->find(false),
            ],
        ];
    }
}
