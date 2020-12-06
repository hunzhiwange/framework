<?php

declare(strict_types=1);

namespace Leevel\Cache;

use InvalidArgumentException;
use Leevel\Di\IContainer;
use ReflectionClass;

/**
 * Cache 快捷载入.
 */
class Load implements ILoad
{
    /**
     * 已载入的缓存数据.
     */
    protected array $cacheLoaded = [];

    /**
     * 构造函数.
     */
    public function __construct(protected IContainer $container)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function data(array $names, ?int $expire = null, bool $force = false): mixed
    {
        foreach ($names as $name) {
            if (!isset($this->cacheLoaded[$name]) || $force) {
                $this->cacheLoaded[$name] = $this->cache($name, $expire, $force);
            }
        }

        $result = [];
        foreach ($names as $name) {
            $result[$name] = $this->cacheLoaded[$name];
        }

        return count($result) > 1 ? $result : reset($result);
    }

    /**
     * {@inheritDoc}
     */
    public function refresh(array $names): void
    {
        foreach ($names as $name) {
            /** @var \Leevel\Cache\IBlock $cache */
            list($cache, $key) = $this->normalize($name);
            $this->deletePersistence($cache->cache(), $key);
            if (isset($this->cacheLoaded[$name])) {
                unset($this->cacheLoaded[$name]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clearCacheLoaded(?array $names = null): void
    {
        if (null === $names) {
            $this->cacheLoaded = [];

            return;
        }

        foreach ($names as $name) {
            if (isset($this->cacheLoaded[$name])) {
                unset($this->cacheLoaded[$name]);
            }
        }
    }

    /**
     * 读取缓存数据.
     */
    protected function cache(string $name, ?int $expire = null, bool $force = false): mixed
    {
        if (false === $force) {
            /** @var \Leevel\Cache\IBlock $cache */
            list($cache, $key) = $this->normalize($name);
            $data = $this->getPersistence($cache->cache(), $key, false);
        } else {
            $data = false;
        }

        if (false === $data) {
            $data = $this->update($name, $expire);
        }

        return $data;
    }

    /**
     * 更新缓存数据.
     */
    protected function update(string $name, ?int $expire = null)
    {
        /** @var \Leevel\Cache\IBlock $cache */
        list($cache, $key, $params) = $this->normalize($name);
        $data = $cache->handle($params);
        $this->setPersistence($cache->cache(), $key, $data, $expire);

        return $data;
    }

    /**
     * 缓存属性整理.
     *
     * @throws \InvalidArgumentException
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
     */
    protected function getPersistence(ICache $cache, string $name, mixed $defaults = false): mixed
    {
        return $cache->get($name, $defaults);
    }

    /**
     * 设置缓存.
     */
    protected function setPersistence(ICache $cache, string $name, $data, ?int $expire = null): void
    {
        $cache->set($name, $data, $expire);
    }

    /**
     * 清除缓存.
     */
    protected function deletePersistence(ICache $cache, string $name): void
    {
        $cache->delete($name);
    }

    /**
     * 解析缓存.
     */
    protected function parse(string $name): array
    {
        list($name, $params) = array_pad(explode(':', $name, 2), 2, []);
        if (is_string($params)) {
            $params = explode(',', $params);
        }

        $params = array_map(fn (string $item) => ctype_digit($item) ? (int) $item : (is_numeric($item) ? (float) $item : $item), $params);

        return [$name, $params];
    }
}
