<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Closure;
use InvalidArgumentException;

/**
 * 缓存抽象类.
 */
abstract class Cache implements ICache
{
    /**
     * 缓存服务句柄.
     */
    protected mixed $handle = null;

    /**
     * 配置.
     */
    protected array $option = [];

    /**
     * 缓存键值正则.
    */
    protected string $keyRegex = '/^[A-Za-z0-9\-\_:.]+$/';

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * {@inheritDoc}
     */
    public function put(array|string $keys, mixed $value = null, ?int $expire = null): void
    {
        if (!is_array($keys)) {
            $keys = [$keys => $value];
        }

        foreach ($keys as $key => $value) {
            $this->set($key, $value, $expire);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remember(string $name, Closure $dataGenerator, ?int $expire = null): mixed
    {
        if (false !== ($result = $this->get($name, false))) {
            return $result;
        }

        $data = $dataGenerator($name);
        $this->set($name, $data, $expire);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(): mixed
    {
        return $this->handle;
    }

    /**
     * {@inheritDoc}
     */
    public function setKeyRegex(string $keyRegex): void
    {
        $this->keyRegex = $keyRegex;
    }

    /**
     * 编码数据.
     *
     * @throws \InvalidArgumentException
     */
    protected function encodeData(mixed $data): mixed
    {
        if (false === $data) {
            $e = 'Data `false` not allowed to avoid cache penetration.';

            throw new InvalidArgumentException($e);
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * 解码数据.
     */
    protected function decodeData(mixed $data): mixed
    {
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * 获取缓存名字.
     *
     * @throws \InvalidArgumentException
     */
    protected function getCacheName(string $name): string
    {
        if (preg_match($this->keyRegex, $name) <= 0) {
            $e = sprintf('Cache key must be `%s`.', $this->keyRegex);

            throw new InvalidArgumentException($e);
        }

        return $name;
    }

    /**
     * 读取缓存时间配置.
     */
    protected function cacheTime(string $id, int $defaultTime): int
    {
        if (!$this->option['time_preset']) {
            return $defaultTime;
        }

        if (isset($this->option['time_preset'][$id])) {
            return (int) $this->option['time_preset'][$id];
        }

        foreach ($this->option['time_preset'] as $key => $value) {
            if (preg_match($this->prepareRegexForWildcard($key), $id, $res)) {
                return (int) $this->option['time_preset'][$key];
            }
        }

        return $defaultTime;
    }

    /**
     * 通配符正则.
     */
    protected function prepareRegexForWildcard(string $regex): string
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^'.str_replace('\*', '(\S*)', $regex).'$/';

        return $regex;
    }

    /**
     * 整理过期时间.
     */
    protected function normalizeExpire(string $name, ?int $expire = null): int
    {
        return $this->cacheTime($name, null !== $expire ? $expire : (int) $this->option['expire']);
    }
}
