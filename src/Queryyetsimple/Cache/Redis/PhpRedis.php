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

namespace Leevel\Cache\Redis;

use Redis;
use RuntimeException;

/**
 * php redis 扩展缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.29
 *
 * @version 1.0
 */
class PhpRedis implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'host'        => '127.0.0.1',
        'port'        => 6379,
        'password'    => '',
        'select'      => 0,
        'timeout'     => 0,
        'persistent'  => false,
    ];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        if (!extension_loaded('redis')) {
            throw new RuntimeException('Redis extension must be loaded before use.');
        }

        $this->option = array_merge($this->option, $option);

        $this->handle = $this->createRedis();

        $this->handle->{$this->option['persistent'] ? 'pconnect' : 'connect'}(
            $this->option['host'],
            (int) ($this->option['port']),
            $this->option['timeout']
        );

        if ($this->option['password']) {
            $this->handle->auth($this->option['password']);
        }

        if ($this->option['select']) {
            $this->handle->select($this->option['select']);
        }
    }

    /**
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->handle;
    }

    /**
     * 获取缓存.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->handle->get($name);
    }

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param int    $expire
     */
    public function set($name, $data, ?int $expire = null)
    {
        if ($expire) {
            $this->handle->setex($name, $expire, $data);
        } else {
            $this->handle->set($name, $data);
        }
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     */
    public function delete($name)
    {
        $this->handle->delete($name);
    }

    /**
     * 关闭 redis.
     */
    public function close()
    {
        $this->handle->close();
        $this->handle = null;
    }

    /**
     * 返回 redis 对象
     *
     * @return \Redis
     */
    protected function createRedis()
    {
        return new Redis();
    }
}
