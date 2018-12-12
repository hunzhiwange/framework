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
    public function __construct(IEntity $targetEntity, IEntity $sourceEntity, string $targetKey, string $sourceKey)
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
    public function matchPreLoad(array $entitys, Collection $result, string $relation): array
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
     * 匹配预载入数据.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     * @param string                         $type
     *
     * @return array
     */
    protected function matchPreLoadOneOrMany(array $entitys, Collection $result, string $relation, string $type): array
    {
        $maps = $this->buildMap($result);

        foreach ($entitys as &$entity) {
            $key = $entity->__get($this->sourceKey);

            if (isset($maps[$key])) {
                $entity->withRelationProp(
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
    protected function getRelationValue(array $maps, string $key, string $type)
    {
        $value = $maps[$key];

        return 'one' === $type ? reset($value) : $this->targetEntity->collection($value);
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
            $maps[$value->__get($this->targetKey)][] = $value;
        }

        return $maps;
    }
}
