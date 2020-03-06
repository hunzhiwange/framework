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

namespace Leevel\Cache\Redis;

/**
 * IRedis 接口.
 */
interface IRedis
{
    /**
     * 返回缓存句柄.
     *
     * @return nulll|object
     */
    public function handle(): ?object;

    /**
     * 获取缓存.
     *
     * @return mixed
     */
    public function get(string $name);

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public function set(string $name, $data, int $expire = 0): void;

    /**
     * 清除缓存.
     */
    public function delete(string $name): void;

    /**
     * 缓存是否存在.
     */
    public function has(string $name): bool;

    /**
     * 自增.
     *
     * @return false|int
     */
    public function increase(string $name, int $step = 1, ?int $expire = null);

    /**
     * 自减.
     *
     * @return false|int
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null);

    /**
     * 关闭 redis.
     */
    public function close(): void;
}
