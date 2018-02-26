<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Throttler;

use RuntimeException;
use Queryyetsimple\{
    Http\Request,
    Cache\ICache
};

/**
 * throttler 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
class Throttler implements IThrottler
{

    /**
     * 节流器实例
     *
     * @var \Queryyetsimple\Throttler\RateLimiter[]
     */
    protected $arrRateLimiter = [];

    /**
     * cache
     *
     * @var \Queryyetsimple\Cache\ICache
     */
    protected $objCache;

    /**
     * http request
     *
     * @var \Queryyetsimple\Http\Request
     */
    protected $objRequest;

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Cache\ICache $objCache
     * @return void
     */
    public function __construct(ICache $objCache)
    {
        $this->objCache = $objCache;
    }

    /**
     * 创建一个节流器
     *
     * @param string|null $strKey
     * @param integer $intXRateLimitLimit
     * @param integer $intXRateLimitTime
     * @return \Queryyetsimple\Throttler\RateLimiter
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
     * 设置 http request
     *
     * @param \Queryyetsimple\Http\Request $objRequest
     * @return $this
     */
    public function setRequest(request $objRequest)
    {
        $this->objRequest = $objRequest;
        return $this;
    }

    /**
     * 获取请求 key
     *
     * @param null|string $strKey
     * @return string
     */
    public function getRequestKey($strKey = null)
    {
        if (! $strKey && ! $this->objRequest) {
            throw new RuntimeException('Request is not set');
        }
        return $strKey ?  : sha1($this->objRequest->getClientIp() . '@' . $this->objRequest->getNode());
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        return $this->{'create'}(...$arrArgs)->$method();
    }
}
