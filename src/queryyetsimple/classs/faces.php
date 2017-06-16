<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\classs;

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
use queryyetsimple\support\interfaces\container;

/**
 * 实现类的静态访问门面
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
abstract class faces {
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\support\interfaces\container
     */
    protected static $objProjectContainer = null;
    
    /**
     * 注入容器实例
     *
     * @var object
     */
    protected static $arrFacesInstance = [ ];
    
    /**
     * 获取注册容器的实例
     *
     * @return mixed
     */
    public static function faces( /* args */ ) {
        $strClass = static::name ();
        $strUnique = static::makeFacesKey ( $strClass, $arrArgs = func_get_args () );
        
        if (isset ( static::$arrFacesInstance [$strUnique] )) {
            return static::$arrFacesInstance [$strUnique];
        }
        if (! (static::$arrFacesInstance [$strUnique] = static::projectContainer ()->makeWithArgs ( $strClass, $arrArgs ))) {
            static::$arrFacesInstance [$strUnique] = new self ( $arrArgs );
        }
        return static::$arrFacesInstance [$strUnique];
    }
    
    /**
     * 返回服务容器
     *
     * @return \queryyetsimple\bootstrap\project
     */
    public static function projectContainer() {
        return static::$objProjectContainer;
    }
    
    /**
     * 设置服务容器
     *
     * @param \queryyetsimple\support\interfaces\container $objProject            
     * @return void
     */
    public static function setProjectContainer(container $objProject) {
        static::$objProjectContainer = $objProject;
    }
    
    /**
     * 生成唯一 key
     *
     * @param string $strClass            
     * @param array $arrArgs            
     * @return string
     */
    protected static function makeFacesKey($strClass, $arrArgs = []) {
        if ($arrArgs) {
            $strSerialize = '';
            foreach ( $arrArgs as $mixArg ) {
                // Serialization of 'Closure' is not allowed
                try {
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
        $objInstance = static::faces ();
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

namespace qys\classs;

/**
 * 实现类的静态访问门面
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
abstract class faces extends \queryyetsimple\classs\faces {
}
