<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc;

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
use ArrayAccess;
use JsonSerializable;
use queryyetsimple\flow\control;
use queryyetsimple\string\string;
use queryyetsimple\classs\serialize;
use queryyetsimple\collection\collection;
use queryyetsimple\support\interfaces\arrayable;

/**
 * 模型 Object Relational Mapping
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.27
 * @version 1.0
 */
class model implements JsonSerializable, ArrayAccess, arrayable {
    
    use control;
    use serialize;
    
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $strTable = '';
    
    /**
     * 此模型的连接名称
     *
     * @var mixed
     */
    protected $mixConnect = '';
    
    /**
     * 模型属性
     *
     * @var array
     */
    protected $arrProp = [ ];
    
    /**
     * 改变的模型属性
     *
     * @var array
     */
    protected $arrChangeProp = [ ];
    
    /**
     * 构造器初始化数据黑名单
     *
     * @var array
     */
    protected $arrConstructBlack = [ ];
    
    /**
     * 构造器初始化数据白名单
     *
     * @var array
     */
    protected $arrConstructWhite = [ ];
    
    /**
     * 数据赋值黑名单
     *
     * @var array
     */
    protected $arrFillBlack = [ ];
    
    /**
     * 数据赋值白名单
     *
     * @var array
     */
    protected $arrFillWhite = [ ];
    
    /**
     * 写入数据黑名单
     *
     * @var array
     */
    protected $arrCreateBlack = [ ];
    
    /**
     * 写入数据白名单
     *
     * @var array
     */
    protected $arrCreateWhite = [ ];
    
    /**
     * 更新数据黑名单
     *
     * @var array
     */
    protected $arrUpdateBlack = [ ];
    
    /**
     * 更新数据白名单
     *
     * @var array
     */
    protected $arrUpdateWhite = [ ];
    
    /**
     * 只读属性
     *
     * @var array
     */
    protected $arrReadonly = [ ];
    
    /**
     * 是否自动提交 POST 数据
     *
     * @var boolean
     */
    protected $booAutoPost = true;
    
    /**
     * 是否处于强制改变属性中
     *
     * @var boolean
     */
    protected $booInChangePropForce = false;
    
    /**
     * 是否自动填充
     *
     * @var boolean
     */
    protected $booAutoFill = true;
    
    /**
     * 自动填充
     *
     * @var array
     */
    protected $arrAutoFill = [ ];
    
    /**
     * 创建自动填充
     *
     * @var array
     */
    protected $arrCreateFill = [ ];
    
    /**
     * 更新自动填充
     *
     * @var array
     */
    protected $arrUpdateFill = [ ];
    
    /**
     * 数据类型
     *
     * @var array
     */
    protected $arrConversion = [ ];
    
    // protected $arrPostField = [ ];
    
    // protected $booIncrementing = false;
    
    // protected $arrTimestamps = false;
    // protected $_sClassName;
    
    /**
     * 模型的日期字段保存格式。
     *
     * @var string
     */
    // protected $dateFormat = 'U';
    
    /**
     * 构造函数
     *
     * @param array|null $arrData            
     * @param string $strTable            
     * @param mixed $mixConnect            
     * @return void
     */
    public function __construct($arrData = null, $strTable = null, $mixConnect = null) {
        if (is_array ( $arrData ) && $arrData) {
            if ($this->arrConstructBlack) {
                foreach ( $arrData as $strField => $mixValue ) {
                    if (in_array ( $strField, $this->arrConstructBlack ) && ! in_array ( $strField, $this->arrConstructBlack )) {
                        unset ( $arrData [$strField] );
                    }
                }
            }
            if ($arrData) {
                $this->changeProp ( $arrData );
            }
        }
        
        if (! is_null ( $strTable )) {
            $this->strTable = $strTable;
        }
        
        if (! is_null ( $mixConnect )) {
            $this->mixConnect = $mixConnect;
        }
    }
    
    /**
     * 自动判断快捷方式
     *
     * @param array|null $arrData            
     * @return $this
     */
    public function save($arrData = null) {
        return $this->saves ( 'save', $arrData );
    }
    
    /**
     * 新增快捷方式
     *
     * @param array|null $arrData            
     * @return $this
     */
    public function create($arrData = null) {
        return $this->saves ( 'update', $arrData );
    }
    
    /**
     * 更新快捷方式
     *
     * @param array|null $arrData            
     * @return $this
     */
    public function update($arrData = null) {
        return $this->saves ( 'update', $arrData );
    }
    
