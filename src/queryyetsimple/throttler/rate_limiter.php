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

use Countable;
use RuntimeException;
use queryyetsimple\cache\interfaces\repository;
use queryyetsimple\support\interfaces\arrayable;
use queryyetsimple\throttler\interfaces\rate_limiter as interfaces_rate_limiter;

/**
 * rate_limiter 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
class rate_limiter implements interfaces_rate_limiter, Countable, arrayable {
    
    /**
     * 缓存接口
     *
     * @var \queryyetsimple\cache\interfaces\repository
     */
    protected $objCache;
    
    /**
     * 缓存键值
     *
     * @var string
     */
    protected $strKey;
    
    /**
     * 指定时间内允许的最大请求次数
     *
     * @var int
     */
    protected $intXRateLimitLimit = 60;
    
    /**
     * 指定时间长度
     *
     * @var int
     */
    protected $intXRateLimitTime = 60;
    
    /**
     * 距离下一次请求等待时间
     *
     * @var int
     */
    protected $intXRateLimitRetryAfter;
    
    /**
     * 指定时间内剩余请求次数
     *
     * @var int
     */
    protected $intXRateLimitRemaining;
    
    /**
     * 请求返回 HEADER
     *
     * @var array
     */
    protected $arrHeader;
    
    /**
     * 当前请求次数
     *
     * @var int
     */
    protected $intCount;
    
    /**
     * 下次重置时间
     *
     * @var int
     */
    protected $intEndTime;
    
    /**
     * 缓存数据
     *
     * @var array
     */
    protected $arrData;
    
    /**
     * 距离下一次请求等待时间
     * 实际，可能扣减为负数
     *
     * @var int
     */
    protected $intXRateLimitRetryAfterReal;
    
    /**
     * 指定时间内剩余请求次数
     * 实际，可能扣减为负数
     *
     * @var int
     */
    protected $intXRateLimitRemainingReal;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\cache\interfaces\repository $objCache            
     * @param string $strKey            
     * @param string $intXRateLimitLimit            
     * @param string $intXRateLimitTime            
     * @return void
     */
    public function __construct(repository $objCache, $strKey, $intXRateLimitLimit = 60, $intXRateLimitTime = 60) {
        $this->objCache = $objCache;
        $this->strKey = $strKey;
        $this->intXRateLimitLimit = $intXRateLimitLimit;
        $this->intXRateLimitTime = $intXRateLimitTime;
    }
    
    /**
     * 验证请求
     *
     * @return boolean
     */
    public function attempt() {
        if (! ($boo = $this->tooManyAttempt ())) {
            $this->hit ();
        }
        return $boo;
    }
    
    /**
     * 判断资源是否被耗尽
     *
     * @return bool
     */
    public function tooManyAttempt() {
        $booTooMany = false;
        
        // 剩余时间完毕，重新计算
        if ($this->retryAfterReal () < 0) {
            $this->clear ();
        } else {
            // 时间未完毕，但是剩余次数已经用光了，则拦截
            if ($this->remainingReal () < 0) {
                $booTooMany = true;
            }
        }
        
        return $booTooMany;
    }
    
    /**
     * 执行请求
     *
     * @return $this
     */
    public function hit() {
        $this->intCount = $this->count () + 1;
        $this->saveData ();
        return $this;
    }
    
    /**
     * 清理记录
     *
     * @return $this
     */
    public function clear() {
        $this->intEndTime = $this->getInitEndTime ();
        $this->intCount = $this->getInitCount ();
        return $this;
    }
    
    /**
     * 下次重置时间
     *
     * @return $this
     */
    public function endTime() {
        if (! is_null ( $this->intEndTime )) {
            return $this->intEndTime;
        }
        return $this->intEndTime = $this->getData ()[0];
    }
    
    /**
     * 请求返回 HEADER
     *
     * @return array
     */
    public function header() {
        if (! is_null ( $this->arrHeader )) {
            return $this->arrHeader;
        }
        
        return $this->arrHeader = [
                // 指定时间长度
                'X-RateLimit-Time' => $this->intXRateLimitTime,
                
                // 指定时间内允许的最大请求次数
                'X-RateLimit-Limit' => $this->intXRateLimitLimit,
                
                // 指定时间内剩余请求次数
                'X-RateLimit-Remaining' => $this->remaining (),
                
                // 距离下一次请求等待时间
                'X-RateLimit-RetryAfter' => $this->retryAfter (),
                
                // 下次重置时间
                'X-RateLimit-Reset' => $this->intEndTime 
        ];
    }
    
    /**
     * 距离下一次请求等待时间
     *
     * @return int
     */
    public function retryAfter() {
        if (! is_null ( $this->intXRateLimitRetryAfter )) {
            return $this->intXRateLimitRetryAfter;
        }
        
        return $this->intXRateLimitRetryAfter = $this->remainingReal () < 0 ? ($this->retryAfterReal () > 0 ? $this->retryAfterReal () : 0) : 0;
    }
    
    /**
     * 指定时间内剩余请求次数
     *
     * @return int
     */
    public function remaining() {
        if (! is_null ( $this->intXRateLimitRemaining )) {
            return $this->intXRateLimitRemaining;
        }
        return $this->intXRateLimitRemaining = $this->remainingReal () > 0 ? $this->remainingReal () : 0;
    }
    
    /**
     * 指定时间长度
     *
     * @param int $intXRateLimitLimit            
     * @return $this
     */
    public function limitLimit($intXRateLimitLimit = 60) {
        $this->intXRateLimitLimit = $intXRateLimitLimit;
        return $this;
    }
    
    /**
     * 指定时间内允许的最大请求次数
     *
     * @param int $intXRateLimitTime            
     * @return $this
     */
    public function limitTime($intXRateLimitTime = 60) {
        $this->intXRateLimitTime = $intXRateLimitTime;
        return $this;
    }
    
    /**
     * 返回缓存组件
     *
     * @return \queryyetsimple\cache\interfaces\repository
     */
    public function getCache() {
        return $this->objCache;
    }
    
    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray() {
        return $this->header ();
    }
    
    /**
     * 请求次数
     *
     * @return int
     */
    public function count() {
        if (! is_null ( $this->intCount )) {
            return $this->intCount;
        }
        return $this->intCount = $this->getData ()[1];
    }
    
    /**
     * 距离下一次请求等待时间
     * 实际，可能扣减为负数
     *
     * @return int
     */
    protected function retryAfterReal() {
        if (! is_null ( $this->intXRateLimitRetryAfterReal )) {
            return $this->intXRateLimitRetryAfterReal;
        }
        return $this->intXRateLimitRetryAfterReal = $this->endTime () - time ();
    }
    
    /**
     * 指定时间内剩余请求次数
     * 实际，可能扣减为负数
     *
     * @return int
     */
    protected function remainingReal() {
        if (! is_null ( $this->intXRateLimitRemainingReal )) {
            return $this->intXRateLimitRemainingReal;
        }
        return $this->intXRateLimitRemainingReal = $this->intXRateLimitLimit - $this->count ();
    }
    
    /**
     * 保存缓存数据
     *
     * @return void
     */
    protected function saveData() {
        $this->objCache->set ( $this->getKey (), $this->getImplodeData ( $this->endTime (), $this->count () ) );
    }
    
    /**
     * 读取缓存数据
     *
     * @return array
     */
    protected function getData() {
        if (! is_null ( $this->arrData )) {
            return $this->arrData;
        }
        
        if (($this->arrData = $this->objCache->get ( $this->getKey () ))) {
            $this->arrData = $this->getExplodeData ( $this->arrData );
        } else {
            $this->arrData = [ 
                    $this->getInitEndTime (),
                    $this->getInitCount () 
            ];
        }
        return $this->arrData;
    }
    
    /**
     * 组装缓存数据
     *
     * @param int $intEndTime            
     * @param int $intCount            
     * @return string
     */
    protected function getImplodeData($intEndTime, $intCount) {
        return $intEndTime . static::SEPARATE . $intCount;
    }
    
    /**
     * 分隔缓存数据
     *
     * @param array $arrData            
     * @return array
     */
    protected function getExplodeData($arrData) {
        return explode ( static::SEPARATE, $arrData );
    }
    
    /**
     * 获取 key
     *
     * @return null|string
     */
    protected function getKey() {
        if (! $this->strKey)
            throw new RuntimeException ( 'Key is not set' );
        return $this->strKey;
    }
    
    /**
     * 初始化下一次重置时间
     *
     * @return int
     */
    protected function getInitEndTime() {
        return time () + $this->intXRateLimitLimit;
    }
    
    /**
     * 初始化点击
     *
     * @return int
     */
    protected function getInitCount() {
        return 0;
    }
}
