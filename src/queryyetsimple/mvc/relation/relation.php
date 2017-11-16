<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 * 
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\mvc\relation;

use Closure;
use Exception;
use queryyetsimple\mvc\imodel;
use queryyetsimple\support\collection;

/**
 * 关联模型基类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
abstract class relation
{
    
    /**
     * 查询对象
     *
     * @var \queryyetsimple\database\select
     */
    protected $objSelect;
    
    /**
     * 关联目标模型
     *
     * @var \queryyetsimple\mvc\imodel
     */
    protected $objTargetModel;
    
    /**
     * 源模型
     *
     * @var \queryyetsimple\mvc\imodel
     */
    protected $objSourceModel;
    
    /**
     * 目标关联字段
     *
     * @var string
     */
    protected $strTargetKey;
    
    /**
     * 源关联字段
     *
     * @var string
     */
    protected $strSourceKey;
    
    /**
     * 是否初始化查询
     *
     * @var boolean
     */
    protected static $booRelationCondition = true;
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\imodel $objTargetModel
     * @param \queryyetsimple\mvc\imodel $objSourceModel
     * @param string $strTargetKey
     * @param string $strSourceKey
     * @return void
     */
    public function __construct(imodel $objTargetModel, imodel $objSourceModel, $strTargetKey, $strSourceKey)
    {
        $this->objTargetModel = $objTargetModel;
        $this->objSourceModel = $objSourceModel;
        $this->strTargetKey = $strTargetKey;
        $this->strSourceKey = $strSourceKey;
        
        $this->getSelectFromModel();
        $this->addRelationCondition();
    }
    
    /**
     * 返回查询
     *
     * @return \queryyetsimple\database\select
     */
    public function getSelect()
    {
        return $this->objSelect;
    }
    
    /**
     * 取得预载入关联模型
     *
     * @return \queryyetsimple\support\collection
     */
    public function getPreLoad()
    {
        return $this->querySelelct()->preLoadResult($this->getAll());
    }
    
    /**
     * 取得关联目标模型
     *
     * @return \queryyetsimple\mvc\imodel
     */
    public function getTargetModel()
    {
        return $this->objTargetModel;
    }
    
    /**
     * 取得源模型
     *
     * @return \queryyetsimple\mvc\imodel
     */
    public function getSourceModel()
    {
        return $this->objSourceModel;
    }
    
    /**
     * 取得目标字段
     *
     * @return string
     */
    public function getTargetKey()
    {
        return $this->strTargetKey;
    }
    
    /**
     * 取得源字段
     *
     * @return string
     */
    public function getSourceKey()
    {
        return $this->strSourceKey;
    }
    
    /**
     * 获取不带关联条件的关联对象
     *
     * @param \Closure $calReturnRelation
     * @return \queryyetsimple\mvc\relation\relation
     */
    public static function withoutRelationCondition(Closure $calReturnRelation)
    {
        $booOld = static::$booRelationCondition;
        static::$booRelationCondition = false;
        
        $objRelation = call_user_func($calReturnRelation);
        if (! ($objRelation instanceof relation)) {
            throw new Exception('The result must be relation');
        }
        
        static::$booRelationCondition = $booOld;
        return $objRelation;
    }
    
    /**
     * 关联基础查询条件
     *
     * @return void
     */
    abstract public function addRelationCondition();
    
    /**
     * 设置预载入关联查询条件
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @return void
     */
    abstract public function preLoadCondition(array $arrModel);
    
    /**
     * 匹配关联查询数据到模型 has_many
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @param \queryyetsimple\support\collection $objResult
     * @param string $strRelation
     * @return array
     */
    abstract public function matchPreLoad(array $arrModel, collection $objResult, $strRelation);
    
    /**
     * 查询关联对象
     *
     * @return mixed
     */
    abstract public function sourceQuery();
    
    /**
     * 返回模型的主键
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @param string $strKey
     * @return array
     */
    protected function getModelKey(array $arrModel, $strKey = null)
    {
        return array_unique(array_values(array_map(function ($objModel) use($strKey)
        {
            return $strKey ? $objModel->getProp($strKey) : $objModel->getPrimaryKeyForQuery();
        }, $arrModel)));
    }
    
    /**
     * 从模型返回查询
     *
     * @return \queryyetsimple\database\select
     */
    protected function getSelectFromModel()
    {
        $this->objSelect = $this->objTargetModel->getClassCollectionQuery();
    }
    
    /**
     * 缺省方法
     *
     * @param 方法名 $sMethod
     * @param 参数 $arrArgs
     * @return mixed
     */
    public function __call($sMethod, $arrArgs)
    {
        $objSelect = call_user_func_array([
            $this->objSelect, 
            $sMethod
        ], $arrArgs);
        
        if ($this->getSelect() === $objSelect) {
            return $this;
        }

        return $objSelect;
    }
}