    /**
     * replace 快捷方式
     *
     * @param array|null $arrData            
     * @return $this
     */
    public function replace($arrData = null) {
        return $this->saves ( 'replace', $arrData );
    }
    
    /**
     * 保存统一入口
     *
     * @param strint $sSaveMethod            
     * @param array|null $arrData            
     * @return $this
     */
    public function saves($sSaveMethod = 'save', $arrData = null) {
        // 强制更新数据
        if (is_array ( $arrData ) && $arrData) {
            $this->changePropForce ( $arrData );
        }
        
        // 表单自动填充
        $this->parseAutoPost ();
        
        // 程序通过内置方法统一实现
        switch (strtolower ( $sSaveMethod )) {
            case 'create' :
                $this->createReal ();
                break;
            case 'update' :
                $this->updateReal ();
                break;
            case 'replace' :
                $this->replaceReal ();
                break;
            case 'save' :
            default :
                $arrPrimaryData = $this->primaryKey ( true );
                
                // 复合主键的情况下，则使用 replace 方式
                if (is_array ( $arrPrimaryData )) {
                    $this->replaceReal ();
                }                 

                // 单一主键
                else {
                    if (empty ( $arrPrimaryData )) {
                        $this->createReal ();
                    } else {
                        $this->updateReal ();
                    }
                }
                break;
        }
        
        return $this;
    }
    
    /**
     * 获取主键
     *
     * @param boolean $booUpdateChange            
     * @return null|array
     */
    public function primaryKey($booUpdateChange = false) {
        $arrPrimaryData = [ ];
        
        $arrPrimaryKey = $this->meta ()->getPrimaryKey ();
        foreach ( $arrPrimaryKey as $sPrimaryKey ) {
            if (! isset ( $this->arrProp [$sPrimaryKey] )) {
                continue;
            }
            if ($booUpdateChange === true) {
                if (! in_array ( $sPrimaryKey, $this->arrChangeProp )) {
                    $arrPrimaryData [$sPrimaryKey] = $this->arrProp [$sPrimaryKey];
                }
            } else {
                $arrPrimaryData [$sPrimaryKey] = $this->arrProp [$sPrimaryKey];
            }
        }
        
        // 复合主键，但是数据不完整则忽略
        if (count ( $arrPrimaryKey ) > 1 && count ( $arrPrimaryKey ) != count ( $arrPrimaryData )) {
            return null;
        }
        
        if (count ( $arrPrimaryData ) == 1) {
            $arrPrimaryData = reset ( $arrPrimaryData );
        }
        
        if (! empty ( $arrPrimaryData )) {
            return $arrPrimaryData;
        } else {
            return null;
        }
    }
    
    /**
     * 改变属性
     *
     * < update 调用无效，请换用 changePropForce >
     *
     * @param mixed $mixProp            
     * @param mixed $mixValue            
     * @return $this
     */
    public function changeProp($mixProp, $mixValue = null) {
        if (! is_array ( $mixProp )) {
            $mixProp = [ 
                    $mixProp => $mixValue 
            ];
        }
        
        $booInChangePropForce = $this->getInChangePropForce ();
        $mixProp = $this->meta ()->fieldsProps ( $mixProp );
        
        foreach ( $mixProp as $sName => $mixValue ) {
            
            if (is_null ( $mixValue ) && ($strCamelize = 'set' . ucwords ( string::camelize ( $sName ) ) . 'Prop') && method_exists ( $this, $strCamelize )) {
                $this->$strCamelize ( $this->getProp ( $sName ), $sName );
                $mixValue = $this->getProp ( $sName );
            }
            
            $this->arrProp [$sName] = $mixValue;
            if ($booInChangePropForce === true && ! in_array ( $sName, $this->arrReadonly ) && ! in_array ( $sName, $this->arrChangeProp )) {
                $this->arrChangeProp [] = $sName;
            }
        }
        
        return $this;
    }
    
    /**
     * 强制改变属性
     *
     * @param mixed $mixPropName            
     * @param mixed $mixValue            
     * @return $this
     */
    public function changePropForce($mixPropName, $mixValue = null) {
        $this->setInChangePropForce ( true );
        call_user_func_array ( [ 
                $this,
                'changeProp' 
        ], func_get_args () );
        $this->setInChangePropForce ( false );
        return $this;
    }
    
