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

namespace Leevel\Cache\Redis;

/**
 * IConnect 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.29
 *
 * @version 1.0
 */
interface IConnect
{
    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle();

    /**
     * 获取缓存.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name);

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param int    $expire
     */
    public function set($name, $data, ?int $expire = null);

    /**
     * 清除缓存.
     *
     * @param string $name
     */
    public function delete($name);

    /**
     * 关闭 redis.
     */
    public function close();
}
