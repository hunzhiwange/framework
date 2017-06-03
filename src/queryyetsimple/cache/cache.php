<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
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
use queryyetsimple\classs\faces as classs_faces;

/**
 * 缓存入口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class cache {
    
    use classs_faces;
    
    /**
     * 缓存连接对象
     *
     * @var \queryyetsimple\abstracts\cache
     */
    protected static $arrConnect;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrClasssFacesOption = [ 
            'cache\default' => 'filecache' 
    ];
    
    /**
     * 连接缓存并返回连接对象
     *
     * @param array $arrOption            
     * @return \queryyetsimple\abstracts\cache
     */
    public function connect($arrOption = []) {
        // 连接唯一标识
        $strDriver = ! empty ( $arrOption ['driver'] ) ? $arrOption ['driver'] : $this->classsFacesOption ( 'cache\default' );
        $strUnique = md5 ( is_array ( $arrOption ) ? json_encode ( $arrOption ) : $arrOption );
        
        // 已经存在直接返回
        if (isset ( static::$arrConnect [$strUnique] )) {
            return static::$arrConnect [$strUnique];
        }
        
        // 连接缓存
        $strConnectClass = 'queryyetsimple\\cache\\' . $strDriver;
        if (class_exists ( $strConnectClass )) {
            return static::$arrConnect [$strUnique] = (new $strConnectClass ( $arrOption ))->initClasssFacesOptionDefault ();
        } else {
            throw new Exception ( __ ( '缓存驱动 %s 不存在！', $strDriver ) );
        }
    }
    
    /**
     * 拦截匿名注册控制器方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        return call_user_func_array ( [ 
                $this->connect (),
                $sMethod 
        ], $arrArgs );
    }
}