    /**
     * 返回属性
     *
     * @param string $strPropName            
     * @return mixed
     */
    public function getProp($strPropName) {
        if (! isset ( $this->arrProp [$strPropName] ))
            return null;
        
        $mixValue = $this->arrProp [$strPropName];
        if (($strCamelize = 'get' . ucwords ( string::camelize ( $strPropName ) ) . 'Prop') && method_exists ( $this, $strCamelize )) {
            $mixValue = $this->$strCamelize ( $mixValue );
        }
        
        if ($this->hasConversion ( $strPropName ))
            $mixValue = $this->conversionProp ( $strPropName, $mixValue );
        
        return $mixValue;
    }
    
    /**
     * 是否存在属性
     *
     * @param string $sPropName            
     * @return boolean
     */
    public function hasProp($sPropName) {
        return array_key_exists ( $sPropName, $this->arrProp );
    }
    
    /**
     * 删除属性
     *
     * @param string $sPropName            
     * @return $this
     */
    public function deleteProp($sPropName) {
        if (! isset ( $this->arrProp [$sPropName] )) {
            unset ( $this->arrProp [$sPropName] );
        }
        return $this;
    }
    
    /**
     * 返回改变
     *
     * @return array
     */
    public function getChanged() {
        return $this->arrChangedProp;
    }
    
    /**
     * 检测是否已经改变
     *
     * @param string $sPropsName            
     * @return boolean
     */
    public function hasChanged($sPropsName = null) {
        // null 判读是否存在属性
        if (is_null ( $sPropsName )) {
            return ! empty ( $this->arrChangedProp );
        }
        
        $arrPropsName = helper::arrays ( $sPropsName );
        foreach ( $arrPropsName as $sPropName ) {
            if (isset ( $this->arrChangedProp [$sPropName] ))
                return true;
        }
        return false;
    }
    
    /**
     * 清除改变属性
     *
     * @param mixed $mixProp            
     * @return $this
     */
    public function clearChanged($mixProp = null) {
        if (is_null ( $mixProp )) {
            $this->arrChangedProp = [ ];
        } else {
            $mixProp = helper::arrays ( $mixProp );
            foreach ( $mixProp as $sProp ) {
                if (isset ( $this->arrChangedProp [$sProp] ))
                    unset ( $this->arrChangedProp [$sProp] );
            }
        }
        return $this;
    }
    
    /**
     * 设置表
     *
     * @param string $strTable            
     * @return $this
     */
    public function table($strTable) {
        $this->strTable = $strTable;
        return $this;
    }
    
    /**
     * 返回设置表
     *
     * @return string
     */
    public function getTable() {
        return $this->strTable;
    }
    
    /**
     * 设置连接
     *
     * @param mixed $mixConnect            
     * @return $this
     */
    public function connect($mixConnect) {
        $this->mixConnect = $mixConnect;
        return $this;
    }
    
    /**
     * 返回设置连接
     *
     * @return mixed
     */
    public function getConnect() {
        return $this->mixConnect;
    }
    
    /**
     * 是否自动提交表单数据
     *
     * @param boolean $booAutoPost            
     * @return $this
     */
    public function autoPost($booAutoPost = true) {
        $this->booAutoPost = $booAutoPost;
        return $this;
    }
    
    /**
     * 返回是否自动提交表单数据
     *
     * @return boolean
     */
    public function getAutoPost() {
        return $this->booAutoPost;
    }
    
    /**
     * 是否自动填充数据
     *
     * @param boolean $booAutoFill            
     * @return $this
     */
    public function autoFill($booAutoFill = true) {
        $this->booAutoFill = $booAutoFill;
        return $this;
    }
    
    /**
     * 返回是否自动填充数据
     *
     * @return boolean
     */
    public function getAutoFill() {
        return $this->booAutoFill;
    }
    
    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray() {
        return $this->arrProp;
    }
    
    /**
     * 对象转 JSON
     *
     * @param integer $intOption            
     * @return string
     */
    public function toJson($intOption = JSON_UNESCAPED_UNICODE) {
        return json_encode ( $this->toArray (), $intOption );
    }
    
    /**
     * 转换 JSON
     *
     * @param string $strValue            
     * @param bool $booObject            
     * @return mixed
     */
    public function fromJson($strValue, $booObject = false) {
        return json_decode ( $strValue, ! $booObject );
    }
    
    /**
     * 转换 Serialize
     *
     * @param string $strValue            
     * @return mixed
     */
    public function fromSerialize($strValue) {
        return unserialize ( $strValue );
    }
    
    /**
     * 实现 JsonSerializable::jsonSerialize
     *
     * @return boolean
     */
    public function jsonSerialize() {
        return $this->toArray ();
    }
    
