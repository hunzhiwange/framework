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

use Leevel\Cache\Redis\IConnect as RedisIConnect;

/**
 * redis 扩展缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.05
 *
 * @version 1.0
 */
class Redis extends Connect implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'time_preset' => [],
        'prefix'      => '_',
        'expire'      => 86400,
        'serialize'   => true,
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\Redis\IConnect $handle
     * @param array                        $option
     */
    public function __construct(RedisIConnect $handle, array $option = [])
    {
        parent::__construct($option);

        $this->handle = $handle;
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
        $option = $this->normalizeOptions($option);

        $data = $this->handle->get(
            $this->getCacheName($name, $option['prefix'])
        );

        if (false === $data) {
            return $defaults;
        }

        if ($option['serialize'] && is_string($data)) {
            $data = unserialize($data);
        }

        return $data;
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
        $option = $this->normalizeOptions($option);

        if ($option['serialize']) {
            $data = serialize($data);
        }

        $option['expire'] = $this->cacheTime($name, $option['expire']);

        $this->handle->set(
            $this->getCacheName($name, $option['prefix']), $data,
            $option['expire'] ? (int) $option['expire'] : null
        );
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     * @param array  $option
     */
    public function delete($name, array $option = [])
    {
        $this->handle->delete(
            $this->getCacheName($name, $this->normalizeOptions($option)['prefix'])
        );
    }

    /**
     * 关闭 redis.
     */
    public function close()
    {
        $this->handle->close();
    }
}
