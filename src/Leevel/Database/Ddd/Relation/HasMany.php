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
     * @param \Leevel\Database\Ddd\IEntity $targetEntity
     * @param \Leevel\Database\Ddd\IEntity $sourceEntity
     * @param string                       $targetKey
     * @param string                       $sourceKey
     */
    public function __construct(IEntity $targetEntity, IEntity $sourceEntity, $targetKey, $sourceKey)
    {
        parent::__construct($targetEntity, $sourceEntity, $targetKey, $sourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition()
    {
        if (static::$relationCondition) {
            $this->select->where(
                $this->targetKey,
                $this->getSourceValue()
            );

            $this->select->whereNotNull(
                $this->targetKey
            );
        }
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     */
    public function preLoadCondition(array $entitys)
    {
        $this->select->whereIn(
            $this->targetKey,
            $this->getEntityKey($entitys, $this->sourceKey)
        );
    }

    /**
     * 匹配关联查询数据到模型实体 HasMany.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     *
     * @return array
     */
    public function matchPreLoad(array $entitys, Collection $result, $relation)
    {
        return $this->matchPreLoadOneOrMany(
            $entitys,
            $result,
            $relation,
            'many'
        );
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->sourceEntity->__get($this->sourceKey);
    }

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    public function sourceQuery()
    {
        return $this->select->findAll();
    }

    /**
     * 保存模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function save(IEntity $entity)
    {
        $this->withSourceKeyValue($entity);

        return $entity->save();
    }

    /**
     * 批量保存模型实体.
     *
     * @param array|\Leevel\Collection\Collection $entitys
     *
     * @return array|\Leevel\Collection\Collection
     */
    public function saveMany($entitys)
    {
        foreach ($entitys as $entity) {
            $this->save($entity);
        }

        return $entitys;
    }

    /**
     * 创建模型实体实例.
     *
     * @param array $props
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function create(array $props)
    {
        $entity = $this->targetEntity->newInstance($props);

        $this->withSourceKeyValue($entity);

        $entity->save();

        return $entity;
    }

    /**
     * 批量创建模型实体实例.
     *
     * @param array $props
     *
     * @return array
     */
    public function createMany(array $props)
    {
        $entitys = [];

        foreach ($props as $value) {
            $entitys[] = $this->create($value);
        }

        return $entitys;
    }

    /**
     * 更新关联模型实体的数据.
     *
     * @param array $props
     *
     * @return int
     */
    public function update(array $props)
    {
        return $this->select->update($props);
    }

    /**
     * 取得源外键值
     *
     * @return mixed
     */
    public function getSourceKeyValue()
    {
        return $this->sourceEntity->getPropValue($this->sourceKey);
    }

    /**
     * 模型实体添加源字段数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    protected function withSourceKeyValue(IEntity $entity)
    {
        $entity->forceProp(
            $this->targetKey,
            $this->getSourceKeyValue()
        );
    }

    /**
     * 匹配预载入数据.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     * @param string                         $type
     *
     * @return array
     */
    protected function matchPreLoadOneOrMany(array $entitys, Collection $result, $relation, $type)
    {
        $maps = $this->buildMap($result);

        foreach ($entitys as &$entity) {
            $key = $entity->__get($this->sourceKey);

            if (isset($maps[$key])) {
                $entity->setRelationProp(
                    $relation,
                    $this->getRelationValue($maps, $key, $type)
                );
            }
        }

        return $entitys;
    }

    /**
     * 取得关联模型实体数据.
     *
     * @param array  $maps
     * @param string $key
     * @param string $type
     *
     * @return mixed
     */
    protected function getRelationValue(array $maps, $key, $type)
    {
        $value = $maps[$key];

        return 'one' === $type ? reset($value) :
            $this->targetEntity->collection($value);
    }

    /**
     * 模型实体隐射数据.
     *
     * @param \Leevel\Collection\Collection $result
     *
     * @return array
     */
    protected function buildMap(Collection $result)
    {
        $maps = [];

        foreach ($result as $value) {
            $maps[$value->getPropValue($this->targetKey)][] = $value;
        }

        return $maps;
    }
}
