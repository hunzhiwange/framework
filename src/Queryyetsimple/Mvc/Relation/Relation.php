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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Mvc\Relation;

use Closure;
use Exception;
use Queryyetsimple\{
    Mvc\IModel,
    Support\Collection
};

/**
 * 关联模型基类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
abstract class Relation
{

    /**
     * 查询对象
     *
     * @var \Queryyetsimple\Database\Select
     */
    protected $objSelect;

    /**
     * 关联目标模型
     *
     * @var \Queryyetsimple\Mvc\IModel
     */
    protected $objTargetModel;

    /**
     * 源模型
     *
     * @var \Queryyetsimple\Mvc\IModel
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
     * @param \Queryyetsimple\Mvc\IModel $objTargetModel
     * @param \Queryyetsimple\Mvc\IModel $objSourceModel
     * @param string $strTargetKey
     * @param string $strSourceKey
     * @return void
     */
    public function __construct(IModel $objTargetModel, IModel $objSourceModel, $strTargetKey, $strSourceKey)
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
     * @return \Queryyetsimple\Database\Select
     */
    public function getSelect()
    {
        return $this->objSelect;
    }

    /**
     * 取得预载入关联模型
     *
     * @return \queryyetsimple\Support\Collection
     */
    public function getPreLoad()
    {
        return $this->querySelelct()->preLoadResult($this->getAll());
    }

    /**
     * 取得关联目标模型
     *
     * @return \Queryyetsimple\Mvc\IModel
     */
    public function getTargetModel()
    {
        return $this->objTargetModel;
    }

    /**
     * 取得源模型
     *
     * @return \Queryyetsimple\Mvc\IModel
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
     * @return \queryyetsimple\Mvc\Relation\Relation
     */
    public static function withoutRelationCondition(Closure $calReturnRelation)
    {
        $booOld = static::$booRelationCondition;
        static::$booRelationCondition = false;

        $objRelation = call_user_func($calReturnRelation);
        if (! ($objRelation instanceof Relation)) {
            throw new Exception('The result must be relation.');
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
     * @param \Queryyetsimple\Mvc\IModel[] $arrModel
     * @return void
     */
    abstract public function preLoadCondition(array $arrModel);

    /**
     * 匹配关联查询数据到模型 HasMany
     *
     * @param \Queryyetsimple\Mvc\IModel[] $arrModel
     * @param \queryyetsimple\Support\Collection $objResult
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
     * @param \Queryyetsimple\Mvc\IModel[] $arrModel
     * @param string $strKey
     * @return array
     */
    protected function getModelKey(array $arrModel, $strKey = null)
    {
        return array_unique(array_values(array_map(function ($objModel) use ($strKey) {
            return $strKey ? $objModel->getProp($strKey) : $objModel->getPrimaryKeyForQuery();
        }, $arrModel)));
    }

    /**
     * 从模型返回查询
     *
     * @return \Queryyetsimple\Database\Select
     */
    protected function getSelectFromModel()
    {
        $this->objSelect = $this->objTargetModel->getClassCollectionQuery();
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        $objSelect = $this->objSelect->$method(...$arrArgs);

        if ($this->getSelect() === $objSelect) {
            return $this;
        }

        return $objSelect;
    }
}
