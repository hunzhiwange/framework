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
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.09
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait Proxy
{
    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param null|mixed   $value
     */
    public function put($keys, $value = null): void
    {
        $this->proxy()->put($keys, $value);
    }

    /**
     * 缓存存在读取否则重新设置.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     *
     * @return mixed
     */
    public function remember(string $name, $data, array $option = [])
    {
        return $this->proxy()->remember($name, $data, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Cache\ICache
     */
    public function setOption(string $name, $value): ICache
    {
        return $this->proxy()->setOption($name, $value);
    }

    /**
     * 获取缓存.
     *
     * @param string $name
     * @param mixed  $defaults
     * @param array  $option
     *
     * @return mixed
     */
    public function get(string $name, $defaults = false, array $option = [])
    {
        return $this->proxy()->get($name, $defaults, $option);
    }

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     */
    public function set(string $name, $data, array $option = []): void
    {
        $this->proxy()->set($name, $data, $option);
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     */
    public function delete(string $name): void
    {
        $this->proxy()->delete($name);
    }

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->proxy()->handle();
    }

    /**
     * 关闭.
     */
    public function close()
    {
        $this->proxy()->close();
    }

    /**
     * 返回代理.
     *
     * @return \Leevel\Cache\ICache
     */
    public function proxy(): ICache
    {
        return $this->connect();
    }
}
