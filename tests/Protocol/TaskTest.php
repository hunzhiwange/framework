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

namespace Tests\Protocol;

use Leevel\Protocol\Task;
use Swoole\Process;
use Swoole\Server;
use Tests\TestCase;
use Throwable;

/**
 * Task 测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.31
 *
 * @version 1.0
 */
class TaskTest extends TestCase
{
    public function testTask(): void
    {
        $process = new Process(function (Process $worker) {
            try {
                $swooleServer = new Server('127.0.0.1', 10000);
                $swooleServer->set([
                    'worker_num'      => 1,
                    'task_worker_num' => 1,
                ]);
                $swooleServer->on('Start', function (Server $server) use ($worker) {
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

    public function testTaskCo(): void
    {
        $process = new Process(function (Process $worker) {
            try {
                $swooleServer = new Server('127.0.0.1', 10000);
                $swooleServer->set([
                    'worker_num'      => 1,
                    'task_worker_num' => 1,
                ]);
                $swooleServer->on('Start', function (Server $server) use ($worker) {
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

        $this->assertSame('Exception Thrown: Swoole\\Server::taskCo(): server is not running', $output);
    }
}
