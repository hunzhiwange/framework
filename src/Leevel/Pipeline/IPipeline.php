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

namespace Leevel\Pipeline;

use Closure;

/**
 * IPipeline 接口.
 */
interface IPipeline
{
    /**
     * 将传输对象传入管道.
     *
     * @return \Leevel\Pipeline\IPipeline
     */
    public function send(array $passed): self;

    /**
     * 设置管道中的执行工序.
     *
     * @return \Leevel\Pipeline\IPipeline
     */
    public function through(array $stage): self;

    /**
     * 执行管道工序响应结果.
     *
     * @return mixed
     */
    public function then(?Closure $end = null);
}
