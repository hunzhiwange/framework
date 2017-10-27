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

use Closure;
use Exception;
use queryyetsimple\mvc\imodel;
use queryyetsimple\support\helper;
use queryyetsimple\support\string;
use queryyetsimple\support\collection;
use queryyetsimple\mvc\model_not_found;
use queryyetsimple\mvc\relation\relation;
use queryyetsimple\database\select as database_select;

/**
 * 模型查询
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.10
 * @version 1.0
 */
class select {
    
    /**
     * 模型
     *
     * @var \queryyetsimple\mvc\imodel
     */
    protected $objModel;
    
    /**
     * 查询
     *
     * @var \queryyetsimple\database\select
     */
    protected $objSelect;
    
    /**
     * 关联预载入
     *
     * @var array
     */
    protected $arrPreLoad = [ ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\imodel $objModel            
     * @return void
     */
    public function __construct($objModel) {
        $this->objModel = $objModel;
    }
    
    /**
     * 拦截一些别名和快捷方式
     *
     * @param 方法名 $sMethod            
     * @param 参数 $arrArgs            
     * @return void|mixed
     */
    public function __call($sMethod, $arrArgs) {
        if (method_exists ( $this->objSelect, $sMethod )) {
            $mixResult = call_user_func_array ( [ 
                    $this->objSelect,
                    $sMethod 
            ], $arrArgs );
            
            $mixResult = $this->preLoadResult ( $mixResult );
            
            return $mixResult;
        }
        
        throw new Exception ( __ ( 'select 没有实现魔法方法 %s.', $sMethod ) );
    }
    
    /**
     * 获取模型
     *
     * @return \queryyetsimple\mvc\imodel
     */
    public function getModel($objModel) {
        return $this->objModel;
    }
    
    /**
     * 占位符返回本对象
     *
     * @return $this
     */
    public function querySelelct() {
        return $this;
    }
    
    /**
     * 注册查询
     *
     * @param \queryyetsimple\database\select $objSelect            
     * @return void
     */
    public function registerSelect(database_select $objSelect) {
        $this->objSelect = $objSelect;
        return $this;
    }
    
    /**
     * 添加预载入的关联
     *
     * @param mixed $mixRelation            
     * @return $this
     */
    public function with($mixRelation) {
        if (is_string ( $mixRelation )) {
            $mixRelation = func_get_args ();
        }
        $this->arrPreLoad = array_merge ( $this->arrPreLoad, $this->parseWithRelation ( $mixRelation ) );
        return $this;
    }
    
    /**
     * 尝试解析结果预载
     *
     * @param mixed $mixResult            
     * @return mixed
     */
    public function preLoadResult($mixResult) {
        list ( $mixResult, $strType ) = $this->conversionToModels ( $mixResult );
        
        if (is_array ( $mixResult )) {
            $mixResult = $this->preLoadRelation ( $mixResult );
            if ($strType == 'model') {
                $mixResult = reset ( $mixResult );
            } elseif ($strType == 'collection') {
                $mixResult = new collection ( $mixResult );
            }
        }
        
        return $mixResult;
    }
    
    /**
     * 通过主键查找模型
     *
     * @param mixed $mixId            
     * @param array $arrColumn            
     * @return \queryyetsimple\mvc\imodel|\queryyetsimple\support\collection|null
     */
    public function find($mixId, $arrColumn = ['*']) {
        if (is_array ( $mixId )) {
            return $this->findMany ( $mixId, $arrColumn );
        }
        
        return $this->objSelect->where ( $this->objModel->getPrimaryKeyNameForQuery (), '=', $mixId )->setColumns ( $arrColumn )->getOne ();
    }
    
    /**
     * 根据主键查找模型
     *
     * @param array $arrId            
     * @param array $arrColumn            
     * @return \queryyetsimple\support\collection
     */
    public function findMany($arrId, $arrColumn = ['*']) {
        if (empty ( $arrId )) {
            return $this->objModel->collection ();
        }
        return $this->objSelect->whereIn ( $this->objModel->getPrimaryKeyNameForQuery (), $arrId )->setColumns ( $arrColumn )->getAll ();
    }
    
    /**
     * 通过主键查找模型，未找到则抛出异常
     *
     * @param mixed $mixId            
     * @param array $arrColumn            
     * @return \queryyetsimple\mvc\imodel|\queryyetsimple\support\collection
     */
    public function findOrFail($mixId, $arrColumn = ['*']) {
        $mixResult = $this->find ( $mixId, $arrColumn );
        
        if (is_array ( $mixId )) {
            if (count ( $mixResult ) == count ( array_unique ( $mixId ) )) {
                return $mixResult;
            }
        } elseif (! is_null ( $mixResult )) {
            return $mixResult;
        }
        
        throw (new model_not_found ())->model ( get_class ( $this->objModel ) );
    }
    
    /**
     * 通过主键查找模型，未找到初始化一个新的模型
     *
     * @param mixed $mixId            
     * @param array $arrColumn            
     * @param array $arrData            
     * @param mixed $mixConnect            
     * @param string $strTable            
     * @return \queryyetsimple\mvc\imodel
     */
    public function findOrNew($mixId, $arrColumn = ['*'], $arrData = null, $mixConnect = null, $strTable = null) {
        if (! is_null ( $objModel = $this->find ( $mixId, $arrColumn ) )) {
            return $objModel;
        }
        return $this->objModel->newInstance ( $arrData, $mixConnect ?  : $this->objModel->getConnect (), $strTable ?  : $this->objModel->getTable () );
    }
    
    /**
     * 查找第一个结果
     *
     * @param array $columns            
     * @return \queryyetsimple\mvc\imodel|static|null
     */
    public function first($arrColumn = ['*']) {
        return $this->objSelect->setColumns ( $arrColumn )->getOne ();
    }
    
    /**
     * 查找第一个结果，未找到则抛出异常
     *
     * @param array $arrColumn            
     * @return \queryyetsimple\mvc\imodel|static
     */
    public function firstOrFail($arrColumn = ['*']) {
        if (! is_null ( ($objModel = $this->first ( $arrColumn )) )) {
            return $objModel;
        }
        throw (new model_not_found ())->model ( get_class ( $this->objModel ) );
    }
    
    /**
     * 查找第一个结果，未找到则初始化一个新的模型
     *
     * @param array $arrProp            
     * @param mixed $mixConnect            
     * @param string $strTable            
     * @return \queryyetsimple\mvc\imodel
     */
    public function firstOrNew(array $arrProp, $mixConnect = null, $strTable = null) {
        if (! is_null ( ($objModel = $this->getFirstByProp ( $arrProp )) )) {
            return $objModel;
        }
        return $this->objModel->newInstance ( $arrProp, $mixConnect ?  : $this->objModel->getConnect (), $strTable ?  : $this->objModel->getTable () );
    }
    
    /**
     * 尝试根据属性查找一个模型，未找到则新建一个模型
     *
     * @param array $arrProp            
     * @param mixed $mixConnect            
     * @param string $strTable            
     * @return \queryyetsimple\mvc\imodel
     */
    public function firstOrCreate(array $arrProp, $mixConnect = null, $strTable = null) {
        if (! is_null ( ($objModel = $this->getFirstByProp ( $arrProp )) )) {
            return $objModel;
        }
        return $this->objModel->newInstance ( $arrProp, $mixConnect ?  : $this->objModel->getConnect (), $strTable ?  : $this->objModel->getTable () )->create ();
    }
    
    /**
     * 尝试根据属性查找一个模型，未找到则新建或者更新一个模型
     *
     * @param array $arrProp            
     * @param array $arrData            
     * @param mixed $mixConnect            
     * @param string $strTable            
     * @return \queryyetsimple\mvc\imodel
     */
    public function updateOrCreate(array $arrProp, array $arrData = [], $mixConnect = null, $strTable = null) {
        return $this->firstOrNew ( $arrProp, $mixConnect, $strTable )->forceProps ( $arrData )->save ();
    }
    
    /**
     * 新建一个模型
     *
     * @param array $arrProp            
     * @param mixed $mixConnect            
     * @param string $strTable            
     * @return \queryyetsimple\mvc\imodel
     */
    public function onlyCreate(array $arrProp = [], $mixConnect = null, $strTable = null) {
        return $this->objModel->newInstance ( $arrProp, $mixConnect ?  : $this->objModel->getConnect (), $strTable ?  : $this->objModel->getTable () )->save ();
    }
    
    /**
     * 从模型中软删除数据
     *
     * @return int
     */
    public function softDelete() {
        $objSelect = $this->objSelect->where ( $this->objModel->getKeyConditionForQuery () );
        $this->objModel->{$this->getDeletedAtColumn ()} = $objTime = $this->objModel->carbon ();
        $this->objModel->addDate ( $this->getDeletedAtColumn () );
        
        $this->objModel->runEvent ( model::BEFORE_SOFT_DELETE_EVENT );
        
        $intNum = $objSelect->update ( [ 
                $this->getDeletedAtColumn () => $this->objModel->fromDateTime ( $objTime ) 
        ] );
        
        $this->objModel->runEvent ( model::AFTER_SOFT_DELETE_EVENT );
        
        return $intNum;
    }
    
    /**
     * 根据主键 ID 删除模型
     *
     * @param array|int $ids            
     * @return int
     */
    public function softDestroy($mixId) {
        $intCount = 0;
        $mixId = ( array ) $mixId;
        $objInstance = $this->objModel->newInstance ();
        foreach ( $objInstance->whereIn ( $objInstance->getPrimaryKeyNameForQuery (), $mixId )->getAll () as $objModel ) {
            if ($objModel->softDelete ()) {
                $intCount ++;
            }
        }
        return $intCount;
    }
    
    /**
     * 恢复软删除的模型
     *
     * @return bool|null
     */
    public function softRestore() {
        $this->objModel->runEvent ( model::BEFORE_SOFT_RESTORE_EVENT );
        
        $this->objModel->{$this->getDeletedAtColumn ()} = null;
        $intNum = $this->objModel->update ();
        
        $this->objModel->runEvent ( model::AFTER_SOFT_RESTORE_EVENT );
        
        return $intNum;
    }
    
    /**
     * 获取不包含软删除的数据
     *
     * @return \queryyetsimple\database\select
     */
    public function withoutSoftDeleted() {
        return $this->objSelect->whereNull ( $this->getDeletedAtColumn () );
    }
    
    /**
     * 获取只包含软删除的数据
     *
     * @return \queryyetsimple\database\select
     */
    public function onlySoftDeleted() {
        return $this->objSelect->whereNotNull ( $this->getDeletedAtColumn () );
    }
    
    /**
     * 检查模型是否已经被软删除了
     *
     * @return bool
     */
    public function softDeleted() {
        return ! is_null ( $this->objModel->{$this->getDeletedAtColumn ()} );
    }
    
    /**
     * 获取软删除字段
     *
     * @return string
     */
    public function getDeletedAtColumn() {
        if (defined ( get_class ( $this->objModel ) . '::DELETED_AT' )) {
            eval ( '$strDeleteAt = ' . get_class ( $this->objModel ) . '::DELETED_AT;' );
        } else {
            $strDeleteAt = 'deleted_at';
        }
        
        if (! $this->objModel->hasField ( $strDeleteAt ))
            throw new Exception ( sprintf ( 'Model %s do not have soft delete field [%s]', get_class ( $this->objModel ), $strDeleteAt ) );
        
        return $strDeleteAt;
    }
    
    /**
     * 获取删除表加字段
     *
     * @return string
     */
    public function getFullDeletedAtColumn() {
        return $this->objModel->getTable () . '.' . $this->getDeletedAtColumn ();
    }
    
    /**
     * 查询范围
     *
     * @param mixed $mixScope            
     * @return \queryyetsimple\mvc\imodel
     */
    public function scope($mixScope /* args */) {
        if ($mixScope instanceof database_select) {
            return $mixScope;
        }
        
        $objSelect = $this->objSelect;
        
        $arrArgs = func_get_args ();
        array_shift ( $arrArgs );
        array_unshift ( $arrArgs, $objSelect );
        
        if ($mixScope instanceof Closure) {
            $mixResultCallback = call_user_func_array ( $mixScope, $arrArgs );
            if ($mixResultCallback instanceof database_select) {
                $objSelect = $mixResultCallback;
            }
            unset ( $mixResultCallback );
            $this->objModel->setSelectForQuery ( $objSelect );
        } else {
            foreach ( helper::arrays ( $mixScope ) as $strScope ) {
                $strScope = 'scope' . ucwords ( $strScope );
                if (method_exists ( $this->objModel, $strScope )) {
                    $mixResultCallback = call_user_func_array ( [ 
                            $this->objModel,
                            $strScope 
                    ], $arrArgs );
                    if ($mixResultCallback instanceof database_select) {
                        $objSelect = $mixResultCallback;
                    }
                    unset ( $mixResultCallback );
                    $this->objModel->setSelectForQuery ( $objSelect );
                }
            }
        }
        
        unset ( $objSelect, $arrArgs, $mixScope );
        return $this->objModel;
    }
    
    /**
     * 预载入模型
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel            
     * @return array
     */
    protected function preLoadRelation(array $arrModel) {
        foreach ( $this->arrPreLoad as $strName => $calCondition ) {
            if (strpos ( $strName, '.' ) === false) {
                $arrModel = $this->loadRelation ( $arrModel, $strName, $calCondition );
            }
        }
        return $arrModel;
    }
    
    /**
     * 取得关联模型
     *
     * @param string $name            
     * @return \queryyetsimple\mvc\relation\relation
     */
    protected function getRelation($strName) {
        $objRelation = relation::withoutRelationCondition ( function () use($strName) {
            return $this->objModel->$strName ();
        } );
        
        $arrNested = $this->nestedRelation ( $strName );
        if (count ( $arrNested ) > 0) {
            $objRelation->getSelect ()->with ( $arrNested );
        }
        
        return $objRelation;
    }
    
    /**
     * 尝试取得嵌套关联
     *
     * @param string $strRelation            
     * @return array
     */
    protected function nestedRelation($strRelation) {
        $arrNested = [ ];
        
        foreach ( $this->arrPreLoad as $strName => $calCondition ) {
            if ($this->isNested ( $strName, $strRelation )) {
                $arrNested [substr ( $strName, strlen ( $strRelation . '.' ) )] = $calCondition;
            }
        }
        
        return $arrNested;
    }
    
    /**
     * 判断是否存在嵌套关联
     *
     * @param string $strName            
     * @param string $strRelation            
     * @return bool
     */
    protected function isNested($strName, $strRelation) {
        return string::contains ( $strName, '.' ) && string::startsWith ( $strName, $strRelation . '.' );
    }
    
    /**
     * 格式化预载入关联
     *
     * @param array $arrRelation            
     * @return array
     */
    protected function parseWithRelation(array $arrRelation) {
        $arr = [ ];
        
        foreach ( $arrRelation as $mixName => $mixCondition ) {
            if (is_numeric ( $mixName )) {
                list ( $mixName, $mixCondition ) = [ 
                        $mixCondition,
                        function () {
                        } 
                ];
            }
            
            $arr = $this->parseNestedWith ( $mixName, $arr );
            $arr [$mixName] = $mixCondition;
        }
        
        return $arr;
    }
    
    /**
     * 解析嵌套关联
     *
     * @param string $strName            
     * @param array $arrResult            
     * @return array
     */
    protected function parseNestedWith($strName, array $arrResult) {
        $arrProgress = [ ];
        
        foreach ( explode ( '.', $strName ) as $strSegment ) {
            $arrProgress [] = $strSegment;
            if (! isset ( $arrResult [$strLast = implode ( '.', $arrProgress )] )) {
                $arrResult [$strLast] = function () {
                };
            }
        }
        
        return $arrResult;
    }
    
    /**
     * 转换结果到模型类型
     *
     * @param mixed $mixResult            
     * @return array
     */
    protected function conversionToModels($mixResult) {
        $strType = '';
        
        if ($mixResult instanceof collection) {
            $arr = [ ];
            foreach ( $mixResult as $objModel ) {
                $arr [] = $objModel;
            }
            $mixResult = $arr;
            $strType = 'collection';
        } elseif ($mixResult instanceof imodel) {
            $mixResult = [ 
                    $mixResult 
            ];
            $strType = 'model';
        }
        
        return [ 
                $mixResult,
                $strType 
        ];
    }
    
    /**
     * 关联数据设置到模型上
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel            
     * @param string $strName            
     * @param callable $calCondition            
     * @return array
     */
    protected function loadRelation(array $arrModel, $strName, callable $calCondition) {
        $objRelation = $this->getRelation ( $strName );
        $objRelation->preLoadCondition ( $arrModel );
        call_user_func ( $calCondition, $objRelation );
        return $objRelation->matchPreLoad ( $arrModel, $objRelation->getPreLoad (), $strName );
    }
    
    /**
     * 尝试根据属性查找一个模型
     *
     * @param array $arrProp            
     * @return \queryyetsimple\mvc\imodel|null
     */
    protected function getFirstByProp(array $arrProp) {
        if (! is_null ( $objModel = $this->objSelect->where ( $arrProp )->getOne () )) {
            return $objModel;
        }
        return null;
    }
}
