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

use Leevel\Cache\ICache;

/**
 * IRateLimiter 接口.
 */
interface IRateLimiter
{
    /**
     * 数据存储分隔符.
     *
     * @var string
     */
    const SEPARATE = "\t";

    /**
     * 验证请求
     */
    public function attempt(): bool;

    /**
     * 判断资源是否被耗尽.
     */
    public function tooManyAttempt(): bool;

    /**
     * 执行请求
     *
     * @return \Leevel\Throttler\IRateLimiter
     */
    public function hit(): self;

    /**
     * 下次重置时间.
     */
    public function endTime(): int;

    /**
     * 请求返回 HEADER.
     */
    public function header(): array;

    /**
     * 距离下一次请求等待时间.
     */
    public function retryAfter(): int;

    /**
     * 指定时间内剩余请求次数.
     */
    public function remaining(): int;

    /**
     * 指定时间长度.
     *
     * @return \Leevel\Throttler\IRateLimiter
     */
    public function limit(int $xRateLimitLimit = 60): self;

    /**
     * 指定时间内允许的最大请求次数.
     *
     * @return \Leevel\Throttler\IRateLimiter
     */
    public function time(int $xRateLimitTime = 60): self;

    /**
     * 返回缓存组件.
     */
    public function getCache(): ICache;
}
