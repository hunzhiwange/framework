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

use Leevel\Http\IRequest;

/**
 * IThrottler 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.07
 *
 * @version 1.0
 */
interface IThrottler
{
    /**
     * 创建一个节流器.
     *
     * @param null|string $key
     * @param int         $xRateLimitLimit
     * @param int         $xRateLimitTime
     *
     * @return \Leevel\Throttler\IRateLimiter
     */
    public function create(?string $key = null, int $xRateLimitLimit = 20, int $xRateLimitTime = 20): IRateLimiter;

    /**
     * 设置 http request.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return $this
     */
    public function setRequest(IRequest $request): self;

    /**
     * 获取请求 key.
     *
     * @param null|string $key
     *
     * @return string
     */
    public function getRequestKey(?string $key = null): string;
}
