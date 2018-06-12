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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Pipeline;

/**
 * IPipeline 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.11
 *
 * @version 1.0
 */
interface IPipeline
{
    /**
     * 将传输对象传入管道.
     *
     * @param mixed $passed
     *
     * @return $this
     */
    public function send($passed);

    /**
     * 设置管道中的执行工序.
     *
     * @param dynamic|array $stage
     *
     * @return $this
     */
    public function through($stage);

    /**
     * 执行管道工序响应结果.
     *
     * @param callable $end
     *
     * @return mixed
     */
    public function then(callable $end = null);
}
