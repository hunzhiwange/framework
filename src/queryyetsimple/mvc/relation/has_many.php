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
namespace queryyetsimple\mvc\relation;

use queryyetsimple\mvc\imodel;
use queryyetsimple\support\collection;

/**
 * 关联模型 has_many
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
class has_many extends relation
{

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
        parent::__construct($objTargetModel, $objSourceModel, $strTargetKey, $strSourceKey);
    }

    /**
     * 关联基础查询条件
     *
     * @return void
     */
    public function addRelationCondition()
    {
        if (static::$booRelationCondition) {
            $this->objSelect->where($this->strTargetKey, $this->getSourceValue());
            $this->objSelect->whereNotNull($this->strTargetKey);
        }
    }

    /**
     * 设置预载入关联查询条件
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @return void
     */
    public function preLoadCondition(array $arrModel)
    {
        $this->objSelect->whereIn($this->strTargetKey, $this->getModelKey($arrModel, $this->strSourceKey));
    }

    /**
     * 匹配关联查询数据到模型 has_many
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @param \queryyetsimple\support\collection $objResult
     * @param string $strRelation
     * @return array
     */
    public function matchPreLoad(array $arrModel, collection $objResult, $strRelation)
    {
        return $this->matchPreLoadOneOrMany($arrModel, $objResult, $strRelation, 'many');
    }

    /**
     * 取回源模型对应数据
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->objSourceModel->getProp($this->strSourceKey);
    }

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    public function sourceQuery()
    {
        return $this->objSelect->getAll();
    }

    /**
     * 保存模型
     *
     * @param \queryyetsimple\mvc\imodel $objModel
     * @return \queryyetsimple\mvc\imodel
     */
    public function save(imodel $objModel)
    {
        $this->withSourceKeyValue($objModel);
        return $objModel->save();
    }

    /**
     * 批量保存模型
     *
     * @param \queryyetsimple\support\collection|array $mixModel
     * @return \queryyetsimple\support\collection|array
     */
    public function saveMany($mixModel)
    {
        foreach ($mixModel as $objModel) {
            $this->save($objModel);
        }

        return $mixModel;
    }

    /**
     * 创建模型实例
     *
     * @param array $arrProp
     * @return \queryyetsimple\mvc\imodel
     */
    public function create(array $arrProp)
    {
        $objModel = $this->objTargetModel->newInstance($arrProp);
        $this->withSourceKeyValue($objModel);
        $objModel->save();

        return $objModel;
    }

    /**
     * 批量创建模型实例
     *
     * @param array $arrProps
     * @return array
     */
    public function createMany(array $arrProps)
    {
        $arrModels = [];
        foreach ($arrProps as $arrProp) {
            $arrModels[] = $this->create($arrProp);
        }

        return $arrModels;
    }

    /**
     * 更新关联模型的数据
     *
     * @param array $arrProp
     * @return int
     */
    public function update(array $arrProp)
    {
        return $this->objSelect->update($arrProp);
    }

    /**
     * 取得源外键值
     *
     * @return mixed
     */
    public function getSourceKeyValue()
    {
        return $this->objSourceModel->getProp($this->strSourceKey);
    }

    /**
     * 模型添加源字段数据
     *
     * @param \queryyetsimple\mvc\imodel $objModel
     * @return void
     */
    protected function withSourceKeyValue(imodel $objModel)
    {
        $objModel->forceProp($this->strTargetKey, $this->getSourceKeyValue());
    }

    /**
     * 匹配预载入数据
     *
     * @param \queryyetsimple\mvc\imodel[] $arrModel
     * @param \queryyetsimple\support\collection $objResult
     * @param string $strRelation
     * @param string $strType
     * @return array
     */
    protected function matchPreLoadOneOrMany(array $arrModel, collection $objResult, $strRelation, $strType)
    {
        $arrMap = $this->buildMap($objResult);

        foreach ($arrModel as &$objModel) {
            $mixKey = $objModel->getProp($this->strSourceKey);

            if (isset($arrMap[$mixKey])) {
                $objModel->setRelationProp($strRelation, $this->getRelationValue($arrMap, $mixKey, $strType));
            }
        }

        return $arrModel;
    }

    /**
     * 取得关联模型数据
     *
     * @param array $arrMap
     * @param string $strKey
     * @param string $strType
     * @return mixed
     */
    protected function getRelationValue(array $arrMap, $strKey, $strType)
    {
        $arrValue = $arrMap[$strKey];
        return $strType == 'one' ? reset($arrValue) : $this->objTargetModel->collection($arrValue);
    }

    /**
     * 模型隐射数据
     *
     * @param \queryyetsimple\support\collection $objResult
     * @return array
     */
    protected function buildMap(collection $objResult)
    {
        $arrMap = [];

        foreach ($objResult as $objResultModel) {
            $arrMap[$objResultModel->getProp($this->strTargetKey)][] = $objResultModel;
        }

        return $arrMap;
    }
}
