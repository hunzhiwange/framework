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

use Leevel\Http\Request;

/**
 * 节流器接口.
 */
interface IThrottler
{
    /**
     * 创建一个速率限制器.
     *
     * @return \Leevel\Throttler\RateLimiter
     */
    public function create(?string $key = null, int $xRateLimitLimit = 20, int $xRateLimitTime = 20): RateLimiter;

    /**
     * 设置 HTTP 请求.
     *
     * @return \Leevel\Throttler\IThrottler
     */
    public function setRequest(Request $request): self;

    /**
     * 获取请求 key.
     */
    public function getRequestKey(?string $key = null): string;
}
