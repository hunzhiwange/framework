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

use SplStack;

/**
 * 对象池接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.14
 *
 * @version 1.0
 */
interface IPool
{
    /**
     * 获取一个对象.
     *
     * @param string $className
     * @param array  $args
     *
     * @return \Object
     */
    public function get(string $className, ...$args);

    /**
     * 返还一个对象.
     *
     * @param \Object $obj
     */
    public function back($obj): void;

    /**
     * 获取对象栈.
     *
     * @param string $className
     *
     * @return \SplStack
     */
    public function pool(string $className): SplStack;

    /**
     * 获取对象池数据.
     *
     * @return array
     */
    public function getPools(): array;
}
