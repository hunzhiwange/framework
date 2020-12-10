<?php

declare(strict_types=1);

namespace Leevel\Protocol\Console\Base;

use InvalidArgumentException;
use Leevel\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Leevel\Protocol\IServer;
use Swoole\Process;

/**
 * Swoole 服务重启.
 */
abstract class Reload extends Command
{
    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion());
        $server = $this->createServer();
        $this->reload((string) $server->option['pid_path']);

        return 0;
    }

    /**
     * 创建 server.
     */
    abstract protected function createServer(): IServer;

    /**
     * 返回 Version.
     */
    abstract protected function getVersion(): string;

    /**
     * 重启 Swoole 服务.
     *
     * @throws \InvalidArgumentException
     */
    protected function reload(string $pidPath): void
    {
        if (!file_exists($pidPath)) {
            $e = sprintf('Pid path `%s` was not found.', $pidPath);

            throw new InvalidArgumentException($e);
        }

        $pid = (int) explode(PHP_EOL, file_get_contents($pidPath))[0];
        if (!Process::kill($pid, 0)) {
            $e = sprintf('Pid `%s` was not found.', $pid);

            throw new InvalidArgumentException($e);
        }

        Process::kill($pid, true === $this->getOption('all') ? SIGUSR1 : SIGUSR2);

        // 开启 opcache 重连后需要刷新
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        $message = sprintf('Process %d has reloaded.', $pid);
        $this->info($message);
    }

    /**
     * 返回 QueryPHP Logo.
     */
    protected function getLogo(): string
    {
        return <<<'queryphp'
            _____________                           _______________
             ______/     \__  _____  ____  ______  / /_  _________
              ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
               __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
                 \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
                    \_\                /_/_/         /_/
            queryphp;
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [
            [
                'all',
                'a',
                InputOption::VALUE_NONE,
                'Reload all progress or only task process.',
            ],
        ];
    }
}
