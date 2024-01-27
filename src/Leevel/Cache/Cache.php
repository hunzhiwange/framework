<?php

declare(strict_types=1);

namespace Leevel\Cache;

use Leevel\Server\Pool\Connection;

/**
 * 缓存抽象类.
 */
abstract class Cache implements ICache
{
    use Connection;

    /**
     * 配置.
     */
    protected array $config = [];

    /**
     * 缓存键值正则.
     */
    protected string $keyRegex = '/^[A-Za-z0-9\-\_:.]+$/';

    /**
     * 构造函数.
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function put(array|string $keys, mixed $value = null, ?int $expire = null): void
    {
        if (!\is_array($keys)) {
            $keys = [$keys => $value];
        }

        foreach ($keys as $key => $value) {
            $this->set($key, $value, $expire);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remember(string $name, \Closure $dataGenerator, ?int $expire = null): mixed
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
    public function setKeyRegex(string $keyRegex): void
    {
        $this->keyRegex = $keyRegex;
    }

    /**
     * {@inheritDoc}
     */
    public function releaseConnect(): void
    {
        // 缓存驱动 \Leevel\Cache\ICache 需要实现 \Leevel\Server\Pool\IConnection
        // 归还连接池方法为 \Leevel\Server\Pool\IConnection::release
        $this->release();
    }

    /**
     * 编码数据.
     *
     * @throws \InvalidArgumentException
     * @throws \JsonException
     */
    protected function encodeData(mixed $data): mixed
    {
        if (false === $data) {
            throw new \InvalidArgumentException('Data `false` not allowed to avoid cache penetration.');
        }

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    /**
     * 解码数据.
     *
     * @throws \JsonException
     */
    protected function decodeData(string $data): mixed
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
            throw new \InvalidArgumentException(sprintf('Cache key must be `%s`.', $this->keyRegex));
        }

        return $name;
    }

    /**
     * 整理过期时间.
     */
    protected function normalizeExpire(?int $expire = null): int
    {
        return null !== $expire ? $expire : (int) $this->config['expire'];
    }
}
