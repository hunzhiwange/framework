<?php

declare(strict_types=1);

namespace Leevel\Server\Console\Base;

use Leevel\Console\Command;
use Swoole\Process;

/**
 * HTTP 服务停止.
 */
abstract class Stop extends Command
{
    use Base;

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->showBaseInfo();
        $config = $this->getServerConfig();
        $this->close((string) ($config['pid_path'] ?? ''));

        return self::SUCCESS;
    }

    /**
     * 停止 Swoole 服务.
     *
     * @throws \InvalidArgumentException
     */
    protected function close(string $pidPath): void
    {
        if (!file_exists($pidPath)) {
            throw new \InvalidArgumentException(sprintf('Pid path `%s` was not found.', $pidPath));
        }

        $pid = (int) file_get_contents($pidPath);
        if (!Process::kill($pid, SIG_DFL)) {
            throw new \InvalidArgumentException(sprintf('Pid `%s` was not found.', $pid));
        }

        Process::kill($pid, SIGTERM);

        if (is_file($pidPath)) {
            unlink($pidPath);
        }

        $message = sprintf('Process %d has stoped.', $pid);
        $this->info($message);
    }
}
