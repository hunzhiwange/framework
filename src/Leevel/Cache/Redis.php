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

use Leevel\Cache\Redis\IRedis;
use Leevel\Protocol\Pool\Connection;
use Leevel\Protocol\Pool\IConnection;

/**
 * redis 扩展缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.05
 *
 * @version 1.0
 */
class Redis extends Cache implements ICache, IConnection
{
    use Connection;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'time_preset' => [],
        'expire'      => 86400,
        'serialize'   => true,
    ];

    /**
     * 构造函数.
     */
    public function __construct(IRedis $handle, array $option = [])
    {
        parent::__construct($option);
        $this->handle = $handle;
    }

    /**
     * 获取缓存.
     *
     * @param mixed $defaults
     *
     * @return mixed
     */
    public function get(string $name, $defaults = false, array $option = [])
    {
        $option = $this->normalizeOptions($option);

        $data = $this->handle->get(
            $this->getCacheName($name)
        );
        if (false === $data) {
            return $defaults;
        }

        if ($option['serialize'] && is_string($data)) {
            $data = unserialize($data);
        }

        $this->release();

        return $data;
    }

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public function set(string $name, $data, array $option = []): void
    {
        $option = $this->normalizeOptions($option);
        if ($option['serialize']) {
            $data = serialize($data);
        }

        $option['expire'] = $this->cacheTime($name, (int) $option['expire']);
        $this->handle->set(
            $this->getCacheName($name), $data,
            $option['expire'] ? (int) $option['expire'] : null
        );

        $this->release();
    }

    /**
     * 清除缓存.
     */
    public function delete(string $name): void
    {
        $this->handle->delete(
            $this->getCacheName($name)
        );
        $this->release();
    }

    /**
     * 关闭 redis.
     */
    public function close(): void
    {
        $this->handle->close();
    }
}
