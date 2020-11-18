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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Ddd\Relation;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Select;

/**
 * 关联实体 HasMany.
 */
class HasMany extends Relation
{
    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition(): void
    {
        $this->prepareRelationCondition(function ($sourceValue): void {
            $this->select->where($this->targetKey, $sourceValue);
        });
    }

    /**
     * 设置预载入关联查询条件.
     */
    public function preLoadCondition(array $entitys): void
    {
        if (!$sourceValue = $this->getEntityKey($entitys, $this->sourceKey)) {
            $this->emptySourceData = true;

            return;
        }

        $this->emptySourceData = false;
        $this->select->whereIn($this->targetKey, $sourceValue);
    }

    /**
     * 匹配关联查询数据到实体 HasMany.
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
     * 查询关联对象
     */
    public function sourceQuery(): mixed
    {
        if (true === $this->emptySourceData) {
            return $this->targetEntity->collection();
        }

        return Select::withoutPreLoadsResult(function () {
            return $this->select->findAll();
        });
    }

    /**
     * 匹配预载入数据.
     */
    protected function matchPreLoadOneOrMany(array $entitys, Collection $result, string $relation, string $type): array
    {
        $maps = $this->buildMap($result);
        foreach ($entitys as &$entity) {
            $key = $entity->prop($this->sourceKey);
            $entity->withRelationProp(
                $relation,
                $this->getRelationValue($maps[$key] ?? [], $type)
            );
        }

        return $entitys;
    }

    /**
     * 取得关联实体数据.
     */
    protected function getRelationValue(array $entitys, string $type): mixed
    {
        if (!$entitys) {
            return 'one' === $type ?
                $this->targetEntity->make() :
                $this->targetEntity->collection();
        }

        return 'one' === $type ?
            reset($entitys) :
            $this->targetEntity->collection($entitys);
    }

    /**
     * 实体映射数据.
     */
    protected function buildMap(Collection $result): array
    {
        $maps = [];
        foreach ($result as $value) {
            $maps[$value->prop($this->targetKey)][] = $value;
        }

        return $maps;
    }
}
