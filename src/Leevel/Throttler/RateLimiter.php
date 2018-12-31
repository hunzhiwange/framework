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

namespace Leevel\Throttler;

use Countable;
use Leevel\Cache\ICache;
use Leevel\Support\IArray;
use RuntimeException;

/**
 * RateLimiter 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.07
 *
 * @version 1.0
 */
class RateLimiter implements IRateLimiter, IArray, Countable
{
    /**
     * 缓存接口.
     *
     * @var \Leevel\Cache\ICache
     */
    protected $cache;

    /**
     * 缓存键值
     *
     * @var string
     */
    protected $key;

    /**
     * 指定时间内允许的最大请求次数.
     *
     * @var int
     */
    protected $limit = 60;

    /**
     * 指定时间长度.
     *
     * @var int
     */
    protected $time = 60;

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\ICache $cache
     * @param string               $key
     * @param string               $limit
     * @param string               $time
     */
    public function __construct(ICache $cache, string $key, int $limit = 60, int $time = 60)
    {
        $this->cache = $cache;
        $this->key = $key;
        $this->limit = $limit;
        $this->time = $time;
    }

    /**
     * 验证请求
     *
     * @return bool
     */
    public function attempt(): bool
    {
        if (!($result = $this->tooManyAttempt())) {
            $this->hit();
        }

        return $result;
    }

    /**
     * 判断资源是否被耗尽.
     *
     * @return bool
     */
    public function tooManyAttempt(): bool
    {
        return $this->retryAfterReal() && $this->remainingReal() < 0;
    }

    /**
     * 执行请求
     *
     * @return $this
     */
    public function hit(): IRateLimiter
    {
        $this->saveData($this->count() + 1);

        return $this;
    }

    /**
     * 请求返回 HEADER.
     *
     * @return array
     */
    public function header(): array
    {
        return [
            'X-RateLimit-Time'       => $this->time, // 指定时间长度
            'X-RateLimit-Limit'      => $this->limit, // 指定时间内允许的最大请求次数
            'X-RateLimit-Remaining'  => $this->remaining(), // 指定时间内剩余请求次数
            'X-RateLimit-RetryAfter' => $this->retryAfter(), // 距离下一次请求等待时间
            'X-RateLimit-Reset'      => $this->endTime(), // 下次重置时间
        ];
    }

    /**
     * 距离下一次请求等待时间.
     *
     * @return int
     */
    public function retryAfter(): int
    {
        return $this->remainingReal() ? 0 : ($this->retryAfterReal() ?: 0);
    }

    /**
     * 指定时间内剩余请求次数.
     *
     * @return int
     */
    public function remaining(): int
    {
        return $this->remainingReal() ?: 0;
    }

    /**
     * 指定时间长度.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit = 60): IRateLimiter
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * 指定时间内允许的最大请求次数.
     *
     * @param int $time
     *
     * @return $this
     */
    public function time(int $time = 60): IRateLimiter
    {
        $this->time = $time;

        return $this;
    }

    /**
     * 返回缓存组件.
     *
     * @return \Leevel\Cache\ICache
     */
    public function getCache(): ICache
    {
        return $this->cache;
    }

    /**
     * 下次重置时间.
     *
     * @return int
     */
    public function endTime(): int
    {
        return $this->getData()[0];
    }

    /**
     * 请求次数.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->getData()[1];
    }

    /**
     * 对象转数组.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->header();
    }

    /**
     * 距离下一次请求等待时间
     * 实际，可能扣减为负数.
     *
     * @return int
     */
    protected function retryAfterReal(): int
    {
        return $this->endTime() - time();
    }

    /**
     * 指定时间内剩余请求次数
     * 实际，可能扣减为负数.
     *
     * @return int
     */
    protected function remainingReal(): int
    {
        return $this->limit - $this->count();
    }

    /**
     * 保存缓存数据.
     *
     * @param int $count
     */
    protected function saveData(int $count): void
    {
        $this->cache->set(
            $this->getKey(),
            $this->getImplodeData($this->endTime(), $count)
        );
    }

    /**
     * 读取缓存数据.
     *
     * @return array
     */
    protected function getData(): array
    {
        if (($data = $this->cache->get($this->getKey()))) {
            $data = $this->getExplodeData($data);
        } else {
            $data = [
                $this->getInitEndTime(),
                $this->getInitCount(),
            ];
        }

        return $data;
    }

    /**
     * 组装缓存数据.
     *
     * @param int $endTime
     * @param int $count
     *
     * @return string
     */
    protected function getImplodeData(int $endTime, int $count): string
    {
        return $endTime.static::SEPARATE.$count;
    }

    /**
     * 分隔缓存数据.
     *
     * @param array $data
     *
     * @return array
     */
    protected function getExplodeData(string $data): array
    {
        return array_map(function ($v) {
            return (int) ($v);
        }, explode(static::SEPARATE, $data));
    }

    /**
     * 获取 key.
     *
     * @return string
     */
    protected function getKey(): string
    {
        if (!$this->key) {
            throw new RuntimeException('Key is not set.');
        }

        return $this->key;
    }

    /**
     * 初始化下一次重置时间.
     *
     * @return int
     */
    protected function getInitEndTime(): int
    {
        return time() + $this->limit;
    }

    /**
     * 初始化点击.
     *
     * @return int
     */
    protected function getInitCount(): int
    {
        return 0;
    }
}
