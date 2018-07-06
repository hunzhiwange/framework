<?php

declare(strict_types=1);

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

namespace Leevel\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;

/**
 * 关联模型实体 HasMany.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
 * @version 1.0
 */
class HasMany extends Relation
{
    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $objTargetEntity
     * @param \Leevel\Database\Ddd\IEntity $objSourceEntity
     * @param string                       $strTargetKey
     * @param string                       $strSourceKey
     */
    public function __construct(IEntity $objTargetEntity, IEntity $objSourceEntity, $strTargetKey, $strSourceKey)
    {
        parent::__construct($objTargetEntity, $objSourceEntity, $strTargetKey, $strSourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition()
    {
        if (static::$booRelationCondition) {
            $this->objSelect->where($this->strTargetKey, $this->getSourceValue());
            $this->objSelect->whereNotNull($this->strTargetKey);
        }
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     */
    public function preLoadCondition(array $arrEntity)
    {
        $this->objSelect->whereIn($this->strTargetKey, $this->getEntityKey($arrEntity, $this->strSourceKey));
    }

    /**
     * 匹配关联查询数据到模型实体 HasMany.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     * @param \Leevel\Collection\Collection  $objResult
     * @param string                         $strRelation
     *
     * @return array
     */
    public function matchPreLoad(array $arrEntity, Collection $objResult, $strRelation)
    {
        return $this->matchPreLoadOneOrMany($arrEntity, $objResult, $strRelation, 'many');
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->objSourceEntity->getProp($this->strSourceKey);
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
     * 保存模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $objEntity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function save(IEntity $objEntity)
    {
        $this->withSourceKeyValue($objEntity);

        return $objEntity->save();
    }

    /**
     * 批量保存模型实体.
     *
     * @param array|\Leevel\Collection\Collection $mixEntity
     *
     * @return array|\Leevel\Collection\Collection
     */
    public function saveMany($mixEntity)
    {
        foreach ($mixEntity as $objEntity) {
            $this->save($objEntity);
        }

        return $mixEntity;
    }

    /**
     * 创建模型实体实例.
     *
     * @param array $arrProp
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function create(array $arrProp)
    {
        $objEntity = $this->objTargetEntity->newInstance($arrProp);
        $this->withSourceKeyValue($objEntity);
        $objEntity->save();

        return $objEntity;
    }

    /**
     * 批量创建模型实体实例.
     *
     * @param array $arrProps
     *
     * @return array
     */
    public function createMany(array $arrProps)
    {
        $arrEntitys = [];
        foreach ($arrProps as $arrProp) {
            $arrEntitys[] = $this->create($arrProp);
        }

        return $arrEntitys;
    }

    /**
     * 更新关联模型实体的数据.
     *
     * @param array $arrProp
     *
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
        return $this->objSourceEntity->getProp($this->strSourceKey);
    }

    /**
     * 模型实体添加源字段数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $objEntity
     */
    protected function withSourceKeyValue(IEntity $objEntity)
    {
        $objEntity->forceProp($this->strTargetKey, $this->getSourceKeyValue());
    }

    /**
     * 匹配预载入数据.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     * @param \Leevel\Collection\Collection  $objResult
     * @param string                         $strRelation
     * @param string                         $strType
     *
     * @return array
     */
    protected function matchPreLoadOneOrMany(array $arrEntity, collection $objResult, $strRelation, $strType)
    {
        $arrMap = $this->buildMap($objResult);

        foreach ($arrEntity as &$objEntity) {
            $mixKey = $objEntity->getProp($this->strSourceKey);

            if (isset($arrMap[$mixKey])) {
                $objEntity->setRelationProp($strRelation, $this->getRelationValue($arrMap, $mixKey, $strType));
            }
        }

        return $arrEntity;
    }

    /**
     * 取得关联模型实体数据.
     *
     * @param array  $arrMap
     * @param string $strKey
     * @param string $strType
     *
     * @return mixed
     */
    protected function getRelationValue(array $arrMap, $strKey, $strType)
    {
        $arrValue = $arrMap[$strKey];

        return 'one' === $strType ? reset($arrValue) : $this->objTargetEntity->collection($arrValue);
    }

    /**
     * 模型实体隐射数据.
     *
     * @param \Leevel\Collection\Collection $objResult
     *
     * @return array
     */
    protected function buildMap(collection $objResult)
    {
        $arrMap = [];

        foreach ($objResult as $objResultEntity) {
            $arrMap[$objResultEntity->getProp($this->strTargetKey)][] = $objResultEntity;
        }

        return $arrMap;
    }
}
