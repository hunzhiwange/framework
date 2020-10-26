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

namespace Leevel\Cache;

use Closure;

/**
 * 缓存接口.
 */
interface ICache
{
    /**
     * 批量设置缓存.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put(array|string $keys, mixed $value = null, ?int $expire = null): void;

    /**
     * 缓存存在读取否则重新设置.
     *
     * @return mixed
     */
    public function remember(string $name, Closure $dataGenerator, ?int $expire = null): mixed;

    /**
     * 获取缓存.
     *
     * @param mixed $defaults
     *
     * @return mixed
     */
    public function get(string $name, mixed $defaults = false);

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public function set(string $name, mixed $data, ?int $expire = null): void;

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
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int;

    /**
     * 自减.
     *
     * @return false|int
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int;

    /**
     * 获取缓存剩余时间.
     *
     * - 不存在的 key:-2
     * - key 存在，但没有设置剩余生存时间:-1
     * - 有剩余生存时间的 key:剩余时间
     */
    public function ttl(string $name): int;

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle(): mixed;

    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * 设置缓存键值正则.
     */
    public function setKeyRegex(string $keyRegex): void;
}
