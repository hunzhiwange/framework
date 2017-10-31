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
use ArrayAccess;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
use BadMethodCallException;
use InvalidArgumentException;
use queryyetsimple\support\icontainer;

/**
 * IOC 容器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
class container implements ArrayAccess, icontainer {
    
    use flow_control;
    
    /**
     * 注册工厂
     *
     * @var array
     */
    protected $arrFactorys = [ ];
    
    /**
     * 注册的实例
     *
     * @var array
     */
    protected $arrInstances = [ ];
    
    /**
     * 单一实例
     *
     * @var array
     */
    protected $arrSingletons = [ ];
    
    /**
     * 别名支持
     *
     * @var array
     */
    protected $arrAlias = [ ];
    
    /**
     * 分组
     *
     * @var array
     */
    protected $arrGroups = [ ];
    
    /**
     * 注册到容器
     *
     * @param mixed $mixFactoryName            
     * @param mixed $mixFactory            
     * @param boolean $booShare            
     * @return $this
     */
    public function register($mixFactoryName, $mixFactory = null, $booShare = false) {
        if (is_array ( $mixFactoryName )) {
            list ( $mixFactoryName, $mixAlias ) = $this->parseAlias ( $mixFactoryName );
            $this->alias ( $mixFactoryName, $mixAlias );
        }
        
        if (! is_callable ( $mixFactory )) {
            if (is_null ( $mixFactory )) {
                $mixFactory = $mixFactoryName;
            }
            $mixFactory = function () use($mixFactory) {
                return $mixFactory;
            };
        }
        
        $this->arrFactorys [$mixFactoryName] = $mixFactory;
        
        if ($booShare)
            $this->arrSingletons [] = $mixFactoryName;
        
        return $this;
    }
    
    /**
     * 注册为实例
     *
     * @param mixed $mixFactoryName            
     * @param mixed $mixFactory            
     * @return $this
     */
    public function instance($mixFactoryName, $mixFactory = null) {
        if (is_array ( $mixFactoryName )) {
            list ( $mixFactoryName, $mixAlias ) = $this->parseAlias ( $mixFactoryName );
            $this->alias ( $mixFactoryName, $mixAlias );
        }
        
        if (is_null ( $mixFactory )) {
            $mixFactory = $mixFactoryName;
        }
        $this->arrInstances [$mixFactoryName] = $mixFactory;
        
        return $this;
    }
    
    /**
     * 注册单一实例
     *
     * @param scalar|array $mixFactoryName            
     * @param mixed $mixFactory            
     * @return $this
     */
    public function singleton($mixFactoryName, $mixFactory = null) {
        return $this->register ( $mixFactoryName, $mixFactory, true );
    }
    
    /**
     * 创建共享的闭包
     *
     * @param \Closure $objClosure            
     * @return \Closure
     */
    public function share(Closure $objClosure) {
        return function ($ojbContainer) use($objClosure) {
            static $obj;
            if (is_null ( $obj ))
                $obj = $ojbContainer ( $ojbContainer );
            return $obj;
        };
    }
    
    /**
     * 设置别名
     *
     * @param array|string $mixAlias            
     * @param string|null|array $mixValue            
     * @return $this
     */
    public function alias($mixAlias, $mixValue = null) {
        if (is_array ( $mixAlias )) {
            foreach ( $mixAlias as $strKey => $mixValue ) {
                $this->alias ( $strKey, $mixValue );
            }
        } else {
            foreach ( ( array ) $mixValue as $strValue ) {
                $this->arrAlias [$strValue] = $mixAlias;
            }
        }
        return $this;
    }
    
    /**
     * 分组注册
     *
     * @param string $strGroupName            
     * @param mixed $mixGroupData            
     * @return $this
     */
    public function group($strGroupName, $mixGroupData) {
        if (! isset ( $this->arrGroups [$strGroupName] )) {
            $this->arrGroups [$strGroupName] = [ ];
        }
        $this->arrGroups [$strGroupName] = $mixGroupData;
        return $this;
    }
    
    /**
     * 分组制造
     *
     * @param string $strGroupName            
     * @param array $arrArgs            
     * @return array
     */
    public function groupMake($strGroupName, array $arrArgs = []) {
        if (! isset ( $this->arrGroups [$strGroupName] )) {
            return [ ];
        }
        
        $arrResult = [ ];
        foreach ( ( array ) $this->arrGroups [$strGroupName] as $strGroupInstance ) {
            $arrResult [$strGroupInstance] = $this->make ( $strGroupInstance, $arrArgs );
        }
        return $arrResult;
    }
    
    /**
     * 服务容器返回对象
     *
     * @param string $strFactoryName            
     * @param array $arrArgs            
     * @return object|false
     */
    public function make($strFactoryName, array $arrArgs = []) {
        if (! is_array ( $arrArgs )) {
            throw new InvalidArgumentException ( __ ( 'makeWithArgs 第二个参数只能为 array' ) );
        }
        
        // 别名
        if (isset ( $this->arrAlias [$strFactoryName] )) {
            $strFactoryName = $this->arrAlias [$strFactoryName];
        }
        
        // 存在直接返回
        if (isset ( $this->arrInstances [$strFactoryName] )) {
            return $this->arrInstances [$strFactoryName];
        }
        
        // 生成实例
        if (! isset ( $this->arrFactorys [$strFactoryName] )) {
            return $this->getInjectionObject ( $strFactoryName, $arrArgs );
        }
        
        array_unshift ( $arrArgs, $this );
        $mixInstances = call_user_func_array ( $this->arrFactorys [$strFactoryName], $arrArgs );
        
        // 单一实例
        if (in_array ( $strFactoryName, $this->arrSingletons )) {
            return $this->arrInstances [$strFactoryName] = $mixInstances;
        }         

        // 多个实例
        else {
            return $mixInstances;
        }
    }
    
    /**
     * 实例回调自动注入
     *
     * @param callable $calClass            
     * @param array $arrArgs            
     * @return mixed
     */
    public function call($calClass, array $arrArgs = []) {
        if (! is_array ( $arrArgs )) {
            throw new InvalidArgumentException ( __ ( 'callWithArgs 第二个参数只能为 array' ) );
        }
        
        if (($arrInjection = $this->parseInjection ( $calClass, $arrArgs )) && isset ( $arrInjection ['args'] )) {
            $arrArgs = $this->getInjectionArgs ( $arrInjection ['args'], $arrArgs, $arrInjection ['class'] );
        }
        
        return call_user_func_array ( $calClass, $arrArgs );
    }
    
    /**
     * 统一去掉前面的斜杠
     *
     * @param string $strFactoryName            
     * @return mixed
     */
    protected function normalize($strFactoryName) {
        return ltrim ( $strFactoryName, '\\' );
    }
    
    /**
     * 根据 class 名字创建实例
     *
     * @param string $strClassName            
     * @param array $arrArgs            
     * @return object
     */
    protected function getInjectionObject($strClassName, array $arrArgs = []) {
        if (! class_exists ( $strClassName )) {
            return false;
        }
        
        // 注入构造器
        if (($arrInjection = $this->parseInjection ( $strClassName )) && isset ( $arrInjection ['args'] )) {
            return $this->newInstanceArgs ( $strClassName, $this->getInjectionArgs ( $arrInjection ['args'], $arrArgs, $arrInjection ['class'] ) );
        } else {
            return $this->newInstanceArgs ( $strClassName, $arrArgs );
        }
    }
    
    /**
     * 分析自动依赖注入
     *
     * @param mixed $mixClassOrCallback            
     * @return array
     */
    protected function parseInjection($mixClassOrCallback) {
        $arrResult = $arrParameter = [ ];
        $booFind = $booFindClass = false;
        
        if ($mixClassOrCallback instanceof Closure) {
            $objReflection = new ReflectionFunction ( $mixClassOrCallback );
            if (($arrTemp = $objReflection->getParameters ())) {
                $arrParameter = $arrTemp;
            }
        } elseif (is_callable ( $mixClassOrCallback )) {
            $objReflection = new ReflectionMethod ( $mixClassOrCallback [0], $mixClassOrCallback [1] );
            if (($arrTemp = $objReflection->getParameters ())) {
                $arrParameter = $arrTemp;
            }
        } elseif (is_string ( $mixClassOrCallback )) {
            $objReflection = new ReflectionClass ( $mixClassOrCallback );
            if (! $objReflection->isInstantiable ()) {
                throw new InvalidArgumentException ( sprintf ( 'Class %s is not instantiable.', $mixClassOrCallback ) );
            }
            if (($objConstructor = $objReflection->getConstructor ()) && ($arrTemp = $objConstructor->getParameters ())) {
                $arrParameter = $arrTemp;
            }
        }
        
        foreach ( $arrParameter as $objParameter ) {
            $strName = $objParameter->name;
            
            try {
                if (($objParameterClass = $objParameter->getClass ()) && $objParameterClass instanceof ReflectionClass && ($objParameterClass = $objParameterClass->getName ())) {
                    // 接口绑定实现
                    if (($objParameterMake = $this->make ( $objParameterClass )) !== false) {
                        // 实例对象
                        if (is_object ( $objParameterMake )) {
                            $arrResult ['args'] [$strName] = $objParameterMake;
                            $booFindClass = true;
                        }                        

                        // 接口绑定实现
                        elseif (class_exists ( $objParameterMake )) {
                            $arrResult ['args'] [$strName] = $this->make ( $objParameterMake );
                            $booFindClass = true;
                        }
                    } elseif (class_exists ( $objParameterClass )) {
                        $arrResult ['args'] [$strName] = $this->make ( $objParameterClass );
                        $booFindClass = true;
                    } else {
                        throw new InvalidArgumentException ( sprintf ( 'Class or interface %s is not register in container', $objParameterClass ) );
                    }
                } elseif ($objParameter->isDefaultValueAvailable ()) {
                    $arrResult ['args'] [$strName] = $objParameter->getDefaultValue ();
                } else {
                    $arrResult ['args'] [$strName] = '';
                }
            } catch ( ReflectionException $oE ) {
                throw new InvalidArgumentException ( $oE->getMessage () );
            }
        }
        
        $arrResult ['class'] = $booFindClass;
        
        return $arrResult;
    }
    
    /**
     * 注入参数分析
     *
     * @param array $arrArgs            
     * @param array $arrExtends            
     * @param boolean $booFindClass            
     * @return array
     */
    protected function getInjectionArgs(array $arrArgs, array $arrExtends = [], $booFindClass = false) {
        $arrParse = [ ];
        
        foreach ( $arrExtends as $mixKey => $mixExtend ) {
            if (isset ( $arrArgs [$mixKey] )) {
                unset ( $arrArgs [$mixKey] );
            } elseif ($booFindClass === false) {
                array_shift ( $arrArgs );
            }
            $arrParse [] = $mixExtend;
        }
        
        foreach ( $arrArgs as $mixArg ) {
            $arrParse [] = $mixArg;
        }
        
        unset ( $arrArgs, $arrExtends );
        return $arrParse;
    }
    
    /**
     * 动态创建实例对象
     *
     * @param string $strClass            
     * @param array $arrArgs            
     * @return mixed
     */
    protected function newInstanceArgs($strClass, $arrArgs) {
        return (new ReflectionClass ( $strClass ))->newInstanceArgs ( $arrArgs );
    }
    
    /**
     * 解析注册容器对象别名
     *
     * @param array $arrFactoryName            
     * @return array
     */
    protected function parseAlias(array $arrFactoryName) {
        return [ 
                key ( $arrFactoryName ),
                current ( $arrFactoryName ) 
        ];
    }
    
    /**
     * 判断容器对象是否存在
     *
     * @param string $strFactoryName            
     * @return bool
     */
    public function offsetExists($strFactoryName) {
        return isset ( $this->arrFactorys [$this->normalize ( $strFactoryName )] );
    }
    
    /**
     * 获取一个容器对象
     *
     * @param string $strFactoryName            
     * @return mixed
     */
    public function offsetGet($strFactoryName) {
        return $this->make ( $strFactoryName );
    }
    
    /**
     * 注册容器对象
     *
     * @param string $strFactoryName            
     * @param mixed $mixFactory            
     * @return void
     */
    public function offsetSet($strFactoryName, $mixFactory) {
        return $this->register ( $strFactoryName, $mixFactory );
    }
    
    /**
     * 删除一个容器对象
     *
     * @param string $strFactoryName            
     * @return void
     */
    public function offsetUnset($strFactoryName) {
        $strFactoryName = $this->normalize ( $strFactoryName );
        foreach ( [ 
                'Factorys',
                'Instances',
                'Singletons' 
        ] as $strType ) {
            $strType = 'arr' . $strType;
            if (isset ( $this->{$strType} [$strFactoryName] ))
                unset ( $this->{$strType} [$strFactoryName] );
        }
    }
    
    /**
     * 捕捉支持属性参数
     *
     * @param string $sName
     *            支持的项
     * @return 设置项
     */
    public function __get($sName) {
        return $this [$sName];
    }
    
    /**
     * 设置支持属性参数
     *
     * @param string $sName
     *            支持的项
     * @param mixed $mixVal
     *            支持的值
     * @return void
     */
    public function __set($sName, $mixVal) {
        $this [$sName] = $mixVal;
        return $this;
    }
    
    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return mixed
     */
    public function __call($sMethod, $arrArgs) {
        if ($this->placeholderFlowControl ( $sMethod )) {
            return $this;
        }
        
        throw new BadMethodCallException ( sprintf ( 'Method %s is not exits.', $sMethod ) );
    }
}
