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
     * {@inheritDoc}
     */
    public function get(string $name, mixed $defaults = false): mixed
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
     * {@inheritDoc}
     */
    public function set(string $name, mixed $data, ?int $expire = null): void
    {
        $expire = $this->normalizeExpire($name, $expire);
        $this->handle->set($this->getCacheName($name), $this->encodeData($data), $expire);
        $this->release();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $name): void
    {
        $this->handle->delete($this->getCacheName($name));
        $this->release();
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        $result = $this->handle->has($this->getCacheName($name));
        $this->release();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function increase(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('increase', $name, $step, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function decrease(string $name, int $step = 1, ?int $expire = null): false|int
    {
        return $this->doIncreaseOrDecrease('decrease', $name, $step, $expire);
    }

    /**
     * {@inheritDoc}
     */
    public function ttl(string $name): int
    {
        $result = $this->handle->ttl($name);
        $this->release();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->handle->close();
    }

    /**
     * 处理自增自减.
     */
    protected function doIncreaseOrDecrease(string $type, string $name, int $step = 1, ?int $expire = null): false|int
    {
        $name = $this->getCacheName($name);
        $expire = $this->normalizeExpire($name, $expire);
        $result = $this->handle->{$type}($name, $step, $expire);
        $this->release();

        return $result;
    }
}
