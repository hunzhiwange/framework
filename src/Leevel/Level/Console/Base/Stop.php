<?php

declare(strict_types=1);

namespace Leevel\Level\Console\Base;

use InvalidArgumentException;
use Leevel\Console\Command;
use Leevel\Level\IServer;
use Swoole\Process;

/**
 * Swoole 服务停止.
 */
abstract class Stop extends Command
{
    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion());
        $server = $this->createServer();
        $this->close((string) $server->option['pid_path']);

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
     * 停止 Swoole 服务.
     *
     * @throws \InvalidArgumentException
     */
    protected function close(string $pidPath): void
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

        Process::kill($pid, SIGKILL);
        if (is_file($pidPath)) {
            unlink($pidPath);
        }

        $message = sprintf('Process %d has stoped.', $pid);
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
}
