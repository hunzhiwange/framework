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

use Closure;

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
        return $this->connect->{$method}(...$args);
    }

    /**
     * 批量插入.
     *
     * @param array|string $keys
     * @param mixed        $value
     * @param array        $option
     */
    public function put($keys, $value = null, array $option = []): void
    {
        if (!is_array($keys)) {
            $keys = [$keys => $value];
        }

        foreach ($keys as $key => $value) {
            $this->connect->set($key, $value, $option);
        }
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
        if (false !== ($result = $this->connect->get($name, false, $option))) {
            return $result;
        }

        if (is_object($data) && $data instanceof Closure) {
            $data = $data($name);
        }

        $this->connect->set($name, $data, $option);

        return $data;
    }
}
