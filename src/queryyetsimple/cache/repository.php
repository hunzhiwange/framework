<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\cache;

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

use Exception;
use queryyetsimple\support\infinity;

/**
 * 缓存入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class repository implements irepository {
    
    use infinity {
        __call as infinityCall;
    }
    
    /**
     * 缓存连接对象
     *
     * @var \queryyetsimple\cache\icache
     */
    protected $objConnect;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\cache\iconnect $objConnect            
     * @return void
     */
    public function __construct(iconnect $objConnect) {
        $this->objConnect = $objConnect;
    }
    
    /**
     * 获取缓存
     *
     * @param string $sCacheName            
     * @param mixed $mixDefault            
     * @param array $arrOption            
     * @return mixed
     */
    public function get($sCacheName, $mixDefault = false, array $arrOption = []) {
        return $this->objConnect->get ( $sCacheName, $mixDefault, $arrOption );
    }
    
    /**
     * 设置缓存
     *
     * @param string $sCacheName            
     * @param mixed $mixData            
     * @param array $arrOption            
     * @return void
     */
    public function set($sCacheName, $mixData, array $arrOption = []) {
        $this->objConnect->set ( $sCacheName, $mixData, $arrOption );
    }
    
    /**
     * 清除缓存
     *
     * @param string $sCacheName            
     * @param array $arrOption            
     * @return void
     */
    public function delele($sCacheName, array $arrOption = []) {
        $this->objConnect->delele ( $sCacheName, $arrOption );
    }
    
    /**
     * 返回缓存句柄
     *
     * @return mixed
     */
    public function handle() {
        return $this->objConnect->handle ();
    }
    
    /**
     * 关闭
     *
     * @return void
     */
    public function close() {
        $this->objConnect->close ();
    }
    
    /**
     * 拦截匿名注册控制器方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        if (static::hasInfinity ( $sMethod )) {
            return $this->infinityCall ( $sMethod, $arrArgs );
        }
        
        return call_user_func_array ( [ 
                $this->objConnect,
                $sMethod 
        ], $arrArgs );
    }
}
