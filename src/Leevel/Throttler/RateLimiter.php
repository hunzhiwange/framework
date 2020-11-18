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

namespace Leevel\Throttler;

use InvalidArgumentException;
use Leevel\Cache\ICache;
use RuntimeException;

/**
 * 速率限制器.
 */
class RateLimiter
{
    /**
     * 缓存接口.
     */
    protected ICache $cache;

    /**
     * 缓存键值.
    */
    protected string $key;

    /**
     * 指定时间内允许的最大请求次数.
     */
    protected int $limit;

    /**
     * 指定时间长度.
     */
    protected int $time;

    /**
     * 当前请求次数.
     */
    protected int $currentCount = 0;

    /**
     * 构造函数.
     */
    public function __construct(ICache $cache, string $key, int $limit, int $time)
    {
        $this->cache = $cache;
        $this->key = $key;
        $this->setLimit($limit);
        $this->setTime($time);
    }

    /**
     * 验证并执行请求.
     */
    public function attempt(): bool
    {
        return $this->hit()->tooManyAttempt();
    }

    /**
     * 判断资源是否被耗尽.
     */
    public function tooManyAttempt(): bool
    {
        return $this->getRemainingReal() < 0;
    }

    /**
     * 执行请求.
     */
    public function hit(): self
    {
        $this->currentCount = $this->cache->increase($this->getKey(), 1, $this->time) ?: 0;

        return $this;
    }

    /**
     * 返回限流响应头.
     *
     * - X-RateLimit-Limit 指定时间内允许的最大请求次数
     * - X-RateLimit-Remaining 指定时间内剩余请求次数
     * - X-RateLimit-Reset 下次重置时间
     *
     * @see https://developer.github.com/v3/rate_limit/
     */
    public function getHeaders(): array
    {
        return [
            'X-RateLimit-Limit'     => $this->limit,
            'X-RateLimit-Remaining' => $this->getRemaining(),
            'X-RateLimit-Reset'     => $this->getReset(),
        ];
    }

    /**
     * 指定时间内剩余请求次数.
     */
    public function getRemaining(): int
    {
        return ($remainingReal = $this->getRemainingReal()) > 0 ? $remainingReal : 0;
    }

    /**
     * 指定时间内剩余请求次数.
     *
     * - 实际可能扣减为负数.
     */
    public function getRemainingReal(): int
    {
        return $this->limit - $this->currentCount;
    }

    /**
     * 下次重置时间.
     */
    public function getReset(): int
    {
        return ($this->cache->ttl($this->getKey()) ?: 0) + time();
    }

    /**
     * 获取请求次数.
     */
    public function getCount(): int
    {
        return $this->currentCount;
    }

    /**
     * 设置指定时间内允许的最大请求次数.
     *
     * @throws \InvalidArgumentException
     */
    public function setLimit(int $limit): self
    {
        if ($limit <= 0) {
            $e = 'Param `$limit` must be greater than 0.';

            throw new InvalidArgumentException($e);
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * 设置指定时间长度.
     *
     * @throws \InvalidArgumentException
     */
    public function setTime(int $time): self
    {
        if ($time <= 0) {
            $e = 'Param `$time` must be greater than 0.';

            throw new InvalidArgumentException($e);
        }

        $this->time = $time;

        return $this;
    }

    /**
     * 返回缓存组件.
     */
    public function getCache(): ICache
    {
        return $this->cache;
    }

    /**
     * 获取 key.
     *
     * @throws \RuntimeException
     */
    protected function getKey(): string
    {
        if (!$this->key) {
            throw new RuntimeException('Rate limiter key must be not empty.');
        }

        return $this->key;
    }
}
