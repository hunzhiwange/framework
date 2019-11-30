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

namespace Tests\Protocol;

use Exception;
use Leevel\Log\ILog;
use Leevel\Protocol\Timer;
use Tests\TestCase;

/**
 * Timer 测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.31
 *
 * @version 1.0
 *
 * @api(
 *     title="毫秒定时器",
 *     path="protocol/timer",
 *     description="毫秒定时器是对 Swoole 官方的简单封装。",
 * )
 */
class TimerTest extends TestCase
{
    /**
     * @api(
     *     title="执行任务",
     *     description="执行任务过程中不抛出异常则为一次通过，有异常支持重试。",
     *     note="",
     * )
     */
    public function testTimer(): void
    {
        /** @var \Leevel\Log\ILog $log */
        $log = $this->createMock(ILog::class);
        $timer = new Timer($log);
        $errorCount = 0;
        $taskLog = __DIR__.'/task.log';

        $timer->work(function () use (&$errorCount, $taskLog): void {
            $errorCount++;
            file_put_contents($taskLog, '.count '.$errorCount, FILE_APPEND);

            if (1 === $errorCount) {
                defer(function () use ($taskLog) {
                    $this->assertSame('.count 1', file_get_contents($taskLog));
                    unlink($taskLog);
                });
            }
        }, 10, 5);

        $this->assertSame(1, 1);
    }

    /**
     * @api(
     *     title="执行任务失败重试",
     *     description="执行任务过程中抛出异常则为失败，失败会支持重试，到达次数后将丢弃。",
     *     note="",
     * )
     */
    public function testTimerError(): void
    {
        /** @var \Leevel\Log\ILog $log */
        $log = $this->createMock(ILog::class);
        $timer = new Timer($log);
        $errorCount = 0;
        $taskLog = __DIR__.'/taskError.log';

        $timer->work(function () use (&$errorCount, $taskLog): void {
            $errorCount++;
            file_put_contents($taskLog, '.count '.$errorCount, FILE_APPEND);

            if (5 === $errorCount) {
                defer(function () use ($taskLog) {
                    $this->assertSame('.count 1.count 2.count 3.count 4.count 5', file_get_contents($taskLog));
                    unlink($taskLog);
                });
            }

            throw new Exception('Failed test');
        }, 10, 5);

        $this->assertSame(1, 1);
    }
}
