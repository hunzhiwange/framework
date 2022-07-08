<?php

declare(strict_types=1);

namespace Leevel\Level;

use Closure;

/**
 * 任务管理接口.
 */
interface ITask
{
    /**
     * 投递异步任务.
     */
    public function task(string $data, int $workerId = -1, ?Closure $finishCallback = null): bool|int;

    /**
     * 并发执行Task并进行协程调度.
     */
    public function taskCo(array $tasks, ?float $timeout = null): array;
}
