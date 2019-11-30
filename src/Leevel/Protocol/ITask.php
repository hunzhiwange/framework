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

/**
 * 任务管理接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.07.01
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
interface ITask
{
    /**
     * 投递异步任务.
     *
     * @param string        $data
     * @param int           $workerId
     * @param null|\Closure $finishCallback
     *
     * @return bool|int
     */
    public function task(string $data, int $workerId = -1, ?Closure $finishCallback = null);

    /**
     * 并发执行Task并进行协程调度.
     *
     * @param array      $tasks
     * @param null|float $timeout
     *
     * @return array
     */
    public function taskCo(array $tasks, ?float $timeout = null): array;
}
