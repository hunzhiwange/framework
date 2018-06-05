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

use RuntimeException;
use Memcache as Memcaches;

/**
 * memcache 扩展缓存
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class Memcache extends Connect implements IConnect
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
        'servers' => [],
        'host' => '127.0.0.1',
        'port' => 11211,
        'compressed' => false,
        'persistent' => false
    ];

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        if (! extension_loaded('memcache')) {
            throw new RuntimeException('Memcache extension must be loaded before use.');
        }

        parent::__construct($option);

        if (empty($this->option['servers'])) {
            $this->option['servers'][] = [
                'host' => $this->option['host'],
                'port' => $this->option['port']
            ];
        }

        // 连接缓存服务器
        $this->handle = $this->getMemcache();

        foreach ($this->option['servers'] as $server) {
            $result = $this->handle->addServer(
                $server['host'],
                $server['port'],
                $this->option['persistent']
            );

            if (! $result) {
                throw new RuntimeException(
                    sprintf(
                        'Unable to connect the memcached server [%s:%s] failed.',
                        $server['host'],
                        $server['port']
                    )
                );
            }
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
        $data = $this->handle->get(
            $this->getCacheName($name, $this->normalizeOptions($option)['prefix'])
        );

        return $data === false ? $defaults : $data;
    }

    /**
     * 设置缓存
     *
     * memcache 0 表示永不过期
     *
     * @param string $name
     * @param mixed $data
     * @param array $option
     * @return void
     */
    public function set($name, $data, array $option = [])
    {
        $option = $this->normalizeOptions($option);
        $option['expire'] = $this->cacheTime($name, $option['expire']);

        $this->handle->set(
            $this->getCacheName($name, $option['prefix']),
            $data,
            $option['compressed'] ? MEMCACHE_COMPRESSED : 0,
            (int)$option['expire'] <= 0 ? 0 : (int)$option['expire']
        );
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
     * 关闭 memcache
     *
     * @return void
     */
    public function close()
    {
        $this->handle->close();
        $this->handle = null;
    }

    /**
     * 返回 memcache 对象
     *
     * @return \Memcache
     */
    protected function getMemcache()
    {
        return new Memcaches();
    }
}
