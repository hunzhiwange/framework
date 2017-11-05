<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\log\middleware;

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
use queryyetsimple\log\log as manager;

/**
 * log 中间件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.14
 * @version 1.0
 */
class log {
    
    /**
     * log 管理
     *
     * @var \queryyetsimple\log\log
     */
    protected $objManager;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\throttler\ithrottler $objManager            
     * @return void
     */
    public function __construct(manager $objManager) {
        $this->objManager = $objManager;
    }
    
    /**
     * 响应
     *
     * @param \queryyetsimple\http\request $objRequest            
     * @param \queryyetsimple\http\response $mixResponse            
     * @return mixed
     */
    public function terminate(Closure $calNext, request $objRequest, response $objResponse) {
        $this->saveLog ();
        return $calNext ( $objRequest, $objResponse );
    }
    
    /**
     * 保存日志
     *
     * @return void
     */
    protected function saveLog() {
        if ($this->objManager->container ()['option'] ['log\enabled']) {
            $this->objManager->save ();
        }
    }
}
