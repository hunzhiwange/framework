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
use Swoole\Server as SwooleServer;

/**
 * 任务管理.
 */
class Task implements ITask
{
    /**
     * Swoole Server.
     *
     * @var \Swoole\Server
     */
    protected SwooleServer $server;

    /**
     * 构造函数.
     */
    public function __construct(SwooleServer $server)
    {
        $this->server = $server;
    }

    /**
     * 投递异步任务.
     *
     * @return bool|int
     */
    public function task(string $data, int $workerId = -1, ?Closure $finishCallback = null)
    {
        return $this->server->task($data, $workerId, $finishCallback);
    }

    /**
     * 并发执行任务并进行协程调度.
     */
    public function taskCo(array $tasks, ?float $timeout = null): array
    {
        return $this->server->taskCo($tasks, $timeout);
    }
}
