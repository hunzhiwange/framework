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
use Leevel\Database\Ddd\Select;

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
     * 关联基础查询条件.
     */
    public function addRelationCondition(): void
    {
        if (static::$relationCondition) {
            if (null === $sourceValue = $this->getSourceValue()) {
                $this->emptySourceData = true;
            } else {
                $this->emptySourceData = false;
                $this->select->where($this->targetKey, $sourceValue);
            }
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
            $key = $value->prop($this->sourceKey);
            $value->withRelationProp($relation, $maps[$key] ?? $this->targetEntity->make());
        }

        return $entitys;
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     */
    public function preLoadCondition(array $entitys): void
    {
        if (!$sourceValue = $this->getPreLoadEntityValue($entitys)) {
            $this->emptySourceData = true;

            return;
        }

        $this->emptySourceData = false;
        $this->select->whereIn($this->targetKey, $sourceValue);
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->sourceEntity->prop($this->sourceKey);
    }

    /**
     * 查询关联对象.
     *
     * @return mixed
     */
    public function sourceQuery()
    {
        if (true === $this->emptySourceData) {
            return $this->targetEntity->make();
        }

        return Select::withoutPreLoadsResult(function () {
            return $this->select->findOne();
        });
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
            $maps[$entity->prop($this->targetKey)] = $entity;
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
        $data = [];
        foreach ($entitys as $value) {
            if (null !== ($tmp = $value->prop($this->sourceKey))) {
                $data[] = $tmp;
            }
        }

        if (!$data) {
            return [];
        }

        return array_values(array_unique($data));
    }
}
