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

/**
 * 缓存接口.
 */
interface ICache
{
    /**
     * 批量设置缓存.
     *
     * @param array|string $keys
     * @param null|mixed   $value
     */
    public function put($keys, $value = null): void;

    /**
     * 缓存存在读取否则重新设置.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function remember(string $name, $data, array $option = []);

    /**
     * 设置配置.
     *
     * @param mixed $value
     *
     * @return \Leevel\Cache\ICache
     */
    public function setOption(string $name, $value): self;

    /**
     * 获取缓存.
     *
     * @param mixed $defaults
     *
     * @return mixed
     */
    public function get(string $name, $defaults = false, array $option = []);

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public function set(string $name, $data, array $option = []): void;

    /**
     * 清除缓存.
     */
    public function delete(string $name): void;

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle();

    /**
     * 关闭.
     */
    public function close(): void;
}
