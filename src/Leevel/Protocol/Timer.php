<?php

declare(strict_types=1);

namespace Leevel\Protocol;

use Closure;
use Exception;
use Leevel\Log\ILog;

/**
 * 定时器.
 */
class Timer implements ITimer
{
    /**
     * Log.
     */
    protected ILog $log;

    /**
     * 构造函数.
     */
    public function __construct(ILog $log)
    {
        $this->log = $log;
    }

    /**
     * {@inheritDoc}
     */
    public function work(Closure $work, int $perMillisecond, int $maxCount, ?Closure $failtureCallback = null): void
    {
        $count = 1;
        swoole_timer_tick($perMillisecond, function (int $timerId) use ($work, &$count, $maxCount, $failtureCallback) {
            try {
                $work($count);
                swoole_timer_clear($timerId);
            } catch (Exception) {
                if ($count >= $maxCount) {
                    swoole_timer_clear($timerId);

                    if ($failtureCallback) {
                        $failtureCallback($work, $count);
                    }

                    $message = sprintf('Work was failed after `%d` tries.', $maxCount);
                    $this->log->error($message);
                }
            }

            $count++;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function perWork(Closure $work, int $perMillisecond, int $maxCount, ?Closure $failtureCallback = null): void
    {
        $count = 1;
        $timerId = swoole_timer_tick($perMillisecond, function () use ($work, &$count, $failtureCallback) {
            try {
                $work($count);
            } catch (Exception $e) {
                if ($failtureCallback) {
                    $failtureCallback($work, $count);
                }

                $message = sprintf('Work was failed at `%d` time.', $count);
                $this->log->error($message);
            }

            $count++;
        });

        swoole_timer_after($maxCount * $perMillisecond, function () use ($timerId) {
            swoole_timer_clear($timerId);
        });
    }
}
