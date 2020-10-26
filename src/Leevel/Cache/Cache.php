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

use Closure;
use InvalidArgumentException;

/**
 * 缓存抽象类.
 */
abstract class Cache implements ICache
{
    /**
     * 缓存服务句柄.
     *
     * @var mixed
     */
    protected mixed $handle;

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [];

    /**
     * 缓存键值正则.
     *
     * @var string
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
     * 批量设置缓存.
     *
     * @param array|string $keys
     * @param mixed        $value
     */
    public function put($keys, mixed $value = null, ?int $expire = null): void
    {
        if (!is_array($keys)) {
            $keys = [$keys => $value];
        }

        foreach ($keys as $key => $value) {
            $this->set($key, $value, $expire);
        }
    }

    /**
     * 缓存存在读取否则重新设置.
     *
     * @return mixed
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
     * 返回缓存句柄.
     *
     * @return mixed
     */
    public function handle(): mixed
    {
        return $this->handle;
    }

    /**
     * 缓存键值正则.
     */
    public function setKeyRegex(string $keyRegex): void
    {
        $this->keyRegex = $keyRegex;
    }

    /**
     * 编码数据.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
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
     *
     * @param mixed $data
     *
     * @return mixed
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
