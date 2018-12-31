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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;

/**
 * 关联模型实体 BelongsTo.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
 * @version 1.0
 */
class BelongsTo extends Relation
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
        }
    }

    /**
     * 匹配关联查询数据到模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     *
     * @return array
     */
    public function matchPreLoad(array $entitys, Collection $result, string $relation): array
    {
        $maps = $this->buildMap($result);

        foreach ($entitys as $value) {
            $key = $value->__get($this->sourceKey);

            if (isset($maps[$key])) {
                $value->withRelationProp($relation, $maps[$key]);
            }
        }

        return $entitys;
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
            $this->getPreLoadEntityValue($entitys)
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
        return $this->select->findOne();
    }

    /**
     * 模型实体隐射数据.
     *
     * @param \Leevel\Collection\Collection $result
     *
     * @return array
     */
    protected function buildMap(Collection $result): array
    {
        $maps = [];

        foreach ($result as $entity) {
            $maps[$entity->__get($this->targetKey)] = $entity;
        }

        return $maps;
    }

    /**
     * 分析预载入模型实体中对应的源数据.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     *
     * @return array
     */
    protected function getPreLoadEntityValue(array $entitys): array
    {
        $arr = [];

        foreach ($entitys as $value) {
            if (null !== ($tmp = $value->__get($this->sourceKey))) {
                $arr[] = $tmp;
            }
        }

        if (0 === count($arr)) {
            return [0];
        }

        return array_values(array_unique($arr));
    }
}
