<?php
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Mvc\Relation;

use Queryyetsimple\{
    Mvc\IModel,
    Collection\Collection
};

/**
 * 关联模型 HasMany
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.28
 * @version 1.0
 */
class HasMany extends Relation
{

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
     * @param \Queryyetsimple\Mvc\IModel[] $arrModel
     * @return void
     */
    public function preLoadCondition(array $arrModel)
    {
        $this->objSelect->whereIn($this->strTargetKey, $this->getModelKey($arrModel, $this->strSourceKey));
    }

    /**
     * 匹配关联查询数据到模型 HasMany
     *
     * @param \Queryyetsimple\Mvc\IModel[] $arrModel
     * @param \Queryyetsimple\Collection\Collection $objResult
     * @param string $strRelation
     * @return array
     */
    public function matchPreLoad(array $arrModel, Collection $objResult, $strRelation)
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
     * @param \Queryyetsimple\Mvc\IModel $objModel
     * @return \Queryyetsimple\Mvc\IModel
     */
    public function save(IModel $objModel)
    {
        $this->withSourceKeyValue($objModel);
        return $objModel->save();
    }

    /**
     * 批量保存模型
     *
     * @param \Queryyetsimple\Collection\Collection|array $mixModel
     * @return \Queryyetsimple\Collection\Collection|array
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
     * @return \Queryyetsimple\Mvc\IModel
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
     * @param \Queryyetsimple\Mvc\IModel $objModel
     * @return void
     */
    protected function withSourceKeyValue(IModel $objModel)
    {
        $objModel->forceProp($this->strTargetKey, $this->getSourceKeyValue());
    }

    /**
     * 匹配预载入数据
     *
     * @param \Queryyetsimple\Mvc\IModel[] $arrModel
     * @param \Queryyetsimple\Collection\Collection $objResult
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
     * @param \Queryyetsimple\Collection\Collection $objResult
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
