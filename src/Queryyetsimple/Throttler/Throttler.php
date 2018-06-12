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

use Leevel\Cache\ICache;
use Leevel\Http\Request;
use RuntimeException;

/**
 * throttler 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.07
 *
 * @version 1.0
 */
class Throttler implements IThrottler
{
    /**
     * 节流器实例.
     *
     * @var \Leevel\Throttler\RateLimiter[]
     */
    protected $arrRateLimiter = [];

    /**
     * cache.
     *
     * @var \Leevel\Cache\ICache
     */
    protected $objCache;

    /**
     * http request.
     *
     * @var \Leevel\Http\Request
     */
    protected $objRequest;

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\ICache $objCache
     */
    public function __construct(ICache $objCache)
    {
        $this->objCache = $objCache;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        return $this->{'create'}(...$arrArgs)->{$method}();
    }

    /**
     * 创建一个节流器.
     *
     * @param null|string $strKey
     * @param int         $intXRateLimitLimit
     * @param int         $intXRateLimitTime
     *
     * @return \Leevel\Throttler\RateLimiter
     */
    public function create($strKey = null, $intXRateLimitLimit = 20, $intXRateLimitTime = 20)
    {
        $strKey = $this->getRequestKey($strKey);
        if (isset($this->arrRateLimiter[$strKey])) {
            return $this->arrRateLimiter[$strKey]->limitLimit($intXRateLimitLimit)->limitTime($intXRateLimitTime);
        }

        return $this->arrRateLimiter[$strKey] = new RateLimiter($this->objCache, $strKey, $intXRateLimitLimit, $intXRateLimitTime);
    }

    /**
     * 设置 http request.
     *
     * @param \Leevel\Http\Request $objRequest
     *
     * @return $this
     */
    public function setRequest(request $objRequest)
    {
        $this->objRequest = $objRequest;

        return $this;
    }

    /**
     * 获取请求 key.
     *
     * @param null|string $strKey
     *
     * @return string
     */
    public function getRequestKey($strKey = null)
    {
        if (!$strKey && !$this->objRequest) {
            throw new RuntimeException('Request is not set');
        }

        return $strKey ?: sha1($this->objRequest->getClientIp().'@'.$this->objRequest->getNode());
    }
}
