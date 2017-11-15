<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\throttler;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use RuntimeException;
use queryyetsimple\http\request;
use queryyetsimple\cache\icache;

/**
 * throttler 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
class throttler implements ithrottler
{

    /**
     * 节流器实例
     *
     * @var \queryyetsimple\throttler\rate_limiter[]
     */
    protected $arrRateLimiter = [ ];

    /**
     * cache
     *
     * @var \queryyetsimple\cache\icache
     */
    protected $objCache;

    /**
     * http request
     *
     * @var \queryyetsimple\http\request
     */
    protected $objRequest;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\cache\icache $objCache
     * @return void
     */
    public function __construct(icache $objCache)
    {
        $this->objCache = $objCache;
    }

    /**
     * 创建一个节流器
     *
     * @param string|null $strKey
     * @param integer $intXRateLimitLimit
     * @param integer $intXRateLimitTime
     * @return \queryyetsimple\throttler\rate_limiter
     */
    public function create($strKey = null, $intXRateLimitLimit = 20, $intXRateLimitTime = 20)
    {
        $strKey = $this->getRequestKey($strKey);
        if (isset($this->arrRateLimiter [$strKey])) {
            return $this->arrRateLimiter [$strKey]->limitLimit($intXRateLimitLimit)->limitTime($intXRateLimitTime);
        }

        return $this->arrRateLimiter [$strKey] = new rate_limiter($this->objCache, $strKey, $intXRateLimitLimit, $intXRateLimitTime);
    }

    /**
     * 设置 http request
     *
     * @param \queryyetsimple\http\request $objRequest
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
        return $strKey ?  : sha1($this->objRequest->ip() . '@' . $this->objRequest->routerNode());
    }

    /**
     * 拦截匿名注册控制器方法
     *
     * @param 方法名 $sMethod
     * @param 参数 $arrArgs
     * @return mixed
     */
    public function __call($sMethod, $arrArgs)
    {
        return call_user_func_array([
                $this,
                'create'
        ], $arrArgs)->$sMethod();
    }
}
