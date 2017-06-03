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
use ReflectionFunction;
use BadMethodCallException;
use RuntimeException;
use queryyetsimple\option\option;
use queryyetsimple\mvc\project;

/**
 * 实现类的静态访问门面以及动态扩展类的功能
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
trait faces {
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\mvc\project
     */
    protected static $objProjectContainer = null;
    
    /**
     * 注入容器实例
     *
     * @var object
     */
    protected static $objFacesInstance = null;
    
    /**
     * 注册的动态扩展
     *
     * @var array
     */
    protected static $arrFaces = [ ];
    
    /**
     * 类中的扩展参数
     *
     * @var array
     */
    protected $arrClasssFacesOptionIn = [ ];
    
    /**
     * 注册一个扩展
     *
     * @param string $strName            
     * @param callable $calFaces            
     * @return void
     */
    public static function registerFaces($strName, callable $calFaces) {
        static::$arrFaces [$strName] = $calFaces;
    }
    
    /**
     * 判断一个扩展是否注册
     *
     * @param string $strName            
     * @return bool
     */
    public static function hasFaces($strName) {
        return isset ( static::$arrFaces [$strName] );
    }
    
    /**
     * 获取注册容器的实例
     *
     * @param boolean $booNew            
     * @return mixed
     */
    public static function getFacesInstance($booNew = false /* args */) {
        if (static::$objFacesInstance) {
            return static::$objFacesInstance;
        }
        
        if ($booNew === true || ! static::projectContainer ( true ) || ! (static::$objFacesInstance = static::projectContainer ( true )->make ( get_called_class () ))) {
            static::$objFacesInstance = new self ();
        }
        
        if (method_exists ( static::$objFacesInstance, 'initClasssFacesOption' )) {
            return static::$objFacesInstance->initClasssFacesOption ();
        } else {
            return static::$objFacesInstance->initClasssFacesOptionDefault ();
        }
    }
    
    /**
     * new 获取注册容器的实例
     *
     * @param \queryyetsimple\mvc\project $objProject            
     * @return mixed
     */
    public static function singleton($objProject = null/* args */) {
        if (! is_null ( $objProject ))
            static::projectContainer ( $objProject );
        $arrArgs = func_get_args ();
        array_shift ( $arrArgs );
        array_unshift ( $arrArgs, true );
        return call_user_func_array ( [ 
                get_called_class (),
                'getFacesInstance' 
        ], $arrArgs );
    }
    
    /**
     * 设置或者返回服务容器
     *
     * @param \queryyetsimple\mvc\project $objProject            
     * @return void
     */
    public static function projectContainer($objProject = null) {
        if (is_null ( $objProject )) {
            return static::$objProjectContainer ?  : (class_exists ( 'queryyetsimple\mvc\project' ) ? project::bootstrap () : false);
        } elseif (is_object ( $objProject )) {
            static::$objProjectContainer = $objProject;
        } elseif ($objProject === true) {
            return class_exists ( 'queryyetsimple\mvc\project' ) ? project::bootstrap () : false;
        }
    }
    
    /**
     * 返回配置
     *
     * @param string $strArgsName            
     * @return mixed
     */
    public function classsFacesOption($strArgsName) {
        return isset ( $this->arrClasssFacesOptionIn [$strArgsName] ) ? $this->arrClasssFacesOptionIn [$strArgsName] : null;
    }
    
    /**
     * 返回初始化参数
     *
     * @return array
     */
    public function classsFacesOptionKey() {
        if (! $this->checkClasssFacesOption ()) {
            return [ ];
        }
        return array_keys ( $this->arrClasssFacesOption );
    }
    
    /**
     * 初始化静态入口配置
     *
     * @return void
     */
    public function initClasssFacesOptionDefault() {
        if (! $this->checkClasssFacesOption ()) {
            return $this;
        }
        
        $this->mergeClasssFacesOption ();
        
        if (! class_exists ( 'queryyetsimple\option\option' )) {
            return $this;
        }
        
        $arrArgs = [ ];
        foreach ( $this->classsFacesOptionKey () as $sArgs ) {
            if (is_null ( $mixTemp = option::gets ( $sArgs ) ))
                continue;
            $arrArgs [$sArgs] = $mixTemp;
        }
        return $this->setClasssFacesOption ( $arrArgs );
    }
    
    /**
     * 初始化参数是否存在
     *
     * @return boolean
     */
    public function checkClasssFacesOption() {
        return property_exists ( $this, 'arrClasssFacesOption' );
    }
    
    /**
     * 设置配置
     *
     * @param array|string $mixArgsName            
     * @param string $strArgsValue            
     * @return void
     */
    public function setClasssFacesOption($mixArgsName, $strArgsValue = null) {
        if (is_array ( $mixArgsName )) {
            $this->arrClasssFacesOptionIn = array_merge ( $this->arrClasssFacesOptionIn, $mixArgsName );
        } else {
            $this->arrClasssFacesOptionIn [$mixArgsName] = $strArgsValue;
        }
        return $this;
    }
    
    /**
     * 合并默认配置
     *
     * @return array
     */
    public function mergeClasssFacesOption() {
        if (! $this->checkClasssFacesOption ()) {
            return;
        }
        $this->arrClasssFacesOptionIn = array_merge ( $this->arrClasssFacesOption, $this->arrClasssFacesOptionIn );
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
        if (static::hasFaces ( $sMethod )) {
            if (static::$arrFaces [$sMethod] instanceof Closure) {
                return call_user_func_array ( Closure::bind ( static::$arrFaces [$sMethod], null, get_called_class () ), $arrArgs );
            } else {
                return call_user_func_array ( static::$arrFaces [$sMethod], $arrArgs );
            }
        }
        
        // 第二步：尝试读取注入容器中的类，没有则自身创建为单一实例
        $objInstance = static::getFacesInstance ();
        if (! $objInstance) {
            throw new RuntimeException ( 'Can not find instance from container.' );
        }
        
        // 移除最后一位方法名字去访问内容，于是可以在方法后面加入 s 或者 _ 来访问类的方法
        // 非 s 和 _ 结尾的由程序通过 __call 方法实现
        if (in_array ( substr ( $sMethod, - 1 ), [ 
                's',
                '_' 
        ] )) {
            $sMethod = substr ( $sMethod, 0, - 1 );
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
    
    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        if (static::hasFaces ( $sMethod )) {
            if (static::$arrFaces [$sMethod] instanceof Closure) {
                $objReflection = new ReflectionFunction ( static::$arrFaces [$sMethod] );
                return call_user_func_array ( Closure::bind ( static::$arrFaces [$sMethod], $objReflection->getClosureThis () ? $this : NULL, get_class ( $this ) ), $arrArgs );
            } else {
                return call_user_func_array ( static::$arrFaces [$sMethod], $arrArgs );
            }
        }
        
        throw new BadMethodCallException ( sprintf ( 'Method %s is not exits.', $sMethod ) );
    }
}
