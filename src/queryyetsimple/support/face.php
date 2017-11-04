<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\support;

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
use Exception;
use RuntimeException;
use BadMethodCallException;
use queryyetsimple\support\icontainer;

/**
 * 实现类的静态访问门面
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
abstract class face {
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\support\icontainer
     */
    protected static $objContainer;
    
    /**
     * 注入容器实例
     *
     * @var object
     */
    protected static $arrInstance = [ ];
    
    /**
     * 获取注册容器的实例
     *
     * @return mixed
     */
    public static function face( /* args */ ) {
        $strClass = static::name ();
        $strUnique = static::makeFaceKey ( $strClass, $arrArgs = func_get_args () );
        
        if (isset ( static::$arrInstance [$strUnique] )) {
            return static::$arrInstance [$strUnique];
        }
        if (! (static::$arrInstance [$strUnique] = static::container ()->make ( $strClass, $arrArgs ))) {
            throw new RuntimeException ( sprintf ( 'No %s services are found in the IOC container.', $strClass ) );
        }
        return static::$arrInstance [$strUnique];
    }
    
    /**
     * 返回服务容器
     *
     * @return \queryyetsimple\support\icontainer
     */
    public static function container() {
        return static::$objContainer;
    }
    
    /**
     * 设置服务容器
     *
     * @param \queryyetsimple\support\icontainer $objContainer            
     * @return void
     */
    public static function setContainer(icontainer $objContainer) {
        static::$objContainer = $objContainer;
    }
    
    /**
     * 生成唯一 key
     *
     * @param string $strClass            
     * @param array $arrArgs            
     * @return string
     */
    protected static function makeFaceKey($strClass, $arrArgs = []) {
        if ($arrArgs) {
            $strSerialize = '';
            foreach ( $arrArgs as $mixArg ) {
                // Serialization of 'Closure' is not allowed
                try {
                    // 返回指定对象的 hash id
                    // http://php.net/manual/zh/function.spl-object-hash.php
                    if (is_object ( $mixArg ))
                        $strSerialize .= spl_object_hash ( $mixArg );
                    else
                        $strSerialize .= serialize ( $mixArg );
                } catch ( Exception $oE ) {
                }
            }
            return $strClass . '.' . md5 ( $strSerialize );
        } else
            return $strClass;
    }
    
    /**
     * 缺省静态方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public static function __callStatic($sMethod, $arrArgs) {
        $objInstance = static::face ();
        if (! $objInstance) {
            throw new RuntimeException ( 'Can not find instance from container.' );
        }
        
        $calMethod = [ 
                $objInstance,
                $sMethod 
        ];
        if (! is_callable ( $calMethod )) {
            throw new BadMethodCallException ( sprintf ( 'Method %s is not exits.', $sMethod ) );
        }
        
        return call_user_func_array ( $calMethod, $arrArgs );
    }
}
