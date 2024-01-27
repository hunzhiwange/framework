<?php

declare(strict_types=1);

namespace Leevel\Server;

use Swoole\Constant;
use Swoole\Process\Manager;
use Swoole\Process\Pool;

use function Swoole\Coroutine\run;

class ProcessManager extends Manager
{
    protected ?\Closure $managerStartEvent = null;

    protected ?\Closure $workerStartEvent = null;

    protected ?\Closure $workerStopEvent = null;

    public function start(): void
    {
        $this->pool = new Pool(\count($this->startFuncMap), $this->ipcType, $this->msgQueueKey, false);

        $this->pool->on(Constant::EVENT_WORKER_START, function (Pool $pool, int $workerId): void {
            [$func, $enableCoroutine] = $this->startFuncMap[$workerId];

            if ($this->workerStartEvent) {
                $workerStartEvent = $this->workerStartEvent;
                $workerStartEvent($pool, $workerId);
            }

            if ($enableCoroutine) {
                // @phpstan-ignore-next-line
                run($func, $pool, $workerId);
            } else {
                $func($pool, $workerId);
            }
        });

        if ($this->workerStopEvent) {
            $this->pool->on(Constant::EVENT_WORKER_STOP, function (Pool $pool, int $workerId): void {
                $workerStopEvent = $this->workerStopEvent;
                // @phpstan-ignore-next-line
                $workerStopEvent($pool, $workerId);
            });
        }

        if ($this->managerStartEvent) {
            $managerStartEvent = $this->managerStartEvent;
            $managerStartEvent($this->pool);
        }

        $this->pool->start();
    }

    public function setManagerStartEvent(?\Closure $managerStartEvent): void
    {
        $this->managerStartEvent = $managerStartEvent;
    }

    public function setWorkerStartEvent(?\Closure $workerStartEvent): void
    {
        $this->workerStartEvent = $workerStartEvent;
    }

    public function setWorkerStopEvent(?\Closure $workerStopEvent): void
    {
        $this->workerStopEvent = $workerStopEvent;
    }
}
