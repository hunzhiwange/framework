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

namespace Leevel\Cache;

/**
 * cache 快捷载入接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.06.05
 *
 * @version 1.0
 */
interface ILoad
{
    /**
     * 载入缓存数据
     * 系统自动存储缓存到内存，可重复执行不会重复载入数据.
     *
     * @param array $names
     * @param array $option
     * @param bool  $force
     *
     * @return array
     */
    public function data(array $names, array $option = [], bool $force = false): array;

    /**
     * 刷新缓存数据.
     *
     * @param array $names
     */
    public function refresh(array $names): void;
}
