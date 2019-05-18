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

/**
 * cache 快捷载入.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.11.20
 *
 * @version 1.0
 */
class Load
{
    /**
     * IOC Container.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * cache 仓储.
     *
     * @var \Leevel\Cache\ICache
     */
    protected ICache $cache;

    /**
     * 已经载入的缓存数据.
     *
     * @var array
     */
    protected array $cacheLoaded = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     * @param \Leevel\Cache\ICache  $cache
     */
    public function __construct(IContainer $container, ICache $cache)
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    /**
     * 切换缓存仓储.
     *
     * @param \Leevel\Cache\ICache $cache
     */
    public function switchCache(ICache $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * 返回缓存仓储.
     *
     * @return \Leevel\Cache\ICache
     */
    public function getCache(): ICache
    {
        return $this->cache;
    }

    /**
     * 载入缓存数据
     * 系统自动存储缓存到内存，可重复执行不会重复载入数据.
     *
     * @param array|string $names
     * @param array        $option
     * @param bool         $force
     *
     * @return array
     */
    public function data($names, array $option = [], bool $force = false): array
    {
        $names = is_array($names) ? $names : [
            $names,
        ];

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
     * @param array|string $names
     */
    public function refresh($names): void
    {
        $tmp = is_array($names) ? $names : [
            $names,
        ];

        foreach ($tmp as $name) {
            $this->delete($name);
        }
    }

    /**
     * 从载入缓存数据中获取
     * 不存在不用更新缓存，返回 false.
     *
     * @param array|string $names
     * @param mixed        $force
     *
     * @return mixed
     */
    public function dataLoaded($names, array $option = [], bool $force = false)
    {
        $result = [];

        $names = is_array($names) ? $names : [
            $names,
        ];

        foreach ($names as $name) {
            $result[$name] = array_key_exists($name, $this->cacheLoaded) ?
                $this->cacheLoaded[$name] :
                false;
        }

        return count($result) > 1 ? $result : reset($result);
    }

    /**
     * 删除缓存数据.
     *
     * @param string $name
     */
    protected function delete(string $name): void
    {
        $this->deletePersistence($name);
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
            $data = $this->getPersistence($name, false, $option);
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
     * @return mixed
     */
    protected function update(string $name, array $option = [])
    {
        $sourceName = $name;

        list($name, $params) = $this->parse($name);

        if (false !== strpos($name, '@')) {
            list($name, $method) = explode('@', $name);
        } else {
            $method = 'handle';
        }

        if (!is_object($cache = $this->container->make($name))) {
            throw new InvalidArgumentException(sprintf('Cache %s is not valid.', $name));
        }

        if (!is_callable([$cache, $method])) {
            throw new InvalidArgumentException(
                sprintf('Cache %s is not a callable.', $name.'@'.$method)
            );
        }

        $sourceData = $cache->{$method}(...$params);

        $this->setPersistence($sourceName, $sourceData, $option);

        return $sourceData;
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
    protected function getPersistence(string $name, $defaults = false, array $option = [])
    {
        return $this->cache->get($name, $defaults, $option);
    }

    /**
     * 设置缓存.
     *
     * @param string $name
     * @param mixed  $data
     * @param array  $option
     */
    protected function setPersistence(string $name, $data, array $option = []): void
    {
        $this->cache->set($name, $data, $option);
    }

    /**
     * 清除缓存.
     *
     * @param string $name
     */
    protected function deletePersistence(string $name): void
    {
        $this->cache->delete($name);
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

        $args = array_map(fn(string $item) => ctype_digit($item) ? (int) $item : $item, $args);

        return [$name, $args];
    }
}
