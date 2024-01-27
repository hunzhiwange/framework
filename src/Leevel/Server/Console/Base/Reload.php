<?php

declare(strict_types=1);

namespace Leevel\Server\Console\Base;

use Leevel\Console\Command;
use Swoole\Process;

/**
 * HTTP 服务重启.
 */
abstract class Reload extends Command
{
    use Base;

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->showBaseInfo();
        $config = $this->getServerConfig();
        $this->reload((string) ($config['pid_path'] ?? ''));

        return self::SUCCESS;
    }

    /**
     * 重启 Swoole 服务.
     *
     * @throws \InvalidArgumentException
     */
    protected function reload(string $pidPath): void
    {
        if (!file_exists($pidPath)) {
            throw new \InvalidArgumentException(sprintf('Pid path `%s` was not found.', $pidPath));
        }

        $pid = (int) file_get_contents($pidPath);
        if (!Process::kill($pid, SIG_DFL)) {
            throw new \InvalidArgumentException(sprintf('Pid `%s` was not found.', $pid));
        }

        Process::kill($pid, SIGUSR1);

        // 开启 opcache 重连后需要刷新
        if (\function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (\function_exists('apc_clear_cache')) {
            apc_clear_cache();
        }

        $message = sprintf('Process %d has reloaded.', $pid);
        $this->info($message);
    }
}
