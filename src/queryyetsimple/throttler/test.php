<?php
class test{
    /**
     * 限制请求频率
     *
     * @param string $strKey            
     * @param string $strName            
     * @param boolean $booException            
     * @param int $intXRateLimitLimit            
     * @param int $intXRateLimitTime            
     * @param array $arrHandle            
     * @return array|void
     */
    public static function limitThrottler($strKey = null, $strName = null, $booException = true, $intXRateLimitLimit = 60, $intXRateLimitTime = 60, array $arrHandle = []) {
        // 判断处理器是否存在
        if (empty ( $arrHandle ) || count ( $arrHandle ) < 2 || ! is_callable ( $arrHandle [0] ) || ! is_callable ( $arrHandle [1] )) {
            $arrHandle = [ 
                    function ($strKey) {
                        return cookie::gets ( $strKey );
                    },
                    function ($strKey, $strValue) {
                        cookie::sets ( $strKey, $strValue );
                    } 
            ];
        }
        
        // 验证请求频率
        $sRequestKey = $strKey ?  : (md5 ( request::getIps () . md5 ( $strName ?  : $_SERVER ['PHP_SELF'] . '?' . $_SERVER ['QUERY_STRING'] ) ));
        $sRequestKey = 'last_http_request_' . $sRequestKey;
        
        if (($arrLastInfo = call_user_func_array ( $arrHandle [0], [ 
                $sRequestKey 
        ] ))) {
            list ( $intEndTime, $intCount ) = explode ( "\t", $arrLastInfo );
            $intCount ++;
        } else {
            $intEndTime = time () + $intXRateLimitLimit - 1;
            $intCount = 1;
        }
        
        $intRetryAfter = $intEndTime - time ();
        $intXRateLimitRemaining = $intXRateLimitLimit - $intCount;
        
        $arrHeader = [ 
                //X-RateLimit-Reset: 1350085394
                'X-RateLimit-Time' => $intXRateLimitTime, // 指定时间长度
                'X-RateLimit-Limit' => $intXRateLimitLimit, // 指定时间内允许的最大请求次数
                'X-RateLimit-Remaining' => $intXRateLimitRemaining >= 0 ? $intXRateLimitRemaining : 0, // 指定时间内剩余请求次数
                'Retry-After' => $intXRateLimitRemaining < 0 ? ($intRetryAfter >= 0 ? $intRetryAfter : 0) : 0 
        ]; // 距离下一次请求等待时间
        
        $booLimit = false;
        
        // 剩余时间完毕，重新计算
        if ($intRetryAfter < 0) {
            $intEndTime = time () + $intXRateLimitLimit - 1;
            $intCount = 1;
        } else {
            // 时间未完毕，但是剩余次数已经用光了，则拦截
            if ($intXRateLimitRemaining < 0) {
                $booLimit = true;
            }
        }
        
        if ($booLimit === false) {
            call_user_func_array ( $arrHandle [1], [ 
                    $sRequestKey,
                    $intEndTime . "\t" . $intCount 
            ] );
        }
        
        if ($booLimit === true && $booException === true) {
            throw new RuntimeException ( 'Too Many Attempts.<br/>' . json_encode ( $arrHeader ) );
        } else {
            return [ 
                    'status' => $booLimit === false ? 'success' : 'fail',
                    'header' => $arrHeader 
            ];
        }
    }
}