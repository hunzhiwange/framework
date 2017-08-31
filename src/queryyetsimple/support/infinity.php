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
use ReflectionFunction;
use BadMethodCallException;

/**
 * 实现类的无限扩展功能
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
trait infinity {
    
    /**
     * 注册的动态扩展
     *
     * @var array
     */
    protected static $arrInfinity = [ ];
    
    /**
     * 注册一个扩展
     *
     * @param string $strName            
     * @param callable $calInfinity            
     * @return void
     */
    public static function infinity($strName, callable $calInfinity) {
        static::$arrInfinity [$strName] = $calInfinity;
    }
    
    /**
     * 判断一个扩展是否注册
     *
     * @param string $strName            
     * @return bool
     */
    public static function hasInfinity($strName) {
        return isset ( static::$arrInfinity [$strName] );
    }
    
    /**
     * 缺省静态方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public static function __callStatic($sMethod, $arrArgs) {
        // 第一步：判断是否存在已经注册的命名
        if (static::hasInfinity ( $sMethod )) {
            if (static::$arrInfinity [$sMethod] instanceof Closure) {
                return call_user_func_array ( Closure::bind ( static::$arrInfinity [$sMethod], null, get_called_class () ), $arrArgs );
            } else {
                return call_user_func_array ( static::$arrInfinity [$sMethod], $arrArgs );
            }
        }
        
        throw new BadMethodCallException ( sprintf ( 'Method %s is not exits.', $sMethod ) );
    }
    
    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        if (static::hasInfinity ( $sMethod )) {
            if (static::$arrInfinity [$sMethod] instanceof Closure) {
                $objReflection = new ReflectionFunction ( static::$arrInfinity [$sMethod] );
                return call_user_func_array ( Closure::bind ( static::$arrInfinity [$sMethod], $objReflection->getClosureThis () ? $this : null, get_class ( $this ) ), $arrArgs );
            } else {
                return call_user_func_array ( static::$arrInfinity [$sMethod], $arrArgs );
            }
        }
        
        throw new BadMethodCallException ( sprintf ( 'Method %s is not exits.', $sMethod ) );
    }
}
