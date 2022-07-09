<?php

declare(strict_types=1);

namespace Leevel\Level;

use Closure;
use Swoole\Server as SwooleServer;

/**
 * 任务管理.
 */
class Task implements ITask
{
    /**
     * Swoole Server.
     */
    protected SwooleServer $server;

    /**
     * 构造函数.
     */
    public function __construct(SwooleServer $server)
    {
        $this->server = $server;
    }

    /**
     * {@inheritDoc}
     */
    public function task(string $data, int $workerId = -1, ?Closure $finishCallback = null): bool|int
    {
        return $this->server->task($data, $workerId, $finishCallback);
    }

    /**
     * {@inheritDoc}
     */
    public function taskCo(array $tasks, ?float $timeout = null): array
    {
        return $this->server->taskCo($tasks, $timeout);
    }
}
