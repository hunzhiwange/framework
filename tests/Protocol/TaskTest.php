<?php

declare(strict_types=1);

namespace Tests\Protocol;

use Leevel\Protocol\Task;
use Swoole\Process;
use Swoole\Server;
use Tests\TestCase;
use Throwable;

/**
 * @api(
 *     zh-CN:title="投递任务",
 *     path="protocol/task",
 *     zh-CN:description="任务投递是对 Swoole 官方的简单封装。",
 * )
 */
class TaskTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('swoole')) {
            $this->markTestSkipped('Swoole extension must be loaded before use.');
        }
    }

    /**
     * @api(
     *     zh-CN:title="投递异步任务",
     *     zh-CN:description="投递单个异步任务。",
     *     zh-CN:note="",
     * )
     */
    public function testTask(): void
    {
        $process = new Process(function (Process $worker) {
            try {
                $swooleServer = new Server('127.0.0.1', 10000);
                $swooleServer->set([
                    'worker_num'      => 1,
                    'task_worker_num' => 1,
                ]);
                $swooleServer->on('Start', function (Server $server) {
                });
                $swooleServer->on('Receive', function ($req, $rep) {
                });
                $swooleServer->on('Task', function (Server $serv, $task_id, $from_id, $data) {
                });
                $task = new Task($swooleServer);
                $task->task('taskWillNotRun');
            } catch (Throwable $exception) {
                $worker->write('Exception Thrown: '.$exception->getMessage());
            }
            $worker->exit();
        });
        $process->start();
        $output = $process->read();
        Process::wait(true);

        $this->assertSame('Exception Thrown: Swoole\\Server::task(): server is not running', $output);
    }

    /**
     * @api(
     *     zh-CN:title="并发执行任务并进行协程调度",
     *     zh-CN:description="支持多个任务并发执行，底层进行协程调度。",
     *     zh-CN:note="",
     * )
     */
    public function testTaskCo(): void
    {
        $process = new Process(function (Process $worker) {
            try {
                $swooleServer = new Server('127.0.0.1', 10000);
                $swooleServer->set([
                    'worker_num'      => 1,
                    'task_worker_num' => 1,
                ]);
                $swooleServer->on('Start', function (Server $server) {
                });
                $swooleServer->on('Receive', function ($req, $rep) {
                });
                $swooleServer->on('Task', function (Server $serv, $task_id, $from_id, $data) {
                });
                $task = new Task($swooleServer);
                $task->taskCo(['taskWillNotRun']);
            } catch (Throwable $exception) {
                $worker->write('Exception Thrown: '.$exception->getMessage());
            }
            $worker->exit();
        });
        $process->start();
        $output = $process->read();
        Process::wait(true);

        $possiableMessage = [
            'Exception Thrown: Swoole\\Server::taskCo(): server is not running',
            'Exception Thrown: Swoole\\Server::taskCo(): taskCo method can only be used in the worker process',
        ];

        $this->assertTrue(in_array($output, $possiableMessage, true));
    }
}
