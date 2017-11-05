<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\throttler\middleware;

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

use Closure;
use queryyetsimple\http\request;
use queryyetsimple\http\response;
use queryyetsimple\throttler\ithrottler;
use queryyetsimple\mvc\too_many_requests_http;

/**
 * throttler 中间件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.10
 * @version 1.0
 */
class throttler {
    
    /**
     * throttler
     *
     * @var \queryyetsimple\throttler\ithrottler
     */
    protected $objThrottler;
    
    /**
     * HTTP Response
     *
     * @var \queryyetsimple\http\response $objResponse
     */
    protected $objResponse;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\throttler\ithrottler $objThrottler            
     * @param \queryyetsimple\http\response $objResponse            
     * @return void
     */
    public function __construct(ithrottler $objThrottler, response $objResponse) {
        $this->objThrottler = $objThrottler;
        $this->objResponse = $objResponse;
    }
    
    /**
     * 请求
     *
     * @param \Closure $calNext            
     * @param \queryyetsimple\http\request $objRequest            
     * @param int $intLimit            
     * @param int $intLime            
     * @return mixed
     */
    public function handle(Closure $calNext, request $objRequest, $intLimit = 60, $intLime = 60) {
        $oRateLimiter = $this->objThrottler->create ( null, ( int ) $intLimit, ( int ) $intLime );
        
        if ($oRateLimiter->attempt ()) {
            $this->header ( $oRateLimiter );
            throw new too_many_requests_http ( 'Too many attempts.' );
        } else {
            $this->header ( $oRateLimiter );
        }
        
        return $calNext ( $objRequest );
    }
    
    /**
     * 发送 HEADER
     *
     * @param \queryyetsimple\throttler\rate_limiter $oRateLimiter            
     * @return void
     */
    protected function header($oRateLimiter) {
        $this->objResponse->headers ( $oRateLimiter->toArray () );
    }
}
