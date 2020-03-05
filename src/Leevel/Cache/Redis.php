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
    public function get(string $name, $defaults = false)
    {
        $data = $this->handle->get($this->getCacheName($name));
        if (false === $data) {
            return $defaults;
        }
        $data = $this->decodeData($data);

        $this->release();

        return $data;
    }

    /**
     * 设置缓存.
     *
     * @param mixed $data
     */
    public function set(string $name, $data, ?int $expire = null): void
    {
        $expire = $this->normalizeExpire($name, $expire);
        $this->handle->set($this->getCacheName($name), $this->encodeData($data), $expire);
        $this->release();
    }

    /**
     * 清除缓存.
     */
    public function delete(string $name): void
    {
        $this->handle->delete($this->getCacheName($name));
        $this->release();
    }

    /**
     * 自增.
     *
     * @return false|int
     */
    public function increase(string $name, int $step = 1, ?int $expire = null)
    {
        return $this->doIncreaseOrDecrease('increase', $name, $step, $expire);
    }

    /**
     * 自减.
     *
     * @return false|int
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null)
    {
        return $this->doIncreaseOrDecrease('decrease', $name, $step, $expire);
    }

    /**
     * 关闭 redis.
     */
    public function close(): void
    {
        $this->handle->close();
    }

    /**
     * 处理自增自减.
     *
     * @return false|int
     */
    protected function doIncreaseOrDecrease(string $type, string $name, int $step = 1, ?int $expire = null)
    {
        $name = $this->getCacheName($name);
        $expire = $this->normalizeExpire($name, $expire);
        $result = $this->handle->{$type}($name, $step, $expire);
        $this->release();

        return $result;
    }
}
