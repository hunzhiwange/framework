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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
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
    protected $xRateLimitLimit = 60;

    /**
     * 指定时间长度.
     *
     * @var int
     */
    protected $xRateLimitTime = 60;

    /**
     * 距离下一次请求等待时间.
     *
     * @var int
     */
    protected $xRateLimitRetryAfter;

    /**
     * 指定时间内剩余请求次数.
     *
     * @var int
     */
    protected $xRateLimitRemaining;

    /**
     * 请求返回 HEADER.
     *
     * @var array
     */
    protected $headers;

    /**
     * 当前请求次数.
     *
     * @var int
     */
    protected $count;

    /**
     * 下次重置时间.
     *
     * @var int
     */
    protected $endTime;

    /**
     * 缓存数据.
     *
     * @var array
     */
    protected $datas;

    /**
     * 距离下一次请求等待时间
     * 实际，可能扣减为负数.
     *
     * @var int
     */
    protected $xRateLimitRetryAfterReal;

    /**
     * 指定时间内剩余请求次数
     * 实际，可能扣减为负数.
     *
     * @var int
     */
    protected $xRateLimitRemainingReal;

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\ICache $cache
     * @param string               $key
     * @param string               $xRateLimitLimit
     * @param string               $xRateLimitTime
     */
    public function __construct(ICache $cache, $key, $xRateLimitLimit = 60, $xRateLimitTime = 60)
    {
        $this->cache = $cache;
        $this->key = $key;
        $this->xRateLimitLimit = $xRateLimitLimit;
        $this->xRateLimitTime = $xRateLimitTime;
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
        $tooMany = false;

        // 剩余时间完毕，重新计算
        if ($this->retryAfterReal() < 0) {
            $this->clear();
        } else {
            // 时间未完毕，但是剩余次数已经用光了，则拦截
            if ($this->remainingReal() < 0) {
                $tooMany = true;
            }
        }

        return $tooMany;
    }

    /**
     * 执行请求
     *
     * @return $this
     */
    public function hit(): self
    {
        $this->count = $this->count() + 1;
        $this->saveData();

        return $this;
    }

    /**
     * 清理记录.
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->endTime = $this->getInitEndTime();
        $this->count = $this->getInitCount();

        return $this;
    }

    /**
     * 下次重置时间.
     *
     * @return int
     */
    public function endTime(): int
    {
        if (null !== $this->endTime) {
            return $this->endTime;
        }

        return $this->endTime = $this->getData()[0];
    }

    /**
     * 请求返回 HEADER.
     *
     * @return array
     */
    public function header(): array
    {
        if (null !== $this->headers) {
            return $this->headers;
        }

        return $this->headers = [
            // 指定时间长度
            'X-RateLimit-Time' => $this->xRateLimitTime,

            // 指定时间内允许的最大请求次数
            'X-RateLimit-Limit' => $this->xRateLimitLimit,

            // 指定时间内剩余请求次数
            'X-RateLimit-Remaining' => $this->remaining(),

            // 距离下一次请求等待时间
            'X-RateLimit-RetryAfter' => $this->retryAfter(),

            // 下次重置时间
            'X-RateLimit-Reset' => $this->endTime,
        ];
    }

    /**
     * 距离下一次请求等待时间.
     *
     * @return int
     */
    public function retryAfter(): int
    {
        if (null !== $this->xRateLimitRetryAfter) {
            return $this->xRateLimitRetryAfter;
        }

        return $this->xRateLimitRetryAfter = $this->remainingReal() < 0 ?
            ($this->retryAfterReal() > 0 ? $this->retryAfterReal() : 0) :
            0;
    }

    /**
     * 指定时间内剩余请求次数.
     *
     * @return int
     */
    public function remaining(): int
    {
        if (null !== $this->xRateLimitRemaining) {
            return $this->xRateLimitRemaining;
        }

        return $this->xRateLimitRemaining = $this->remainingReal() > 0 ?
            $this->remainingReal() :
            0;
    }

    /**
     * 指定时间长度.
     *
     * @param int $xRateLimitLimit
     *
     * @return $this
     */
    public function limitLimit($xRateLimitLimit = 60): self
    {
        $this->xRateLimitLimit = $xRateLimitLimit;

        return $this;
    }

    /**
     * 指定时间内允许的最大请求次数.
     *
     * @param int $xRateLimitTime
     *
     * @return $this
     */
    public function limitTime($xRateLimitTime = 60): self
    {
        $this->xRateLimitTime = $xRateLimitTime;

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
     * 对象转数组.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->header();
    }

    /**
     * 请求次数.
     *
     * @return int
     */
    public function count(): int
    {
        if (null !== $this->count) {
            return $this->count;
        }

        return $this->count = $this->getData()[1];
    }

    /**
     * 距离下一次请求等待时间
     * 实际，可能扣减为负数.
     *
     * @return int
     */
    protected function retryAfterReal(): int
    {
        if (null !== $this->xRateLimitRetryAfterReal) {
            return $this->xRateLimitRetryAfterReal;
        }

        return $this->xRateLimitRetryAfterReal = $this->endTime() - time();
    }

    /**
     * 指定时间内剩余请求次数
     * 实际，可能扣减为负数.
     *
     * @return int
     */
    protected function remainingReal(): int
    {
        if (null !== $this->xRateLimitRemainingReal) {
            return $this->xRateLimitRemainingReal;
        }

        return $this->xRateLimitRemainingReal = $this->xRateLimitLimit - $this->count();
    }

    /**
     * 保存缓存数据.
     */
    protected function saveData(): void
    {
        $this->cache->set(
            $this->getKey(),
            $this->getImplodeData($this->endTime(), $this->count())
        );
    }

    /**
     * 读取缓存数据.
     *
     * @return array
     */
    protected function getData(): array
    {
        if (null !== $this->datas) {
            return $this->datas;
        }

        if (($this->datas = $this->cache->get($this->getKey()))) {
            $this->datas = $this->getExplodeData($this->datas);
        } else {
            $this->datas = [
                $this->getInitEndTime(),
                $this->getInitCount(),
            ];
        }

        return $this->datas;
    }

    /**
     * 组装缓存数据.
     *
     * @param int $endTime
     * @param int $count
     *
     * @return string
     */
    protected function getImplodeData($endTime, $count): string
    {
        return $endTime.static::SEPARATE.$count;
    }

    /**
     * 分隔缓存数据.
     *
     * @param array $datas
     *
     * @return array
     */
    protected function getExplodeData($datas): array
    {
        return explode(static::SEPARATE, $datas);
    }

    /**
     * 获取 key.
     *
     * @return null|string
     */
    protected function getKey()
    {
        if (!$this->key) {
            throw new RuntimeException('Key is not set');
        }

        return $this->key;
    }

    /**
     * 初始化下一次重置时间.
     *
     * @return int
     */
    protected function getInitEndTime()
    {
        return time() + $this->xRateLimitLimit;
    }

    /**
     * 初始化点击.
     *
     * @return int
     */
    protected function getInitCount()
    {
        return 0;
    }
}
