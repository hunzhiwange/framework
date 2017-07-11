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
use Carbon\Carbon;
use JsonSerializable;
use DateTimeInterface;
use queryyetsimple\flow\control;
use queryyetsimple\string\string;
use queryyetsimple\classs\serialize;
use queryyetsimple\collection\collection;
use queryyetsimple\support\interfaces\arrayable;
use queryyetsimple\mvc\interfaces\model as interfaces_model;

/**
 * 模型 Object Relational Mapping
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.27
 * @version 1.0
 */
class model implements interfaces_model, JsonSerializable, ArrayAccess, arrayable {
    
    use control;
    use serialize;
    
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $strTable;
    
    /**
     * 此模型的连接名称
     *
     * @var mixed
     */
    protected $mixConnect;
    
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
    protected $arrChangedProp = [ ];
    
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
    protected $booForceProp = false;
    
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
    
    /**
     * 转换隐藏的属性
     *
     * @var array
     */
    protected $arrHidden = [ ];
    
    /**
     * 转换显示的属性
     *
     * @var array
     */
    protected $arrVisible = [ ];
    
    /**
     * 追加
     *
     * @var array
     */
    protected $arrAppend = [ ];
    
    /**
     * 模型的日期字段保存格式
     *
     * @var string
     */
    protected $strDateFormat = 'Y-m-d H:i:s';
    
    /**
     * 应被转换为日期的属性
     *
     * @var array
     */
    protected $arrDate = [ ];
    
    /**
     * 开启默认时间属性转换
     *
     * @var array
     */
    protected $booTimestamp = true;
    
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
                $this->props ( $arrData );
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
        return $this->saves ( 'create', $arrData );
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
        if ($this->checkFlowControl ())
            return $this;
        
