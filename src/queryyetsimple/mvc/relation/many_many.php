<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc\relation;

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

use queryyetsimple\mvc\interfaces\model;
use queryyetsimple\collection\collection;

/**
 * 关联模型 many_many
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
class many_many extends relation {
    
    /**
     * 中间表查询对象
     *
     * @var \queryyetsimple\database\select
     */
    protected $objMiddleSelect;
    
    /**
     * 中间表模型
     *
     * @var \queryyetsimple\mvc\interfaces\model
     */
    protected $objMiddleModel;
    
    /**
     * 目标中间表关联字段
     *
     * @var string
     */
    protected $strMiddleTargetKey;
    
    /**
     * 源中间表关联字段
     *
     * @var string
     */
    protected $strMiddleSourceKey;
    
    /**
     * 中间表隐射数据
     *
     * @var array
     */
    protected $arrMiddleMap = [ ];
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\interfaces\model $objTargetModel            
     * @param \queryyetsimple\mvc\interfaces\model $objSourceModel            
     * @param \queryyetsimple\mvc\interfaces\model $objMiddleModel            
     * @param string $strTargetKey            
     * @param string $strSourceKey            
     * @param string $strMiddleTargetKey            
     * @param string $strMiddleSourceKey            
     * @return void
     */
    public function __construct(model $objTargetModel, model $objSourceModel, model $objMiddleModel, $strTargetKey, $strSourceKey, $strMiddleTargetKey, $strMiddleSourceKey) {
        $this->objMiddleModel = $objMiddleModel;
        $this->strMiddleTargetKey = $strMiddleTargetKey;
        $this->strMiddleSourceKey = $strMiddleSourceKey;
        
        parent::__construct ( $objTargetModel, $objSourceModel, $strTargetKey, $strSourceKey );
    }
    
    /**
     * 关联基础查询条件
     *
     * @return void
     */
    public function addRelationCondition() {
        if (static::$booRelationCondition) {
            $this->objMiddleSelect = $this->objMiddleModel->where ( $this->strMiddleSourceKey, $this->getSourceValue () );
        }
    }
    
    /**
     * 设置预载入关联查询条件
     *
     * @param \queryyetsimple\mvc\interfaces\model[] $arrModel            
     * @return void
     */
    public function preLoadCondition(array $arrModel) {
        $this->preLoadRelationCondition ( $arrModel );
        $this->parseSelectCondition ();
    }
    
    /**
     * 匹配关联查询数据到模型
     *
     * @param \queryyetsimple\mvc\interfaces\model[] $arrModel            
     * @param \queryyetsimple\collection\collection $objResult            
     * @param string $strRelation            
     * @return array
     */
    public function matchPreLoad(array $arrModel, collection $objResult, $strRelation) {
        $arrMap = $this->buildMap ( $objResult );
        
        foreach ( $arrModel as &$objModel ) {
            $mixKey = $objModel->getProp ( $this->strSourceKey );
            if (isset ( $arrMap [$mixKey] )) {
                $objModel->setRelationProp ( $strRelation, $this->objTargetModel->collection ( $arrMap [$mixKey] ) );
            }
        }
        
        return $arrModel;
    }
    
    /**
     * 中间表查询回调处理
     *
     * @param callable $calCallback            
     * @return $this
     */
    public function middleCondition($calCallback) {
        call_user_func_array ( $calCallback, [ 
                $this->objMiddleSelect,
                $this 
        ] );
        
        return $this;
    }
    
    /**
     * 取回源模型对应数据
     *
     * @return mixed
     */
    public function getSourceValue() {
        return $this->objSourceModel->getProp ( $this->strSourceKey );
    }
    
    /**
     * 查询关联对象
     *
     * @return mixed
     */
    public function sourceQuery() {
        if ($this->parseSelectCondition () === false) {
            return new collection ();
        } else {
            return $this->objSelect->getAll ();
        }
    }
    
    /**
     * 取得中间表查询对象
     *
     * @return \queryyetsimple\database\select
     */
    public function getMiddleSelect() {
        return $this->objMiddleSelect;
    }
    
    /**
     * 取得中间表模型
     *
     * @return \queryyetsimple\mvc\interfaces\model
     */
    public function getMiddleModel() {
        return $this->objMiddleModel;
    }
    
    /**
     * 取得目标中间表关联字段
     *
     * @return string
     */
    public function getTargetKey() {
        return $this->strMiddleTargetKey;
    }
    
    /**
     * 取得源中间表关联字段
     *
     * @return string
     */
    public function getSourceKey() {
        return $this->strMiddleSourceKey;
    }
    
    /**
     * 取得中间表隐射数据
     *
     * @return array
     */
    public function getMiddleMap() {
        return $this->arrMiddleMap;
    }
    
    /**
     * 取得源外键值
     *
     * @return mixed
     */
    public function getSourceKeyValue() {
        return $this->objSourceModel->getProp ( $this->strSourceKey );
    }
    
    /**
     * 预载入关联基础查询条件
     *
     * @return void
     */
    protected function preLoadRelationCondition(array $arrModel) {
        $this->objMiddleSelect = $this->objMiddleModel->whereIn ( $this->strMiddleSourceKey, $this->getPreLoadSourceValue ( $arrModel ) );
    }
    
    /**
     * 取回源模型对应数据
     *
     * @return mixed
     */
    protected function getPreLoadSourceValue(array $arrModel) {
        $arr = [ ];
        
        foreach ( $arrModel as $objSourceModel ) {
            $arr [] = $objSourceModel->{$this->strSourceKey};
        }
        
        return $arr;
    }
    
    /**
     * 模型隐射数据
     *
     * @param \queryyetsimple\collection\collection $objResult            
     * @return array
     */
    protected function buildMap(collection $objResult) {
        $arrMap = [ ];
        
        foreach ( $objResult as $objResultModel ) {
            $mixKey = $objResultModel->getProp ( $this->strTargetKey );
            if (isset ( $this->arrMiddleMap [$mixKey] )) {
                foreach ( $this->arrMiddleMap [$mixKey] as $mixValue ) {
                    $arrMap [$mixValue] [] = $objResultModel;
                }
            }
        }
        
        return $arrMap;
    }
    
    /**
     * 通过中间表获取目标 ID
     *
     * @return array
     */
    protected function parseSelectCondition() {
        $arrTargetId = $this->parseTargetId ();
        
        $this->objSelect->whereIn ( $this->strTargetKey, $arrTargetId ?  : [ 
                0 
        ] );
        
        if (! $arrTargetId) {
            return false;
        }
    }
    
    /**
     * 通过中间表获取目标 ID
     *
     * @return array
     */
    protected function parseTargetId() {
        $arr = $arrTargetId = [ ];
        
        foreach ( $this->objMiddleSelect->getAll () as $objMiddleModel ) {
            $arr [$objMiddleModel->{$this->strMiddleTargetKey}] [] = $objMiddleModel->{$this->strMiddleSourceKey};
            $arrTargetId [] = $objMiddleModel->{$this->strMiddleTargetKey};
        }
        
        $this->arrMiddleMap = $arr;
        
        return array_unique ( $arrTargetId );
    }
}
