<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * 执行任务支持失败重试.
     */
    public function work(Closure $work, int $perMillisecond, int $maxCount, ?Closure $failtureCallback = null): void
    {
        $count = 1;
        swoole_timer_tick($perMillisecond, function (int $timerId) use ($work, &$count, $maxCount, $failtureCallback) {
            try {
                $work($count);
                swoole_timer_clear($timerId);
            } catch (Exception $e) {
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
     * 每隔一段时间执行同一任务.
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
