<?php declare(strict_types=1);
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

use Redis as Rediss;
use RuntimeException;

/**
 * redis 扩展缓存
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class Redis extends Connect implements IConnect
{
    /**
     * 配置
     *
     * @var array
     */
    protected $option = [
        'time_preset' => [],
        'prefix' => '_',
        'expire' => 86400,
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'persistent' => false,
        'serialize' => true
    ];

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        if (! extension_loaded('redis')) {
            throw new RuntimeException('Redis extension must be loaded before use.');
        }

        parent::__construct($option);

        $this->handle = $this->getRedis();

        $this->handle->{$this->option['persistent'] ? 'pconnect' : 'connect'}(
            $this->option['host'],
            $this->option['port'],
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
     * 获取缓存
     *
     * @param string $name
     * @param mixed $defaults
     * @param array $option
     * @return mixed
     */
    public function get($name, $defaults = false, array $option = [])
    {
        $option = $this->normalizeOptions($option);

        $data = $this->handle->get(
            $this->getCacheName($name, $option['prefix'])
        );

        if (is_null($data)) {
            return $defaults;
        }

        if ($option['serialize']) {
            $data = unserialize($data);
        }

        return $data;
    }

    /**
     * 设置缓存
     *
     * @param string $name
     * @param mixed $data
     * @param array $option
     * @return void
     */
    public function set($name, $data, array $option = [])
    {
        $option = $this->normalizeOptions($option);

        if ($option['serialize']) {
            $data = serialize($data);
        }

        $option['expire'] = $this->cacheTime($name, $option['expire']);

        if ((int)$option['expire']) {
            $this->handle->setex(
                $this->getCacheName($name, $option['prefix']),
                (int)$option['expire'],
                $data
            );
        } else {
            $this->handle->set(
                $this->getCacheName($name, $option['prefix']),
                $data
            );
        }
    }

    /**
     * 清除缓存
     *
     * @param string $name
     * @param array $option
     * @return void
     */
    public function delete($name, array $option = [])
    {
        $this->handle->delete(
            $this->getCacheName($name, $this->normalizeOptions($option)['prefix'])
        );
    }

    /**
     * 关闭 redis
     *
     * @return void
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
    protected function getRedis()
    {
        return new Rediss();
    }
}
