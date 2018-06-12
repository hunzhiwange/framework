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

namespace Leevel\Cache;

use Leevel\Support\TMacro;

/**
 * cache 仓储.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.02.15
 *
 * @version 1.0
 */
class Cache implements ICache
{
    use TMacro {
        __call as macroCall;
    }

    /**
     * 缓存连接对象
     *
     * @var \Leevel\Cache\IConnect
     */
    protected $connect;

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\IConnect $connect
     */
    public function __construct(IConnect $connect)
    {
        $this->connect = $connect;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $args);
        }

        return $this->connect->{$method}(...$args);
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
    public function get($name, $defaults = false, array $option = [])
    {
        return $this->connect->get($name, $defaults, $option);
    }

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     */
    public function set($name, $data, array $option = [])
    {
        $this->connect->set($name, $data, $option);
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     * @param array  $option
     */
    public function delete($name, array $option = [])
    {
        $this->connect->delete($name, $option);
    }

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->connect->handle();
    }

    /**
     * 关闭.
     */
    public function close()
    {
        $this->connect->close();
    }
}