    /**
     * 是否存在转换类型
     *
     * @param string $strKey            
     * @param array|string|null $mixType            
     * @return bool
     */
    public function hasConversion($strKey, $mixType = null) {
        if (array_key_exists ( $strKey, $this->getConversion () )) {
            return $mixType ? in_array ( $this->getConversionType ( $strKey ), ( array ) $mixType, true ) : true;
        }
        
        return false;
    }
    
    /**
     * 获取转换类型
     *
     * @return array
     */
    public function getConversion() {
        return $this->arrConversion;
    }
    
    /**
     * 返回模型类的 meta 对象
     *
     * @return Meta
     */
    public function meta() {
        if (! $this->strTable) {
            $strTable = get_called_class ();
            $strTable = explode ( '\\', $strTable );
            $this->strTable = array_pop ( $strTable );
        }
        return meta::instance ( $this->strTable, $this->mixConnect );
    }
    
    /**
     * 添加数据
     *
     * @return void
     */
    protected function createReal() {
        $this->parseAutoFill ( 'create' );
        
        $arrSaveData = [ ];
        foreach ( $this->arrProp as $sPropName => $mixValue ) {
            if (is_null ( $mixValue )) {
                continue;
            }
            if (in_array ( $sPropName, $this->arrFillBlack ) && ! in_array ( $sPropName, $this->arrFillWhite ) && ! in_array ( $sPropName, $this->arrCreateWhite )) {
                continue;
            }
            if (in_array ( $sPropName, $this->arrCreateBlack ) && ! in_array ( $sPropName, $this->arrCreateWhite )) {
                continue;
            }
            $arrSaveData [$sPropName] = $mixValue;
        }
        
        if ($arrSaveData) {
            $arrLastInsertId = $this->meta ()->insert ( $arrSaveData );
            $this->arrProp = array_merge ( $this->arrProp, $arrLastInsertId );
        }
        
        $this->clearChanged ();
        
        return reset ( $arrLastInsertId );
    }
    
    /**
     * 更新数据
     *
     * @return void|int
     */
    protected function updateReal() {
        $this->parseAutoFill ( 'update' );
        
        $arrSaveData = [ ];
        foreach ( $this->arrProp as $sPropName => $mixValue ) {
            if (! in_array ( $sPropName, $this->arrChangeProp )) {
                continue;
            }
            if (in_array ( $sPropName, $this->arrFillBlack ) && ! in_array ( $sPropName, $this->arrFillWhite ) && ! in_array ( $sPropName, $this->arrUpdateWhite )) {
                continue;
            }
            if (in_array ( $sPropName, $this->arrUpdateBlack ) && ! in_array ( $sPropName, $this->arrUpdateWhite )) {
                continue;
            }
            $arrSaveData [$sPropName] = $mixValue;
        }
        
        if ($arrSaveData) {
            $arrConditions = array ();
            foreach ( $this->meta ()->getPrimaryKey () as $sFieldName ) {
                if (isset ( $arrSaveData [$sFieldName] )) {
                    unset ( $arrSaveData [$sFieldName] );
                }
                if (! empty ( $this->arrProp [$sFieldName] )) {
                    $arrConditions [$sFieldName] = $this->arrProp [$sFieldName];
                }
            }
            if (! empty ( $arrSaveData ) && ! empty ( $arrConditions )) {
                $intNum = $this->meta ()->update ( $arrConditions, $arrSaveData );
            }
        }
        
        $this->clearChanged ();
        
        return isset ( $intNum ) ? $intNum : null;
    }
    
    /**
     * 模拟 replace 数据
     *
     * @return void
     */
    protected function replaceReal() {
        try {
            $this->createReal ();
        } catch ( Exception $oE ) {
            $this->updateReal ();
        }
    }
    
    /**
     * 自动提交表单数据
     *
     * @return void
     */
    protected function parseAutoPost() {
        if ($this->booAutoPost === false || empty ( $_POST )) {
            return;
        }
        
        $_POST = $this->meta ()->fieldsProps ( $_POST );
        foreach ( $_POST as $strField => $mixValue ) {
            if (! in_array ( $strField, $this->arrChangeProp )) {
                $this->arrProp [$strField] = trim ( $mixValue );
                $this->arrChangeProp [] = $strField;
            }
        }
    }
    
