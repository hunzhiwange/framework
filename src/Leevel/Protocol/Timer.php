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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Protocol;

use Closure;
use Leevel\Log\ILog;
use Throwable;

/**
 * 定时器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.03
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Timer implements ITimer
{
    /**
     * Log.
     *
     * @var \Leevel\Log\ILog
     */
    protected $log;

    /**
     * 构造函数.
     *
     * @param \Swoole\Server $log
     */
    public function __construct(ILog $log)
    {
        $this->log = $log;
    }

    /**
     * 执行任务支持失败重试.
     *
     * @param \Closure      $work
     * @param int           $perMillisecond
     * @param int           $maxCount
     * @param null|\Closure $failtureCallback
     */
    public function work(Closure $work, int $perMillisecond, int $maxCount, ?Closure $failtureCallback = null): void
    {
        $count = 1;

        swoole_timer_tick($perMillisecond, function (int $timerId) use ($work, &$count, $perMillisecond, $maxCount, $failtureCallback) {
            try {
                $work($count);
                swoole_timer_clear($timerId);
            } catch (Throwable $th) {
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
     *
     * @param \Closure      $work
     * @param int           $perMillisecond
     * @param int           $maxCount
     * @param null|\Closure $failtureCallback
     */
    public function perWork(Closure $work, int $perMillisecond, int $maxCount, ?Closure $failtureCallback = null): void
    {
        $count = 1;

        $timerId = swoole_timer_tick($perMillisecond, function () use ($work, &$count, $failtureCallback) {
            try {
                $work($count);
            } catch (Throwable $th) {
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
