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

use InvalidArgumentException;
use Leevel\Di\IContainer;
use ReflectionClass;

/**
 * cache 快捷载入.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.11.20
 *
 * @version 1.0
 */
class Load implements ILoad
{
    /**
     * IOC Container.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 已经载入的缓存数据.
     *
     * @var array
     */
    protected $cacheLoaded = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 载入缓存数据
     * 系统自动存储缓存到内存，可重复执行不会重复载入数据.
     *
     * @param array $names
     * @param array $option
     * @param bool  $force
     *
     * @return array
     */
    public function data(array $names, array $option = [], bool $force = false): array
    {
        foreach ($names as $name) {
            if (!isset($this->cacheLoaded[$name]) || $force) {
                $this->cacheLoaded[$name] = $this->cache($name, $option, $force);
            }
        }

        $result = [];

        foreach ($names as $name) {
            $result[$name] = $this->cacheLoaded[$name];
        }

        return count($result) > 1 ? $result : reset($result);
    }

    /**
     * 刷新缓存数据.
     *
     * @param array $names
     */
    public function refresh(array $names): void
    {
        foreach ($names as $name) {
            list($cache, $key) = $this->normalize($name);
            $this->deletePersistence($cache->cache(), $key);
        }
    }

    /**
     * 读取缓存数据.
     *
     * @param string $name
     * @param array  $option
     * @param bool   $force
     *
     * @return mixed
     */
    protected function cache(string $name, array $option = [], bool $force = false)
    {
        if (false === $force) {
            list($cache, $key) = $this->normalize($name);
            $data = $this->getPersistence($cache->cache(), $key, false, $option);
        } else {
            $data = false;
        }

        if (false === $data) {
            $data = $this->update($name, $option);
        }

        return $data;
    }

    /**
     * 更新缓存数据.
     *
     * @param string $name
     * @param array  $option
     *
     * @return array
     */
    protected function update(string $name, array $option = []): array
    {
        list($cache, $key, $params) = $this->normalize($name);
        $data = $cache->handle($params);
        $this->setPersistence($cache->cache(), $key, $data, $option);

        return $data;
    }

    /**
     * 缓存属性整理.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function normalize(string $name): array
    {
        list($name, $params) = $this->parse($name);

        $rc = new ReflectionClass($name);

        if (!in_array(IBlock::class, $rc->getInterfaceNames(), true)) {
            $e = sprintf('Cache `%s` must implements `%s`.', $name, IBlock::class);

            throw new InvalidArgumentException($e);
        }

        $cache = $this->container->make($name);

        return [$cache, $name::key($params), $params];
    }

    /**
     * 获取缓存.
     *
     * @param \Leevel\Cache\ICache $cache
     * @param string               $name
     * @param mixed                $defaults
     * @param array                $option
     *
     * @return mixed
     */
    protected function getPersistence(ICache $cache, string $name, $defaults = false, array $option = [])
    {
        return $cache->get($name, $defaults, $option);
    }

    /**
     * 设置缓存.
     *
     * @param \Leevel\Cache\ICache $cache
     * @param string               $name
     * @param array                $data
     * @param array                $option
     */
    protected function setPersistence(ICache $cache, string $name, array $data, array $option = []): void
    {
        $cache->set($name, $data, $option);
    }

    /**
     * 清除缓存.
     *
     * @param \Leevel\Cache\ICache $cache
     * @param string               $name
     */
    protected function deletePersistence(ICache $cache, string $name): void
    {
        $cache->delete($name);
    }

    /**
     * 解析缓存.
     *
     * @param string $name
     *
     * @return array
     */
    protected function parse(string $name): array
    {
        list($name, $args) = array_pad(explode(':', $name, 2), 2, []);

        if (is_string($args)) {
            $args = explode(',', $args);
        }

        $args = array_map(function (string $item) {
            return ctype_digit($item) ? (int) $item : $item;
        }, $args);

        return [$name, $args];
    }
}