    /**
     * 自动填充
     *
     * @param string $strType            
     * @return void
     */
    protected function parseAutoFill($strType = 'create') {
        if ($this->booAutoFill === false) {
            return;
        }
        
        if ($strType == 'create') {
            $arrFill = array_merge ( $this->arrAutoFill, $this->arrCreateFill );
        } else {
            $arrFill = array_merge ( $this->arrAutoFill, $this->arrUpdateFill );
        }
        
        if (! $arrFill)
            return;
        
        foreach ( $arrFill as $mixKey => $mixValue ) {
            if (is_integer ( $mixKey )) {
                $mixKey = $mixValue;
                $mixValue = null;
            }
            $this->changePropForce ( $mixKey, $mixValue );
        }
    }
    
    /**
     * 获取转换类型
     *
     * @param string $strKey            
     * @return string
     */
    protected function getConversionType($strKey) {
        return trim ( strtolower ( $this->getConversion ()[$strKey] ) );
    }
    
    /**
     * 转换属性
     *
     * @param string $strKey            
     * @param mixed $mixValue            
     * @return mixed
     */
    protected function conversionProp($strKey, $mixValue) {
        if (is_null ( $mixValue )) {
            return $mixValue;
        }
        
        switch ($this->getConversionType ( $strKey )) {
            case 'int' :
            case 'integer' :
                return ( int ) $mixValue;
            case 'real' :
            case 'float' :
            case 'double' :
                return ( float ) $mixValue;
            case 'string' :
                return ( string ) $mixValue;
            case 'bool' :
            case 'boolean' :
                return ( bool ) $mixValue;
            case 'object' :
                return $this->fromJson ( $mixValue, true );
            case 'array' :
            case 'json' :
                return $this->fromJson ( $mixValue );
            case 'collection' :
                return new collection ( $this->fromJson ( $mixValue ) );
            default :
                return $mixValue;
        }
    }
    
    /**
     * 设置是否处于强制更新属性的
     *
     * @return boolean
     */
    protected function setInChangePropForce($booInChangePropForce = true) {
        $this->booInChangePropForce = $booInChangePropForce;
    }
    
    /**
     * 返回是否处于强制更新属性的
     *
     * @return boolean
     */
    protected function getInChangePropForce() {
        return $this->booInChangePropForce;
    }
    
    /**
     * 魔术方法获取
     *
     * @param string $sPropName            
     * @return mixed
     */
    public function __get($sPropName) {
        return $this->getProp ( $sPropName );
    }
    
    /**
     * 强制更新属性值
     *
     * @param string $sPropName            
     * @param mixed $mixValue            
     * @return $this
     */
    public function __set($sPropName, $mixValue) {
        return $this->changePropForce ( $sPropName, $mixValue );
    }
    
    /**
     * 是否存在属性
     *
     * @param string $sPropName            
     * @return boolean
     */
    public function __isset($sPropName) {
        return $this->hasProp ( $sPropName );
    }
    
    /**
     * 实现 ArrayAccess::offsetExists
     *
     * @param string $sPropName            
     * @return boolean
     */
    public function offsetExists($sPropName) {
        return $this->hasProp ( $sPropName );
    }
    
    /**
     * 实现 ArrayAccess::offsetSet
     *
     * @param string $sPropName            
     * @param mixed $mixValue            
     * @return $this
     */
    public function offsetSet($sPropName, $mixValue) {
        return $this->changePropForce ( $sPropName, $mixValue );
    }
    
    /**
     * 实现 ArrayAccess::offsetGet
     *
     * @param string $sPropName            
     * @return mixed
     */
    public function offsetGet($sPropName) {
        return $this->getProp ( $sPropName );
    }
    
    /**
     * 实现 ArrayAccess::offsetUnset
     *
     * @param string $sPropName            
     * @return mixed
     */
    public function offsetUnset($sPropName) {
        $this->deleteProp ( $sPropName );
    }
    
    /**
     * 查询方式
     *
     * @param string $sMethod            
     * @param array $arrArgs            
     * @return boolean
     */
    public function __call($sMethod, $arrArgs) {
        if ($this->placeholderFlowControl ( $sMethod )) {
            return $this;
        }
        
        return call_user_func_array ( [ 
                $this->meta ()->getSelect ()->asClass ( get_called_class () )->asCollection (),
                $sMethod 
        ], $arrArgs );
    }
    
    /**
     * 查询方式
     *
     * @param string $sMethod            
     * @param array $arrArgs            
     * @return boolean
     */
    public static function __callStatic($sMethod, $arrArgs) {
        return call_user_func_array ( [ 
                new static (),
                $sMethod 
        ], $arrArgs );
    }
    
    /**
     * 将模型转化为数组
     *
     * @return string
     */
    public function __toString() {
        return $this->toJson ();
    }
}