        if (is_array ( $arrData ) && $arrData) {
            $this->propForces ( $arrData );
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
     * 根据主键 ID 删除模型
     *
     * @param array|int $ids            
     * @return int
     */
    public function destroy($mixId) {
        $intCount = 0;
        $mixId = ( array ) $mixId;
        $objInstance = new static ();
        foreach ( $objInstance->whereIn ( $objInstance->getPrimaryKeyNameForQuery (), $mixId )->getAll () as $objModel ) {
            if ($objModel->delete ()) {
                $intCount ++;
            }
        }
        return $intCount;
    }
    
    /**
     * 删除模型
     *
     * @return bool|null
     */
    public function delete() {
        if (is_null ( $this->getPrimaryKeyName () )) {
            throw new Exception ( sprintf ( 'Model %s has no primary key', $this->getCalledClass () ) );
        }
        return $this->deleteModelByKey ();
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
                if (! in_array ( $sPrimaryKey, $this->arrChangedProp )) {
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
     * < update 调用无效，请换用 forceProp >
     *
     * @param mixed $mixProp            
     * @param mixed $mixValue            
     * @return $this
     */
    public function prop($strProp, $mixValue) {
        if ($this->checkFlowControl ())
            return $this;
        
        $mixValue = $this->meta ()->fieldsProp ( $strProp, $mixValue );
        
        if (is_null ( $mixValue ) && ($strCamelize = 'set' . ucwords ( string::camelize ( $strProp ) ) . 'Prop') && method_exists ( $this, $strCamelize )) {
            if (is_null ( ($mixValue = $this->$strCamelize ( $this->getProp ( $strProp ) )) ))
                $mixValue = $this->getProp ( $strProp );
        } 

        elseif ($mixValue && (in_array ( $strProp, $this->getDate () ) || $this->isDateConversion ( $strProp ))) {
            $mixValue = $this->fromDateTime ( $mixValue );
        } 

        elseif ($this->isJsonConversion ( $strProp ) && ! is_null ( $mixValue )) {
            $mixValue = $this->asJson ( $mixValue );
        }
        
        $this->arrProp [$strProp] = $mixValue;
        if ($this->getForceProp () && ! in_array ( $strProp, $this->arrReadonly ) && ! in_array ( $strProp, $this->arrChangedProp )) {
            $this->arrChangedProp [] = $strProp;
        }
        
        return $this;
    }
    
    /**
     * 批量强制改变属性
     *
     * < update 调用无效，请换用 propForces >
     *
     * @param array $arrProp            
     * @return $this
     */
    public function props(array $arrProp) {
        if ($this->checkFlowControl ())
            return $this;
        foreach ( $arrProp as $strProp => $mixValue ) {
            $this->prop ( $strProp, $mixValue );
        }
        return $this;
    }
    
    /**
     * 强制改变属性
     *
     * @param mixed $strPropName            
     * @param mixed $mixValue            
     * @return $this
     */
    public function forceProp($strPropName, $mixValue) {
        if ($this->checkFlowControl ())
            return $this;
        
        $this->setForceProp ( true );
        call_user_func_array ( [ 
                $this,
                'prop' 
        ], [ 
                $strPropName,
                $mixValue 
        ] );
        $this->setForceProp ( false );
        return $this;
    }
    
    /**
     * 批量强制改变属性
     *
     * @param array $arrProp            
     * @return $this
     */
    public function forceProps(array $arrProp) {
        if ($this->checkFlowControl ())
            return $this;
        
        $this->setForceProp ( true );
        call_user_func_array ( [ 
                $this,
                'props' 
        ], [ 
                $arrProp 
        ] );
        $this->setForceProp ( false );
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
            $mixValue = null;
        else
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
        if ($this->checkFlowControl ())
            return $this;
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
        if ($this->checkFlowControl ())
            return $this;
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
     * 返回主键字段
     *
     * @return array|string|null
     */
    public function getPrimaryKeyName() {
        $arrKey = $this->getPrimaryKeyNameSource ();
        return count ( $arrKey ) == 1 ? reset ( $arrKey ) : $arrKey;
    }
    
    /**
     * 返回主键字段
     *
     * @return array|null
     */
    public function getPrimaryKeyNameSource() {
        return $this->meta ()->getPrimaryKey ();
    }
    
    /**
     * 返回属性映射字段
     *
     * @return array
     */
    public function getPropField() {
        return $this->meta ()->getPropField ();
    }
    
    /**
     * 返回字段映射属性
     *
     * @return array
     */
    public function getFieldProp() {
        return $this->meta ()->getFieldProp ();
    }
    
    /**
     * 是否存在字段
     *
     * @param string $strFiled            
     * @return array
     */
    public function hasField($strField) {
        return in_array ( $strField, $this->getPropField () );
    }
    
    /**
     * 返回供查询的主键字段
     * 复合主键或者没有主键直接抛出异常
     *
     * @return string|void
     */
    public function getPrimaryKeyNameForQuery() {
        $mixKey = $this->getPrimaryKeyName ();
        if (! is_string ( $mixKey ))
            throw new Exception ( sprintf ( 'Model %s do not have primary key or composite id not supported', $this->getCalledClass () ) );
        return $mixKey;
    }
    
    /**
     * 设置表
     *
     * @param string $strTable            
     * @return $this
     */
    public function table($strTable) {
        if ($this->checkFlowControl ())
            return $this;
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
        if ($this->checkFlowControl ())
            return $this;
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
        if ($this->checkFlowControl ())
            return $this;
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
     * 设置转换隐藏属性
     *
     * @param array $arrHidden            
     * @return $this
     */
    public function hidden(array $arrHidden) {
        if ($this->checkFlowControl ())
            return $this;
        $this->arrHidden = $arrHidden;
        return $this;
    }
    
    /**
     * 获取转换隐藏属性
     *
     * @return array
     */
    public function getHidden() {
        return $this->arrHidden;
    }
    
    /**
     * 添加转换隐藏属性
     *
     * @param array|string $mixProp            
     * @return $this
     */
    public function addHidden($mixProp) {
        if ($this->checkFlowControl ())
            return $this;
        $mixProp = is_array ( $mixProp ) ? $mixProp : func_get_args ();
        $this->arrHidden = array_merge ( $this->arrHidden, $mixProp );
        return $this;
    }
    
    /**
     * 设置转换显示属性
     *
     * @param array $arrVisible            
     * @return $this
     */
    public function visible(array $arrVisible) {
        if ($this->checkFlowControl ())
            return $this;
        $this->arrVisible = $arrVisible;
        return $this;
    }
    
    /**
     * 获取转换显示属性
     *
     * @return array
     */
    public function getVisible() {
        return $this->arrVisible;
    }
    
    /**
     * 添加转换显示属性
     *
     * @param array|string $mixProp            
     * @return $this
     */
    public function addVisible($mixProp) {
        if ($this->checkFlowControl ())
            return $this;
        $mixProp = is_array ( $mixProp ) ? $mixProp : func_get_args ();
        $this->arrVisible = array_merge ( $this->arrVisible, $mixProp );
        return $this;
    }
    
    /**
     * 设置转换追加属性
     *
     * @param array $arrAppend            
     * @return $this
     */
    public function append(array $arrAppend) {
        if ($this->checkFlowControl ())
            return $this;
        $this->arrAppend = $arrAppend;
        return $this;
    }
    
    /**
     * 获取转换追加属性
     *
     * @return array
     */
    public function getAppend() {
        return $this->arrAppend;
    }
    
    /**
     * 添加转换追加属性
     *
     * @param array|string|null $mixProp            
     * @return $this
     */
    public function addAppend($mixProp = null) {
        if ($this->checkFlowControl ())
            return $this;
        $mixProp = is_array ( $mixProp ) ? $mixProp : func_get_args ();
        $this->arrAppend = array_merge ( $this->arrAppend, $mixProp );
        return $this;
    }
    
    /**
     * 是否自动填充数据
     *
     * @param boolean $booAutoFill            
     * @return $this
     */
    public function autoFill($booAutoFill = true) {
        if ($this->checkFlowControl ())
            return $this;
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
     * 设置模型时间格式化
     *
     * @param string $strDateFormat            
     * @return $this
     */
    public function setDateFormat($strDateFormat) {
        if ($this->checkFlowControl ())
            return $this;
        $this->strDateFormat = $strDateFormat;
        return $this;
    }
    
    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray() {
        if (! empty ( $this->arrHidden )) {
            $arrProp = array_intersect_key ( $this->arrProp, array_flip ( $this->arrVisible ) );
        } elseif (! empty ( $this->arrHidden )) {
            $arrProp = array_diff_key ( $this->arrProp, array_flip ( $this->arrHidden ) );
        } else {
            $arrProp = $this->arrProp;
        }
        
        $arrProp = array_merge ( $arrProp, $this->arrAppend ? array_flip ( $this->arrAppend ) : [ ] );
        foreach ( $arrProp as $strProp => &$mixValue ) {
            $mixValue = $this->getProp ( $strProp );
        }
        
        return $arrProp;
    }
    
    /**
     * 创建一个 Carbon 时间对象
     *
     * @return \Carbon\Carbon
     */
    public function carbon() {
        return new Carbon ();
    }
    
    /**
     * 取得新建时间字段
     *
     * @return string
     */
    public function getCreatedAtColumn() {
        return static::CREATED_AT;
    }
    
    /**
     * 取得更新时间字段
     *
     * @return string
     */
    public function getUpdatedAtColumn() {
        return static::UPDATED_AT;
    }
    
    /**
     * 获取需要转换为时间的属性
     *
     * @return array
     */
    public function getDate() {
        return $this->booTimestamp ? array_merge ( $this->arrDate, [ 
                static::CREATED_AT,
                static::UPDATED_AT 
        ] ) : $this->arrDate;
    }
    
    /**
     * 设置需要转换时间的属性
     *
     * @param array $arrDate            
     * @return $this
     */
    public function date(array $arrDate) {
        if ($this->checkFlowControl ())
            return $this;
        $this->arrDate = $arrDate;
        return $this;
    }
    
    /**
     * 添加需要转换时间的属性
     *
     * @param array|string $mixProp            
     * @return $this
     */
    public function addDate($mixProp) {
        if ($this->checkFlowControl ())
            return $this;
        $mixProp = is_array ( $mixProp ) ? $mixProp : func_get_args ();
        $this->arrDate = array_merge ( $this->arrDate, $mixProp );
        return $this;
    }
    
    /**
     * 是否使用默认时间
     *
     * @return bool
     */
    public function getTimestamp() {
        return $this->booTimestamp;
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
     * 创建一个模型集合
     *
     * @param array $arrProp            
     * @return \queryyetsimple\collection\collection
     */
    public function collection(array $arrProp = []) {
        return new collection ( $arrProp );
    }
    
    /**
     * 创建新的应用程序
     *
     * @param array $arrProp            
     * @return static
     */
    public function newInstance($arrProp = []) {
        return new static ( ( array ) $arrProp );
    }
    
    /**
     * 将时间转化为数据库存储的值
     *
     * @param \DateTime|int $mixValue            
     * @return string
     */
    public function fromDateTime($mixValue) {
        return $this->asDateTime ( $mixValue )->format ( $this->getDateFormat () );
    }
    
    /**
     * 获取查询键值
     *
     * @return array|void
     */
    public function getKeyConditionForQuery() {
        if (is_null ( ($arrPrimaryData = $this->primaryKey ()) )) {
            throw new Exception ( sprintf ( 'Model %s has no primary key data', $this->getCalledClass () ) );
        }
        
        if (! is_array ( $arrPrimaryData )) {
            $arrPrimaryData = [ 
                    $this->getPrimaryKeyNameForQuery () => $arrPrimaryData 
            ];
        }
        
        return $arrPrimaryData;
    }
    
    /**
     * 返回模型类的 meta 对象
     *
     * @return Meta
     */
    public function meta() {
        if (! $this->strTable) {
            $strTable = $this->getCalledClass ();
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
        
        if (! $arrSaveData) {
            if (is_null ( ($arrPrimaryKey = $this->getPrimaryKeyNameSource ()) ))
                throw new Exception ( sprintf ( 'Model %s has no primary key', $this->getCalledClass () ) );
            
            foreach ( $arrPrimaryKey as $strPrimaryKey ) {
                $arrSaveData [$strPrimaryKey] = null;
            }
        }
        
        $arrLastInsertId = $this->meta ()->insert ( $arrSaveData );
        $this->arrProp = array_merge ( $this->arrProp, $arrLastInsertId );
        
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
            if (! in_array ( $sPropName, $this->arrChangedProp )) {
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
            $arrCondition = [ ];
            foreach ( $this->getPrimaryKeyNameSource () as $sFieldName ) {
                if (isset ( $arrSaveData [$sFieldName] )) {
                    unset ( $arrSaveData [$sFieldName] );
                }
                if (! empty ( $this->arrProp [$sFieldName] )) {
                    $arrCondition [$sFieldName] = $this->arrProp [$sFieldName];
                }
            }
            
            if (! empty ( $arrSaveData ) && ! empty ( $arrCondition )) {
                $intNum = $this->meta ()->update ( $arrCondition, $arrSaveData );
            }
        }
        
        $this->clearChanged ();
        
        return isset ( $intNum ) ? $intNum : 0;
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
            if (! in_array ( $strField, $this->arrChangedProp )) {
                $this->arrProp [$strField] = trim ( $mixValue );
                $this->arrChangedProp [] = $strField;
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
            $this->forceProp ( $mixKey, $mixValue );
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
     * 属性是否可以被转换为属性
     *
     * @param string $strProp            
     * @return bool
     */
    protected function isDateConversion($strProp) {
        return $this->hasConversion ( $strProp, [ 
                'date',
                'datetime' 
        ] );
    }
    
    /**
     * 属性是否可以转换为 JSON
     *
     * @param string $strProp            
     * @return bool
     */
    protected function isJsonConversion($strProp) {
        return $this->hasConversion ( $strProp, [ 
                'array',
                'json',
                'object',
                'collection' 
        ] );
    }
    
    /**
     * 将变量转为 JSON
     *
     * @param mixed $mixValue            
     * @return string
     */
    protected function asJson($mixValue) {
        return json_encode ( $mixValue );
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
            case 'date' :
            case 'datetime' :
                return $this->asDateTime ( $mixValue );
            case 'timestamp' :
                return $this->asTimeStamp ( $mixValue );
            default :
                return $mixValue;
        }
    }
    
    /**
     * 设置是否处于强制更新属性的
     *
     * @param boolean $booForceProp            
     * @return boolean
     */
    protected function setForceProp($booForceProp = true) {
        $this->booForceProp = $booForceProp;
    }
    
    /**
     * 返回是否处于强制更新属性的
     *
     * @return boolean
     */
    protected function getForceProp() {
        return $this->booForceProp;
    }
    
    /**
     * 返回数据库查询集合对象
     *
     * @return \queryyetsimple\database\interfaces\connect
     */
    protected function getClassCollectionQuery() {
        return $this->getQuery ()->asClass ( $this->getCalledClass () )->asCollection ()->registerCallSelect ( new select ( $this ) );
    }
    
    /**
     * 返回数据库查询对象
     *
     * @return \queryyetsimple\database\interfaces\connect
     */
    protected function getQuery() {
        return $this->meta ()->getSelect ();
    }
    
    /**
     * 转换为时间对象
     *
     * @param mixed $mixValue            
     * @return \Carbon\Carbon
     */
    protected function asDateTime($mixValue) {
        if ($mixValue instanceof Carbon) {
            return $mixValue;
        }
        
        if ($mixValue instanceof DateTimeInterface) {
            return new Carbon ( $mixValue->format ( 'Y-m-d H:i:s.u' ), $mixValue->getTimeZone () );
        }
        
        if (is_numeric ( $mixValue )) {
            return Carbon::createFromTimestamp ( $mixValue );
        }
        
        if (preg_match ( '/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $mixValue )) {
            return Carbon::createFromFormat ( 'Y-m-d', $mixValue )->startOfDay ();
        }
        
        return Carbon::createFromFormat ( $this->getDateFormat (), $mixValue );
    }
    
    /**
     * 转为 unix 时间风格
     *
     * @param mixed $mixValue            
     * @return int
     */
    protected function asTimeStamp($mixValue) {
        return $this->asDateTime ( $mixValue )->getTimestamp ();
    }
    
    /**
     * 返回属性时间格式化
     *
     * @return string
     */
    protected function getDateFormat() {
        return $this->strDateFormat ?  : 'Y-m-d H:i:s';
    }
    
    /**
     * 删除模型
     *
     * @return int
     */
    protected function deleteModelByKey() {
        return $this->getQuery ()->where ( $this->getKeyConditionForQuery () )->delete ();
    }
    
    /**
     * 获取调用 class
     *
     * @return string
     */
    protected function getCalledClass() {
        return get_called_class ();
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
        return $this->forceProp ( $sPropName, $mixValue );
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
        return $this->forceProp ( $sPropName, $mixValue );
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
     * @return $this
     */
    public function offsetUnset($sPropName) {
        return $this->deleteProp ( $sPropName );
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
                $this->getClassCollectionQuery (),
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
