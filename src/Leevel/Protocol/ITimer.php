<?php

declare(strict_types=1);

namespace Leevel\Protocol;

use Closure;

/**
 * 定时器接口.
 */
interface ITimer
{
    /**
     * 执行任务支持失败重试.
     */
    public function work(Closure $work, int $perMillisecond, int $maxCount, ?Closure $failtureCallback = null): void;

    /**
     * 每隔一段时间执行同一任务.
     */
    public function perWork(Closure $work, int $perMillisecond, int $maxCount, ?Closure $failtureCallback = null): void;
}
