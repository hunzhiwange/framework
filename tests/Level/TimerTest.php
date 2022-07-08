<?php

declare(strict_types=1);

namespace Tests\Level;

use Exception;
use Leevel\Log\ILog;
use Leevel\Level\Timer;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="毫秒定时器",
 *     path="level/timer",
 *     zh-CN:description="毫秒定时器是对 Swoole 官方的简单封装。",
 * )
 */
class TimerTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('swoole')) {
            $this->markTestSkipped('Swoole extension must be loaded before use.');
        }
    }

    /**
     * @api(
     *     zh-CN:title="执行任务",
     *     zh-CN:description="执行任务过程中不抛出异常则为一次通过，有异常支持重试。",
     *     zh-CN:note="",
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
     *     zh-CN:title="执行任务失败重试",
     *     zh-CN:description="执行任务过程中抛出异常则为失败，失败会支持重试，到达次数后将丢弃。",
     *     zh-CN:note="",
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
