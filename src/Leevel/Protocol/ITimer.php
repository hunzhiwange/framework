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
 * 定时器接口.
 *
 * @codeCoverageIgnore
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
