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

use Closure;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Select;

/**
 * 关联实体 ManyMany.
 */
class ManyMany extends Relation
{
    /**
     * 中间实体.
     */
    protected Entity $middleEntity;

    /**
     * 目标中间实体关联字段.
    */
    protected string $middleTargetKey;

    /**
     * 源中间实体关联字段.
    */
    protected string $middleSourceKey;

    /**
     * 中间实体只包含软删除的数据.
    */
    protected bool $middleOnlySoftDeleted = false;

    /**
     * 中间实体包含软删除的数据.
    */
    protected bool $middleWithSoftDeleted = false;

    /**
     * 中间实体查询字段.
     */
    protected array $middleField = [];

    /**
     * 构造函数.
     */
    public function __construct(Entity $targetEntity, Entity $sourceEntity, Entity $middleEntity, string $targetKey, string $sourceKey, string $middleTargetKey, string $middleSourceKey, ?Closure $scope = null)
    {
        $this->middleEntity = $middleEntity;
        $this->middleTargetKey = $middleTargetKey;
        $this->middleSourceKey = $middleSourceKey;

        parent::__construct($targetEntity, $sourceEntity, $targetKey, $sourceKey, $scope);
    }

    /**
     * 中间实体包含软删除数据的实体查询对象.
     *
     * - 获取包含软删除的数据.
     */
    public function middleWithSoftDeleted(bool $middleWithSoftDeleted = true): self
    {
        $this->middleWithSoftDeleted = $middleWithSoftDeleted;

        return $this;
    }

    /**
     * 中间实体仅仅包含软删除数据的实体查询对象.
     *
     * - 获取只包含软删除的数据.
     */
    public function middleOnlySoftDeleted(bool $middleOnlySoftDeleted = true): self
    {
        $this->middleOnlySoftDeleted = $middleOnlySoftDeleted;

        return $this;
    }

    /**
     * 中间实体查询字段.
     */
    public function middleField(array $middleField = []): self
    {
        $this->middleField = $middleField;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addRelationCondition(): void
    {
        $this->prepareRelationCondition(function ($sourceValue): void {
            $this->selectRelationData([$sourceValue]);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function preLoadCondition(array $entitys): void
    {
        if (!$sourceValue = $this->getPreLoadSourceValue($entitys)) {
            $this->emptySourceData = true;

            return;
        }

        $this->emptySourceData = false;
        $this->selectRelationData($sourceValue);
    }

    /**
     * {@inheritDoc}
     */
    public function matchPreLoad(array $entitys, collection $result, string $relation): array
    {
        $maps = $this->buildMap($result);
        foreach ($entitys as $entity) {
            $key = $entity->prop($this->sourceKey);
            $entity->withRelationProp(
                $relation,
                $this->targetEntity->collection($maps[$key] ?? []),
            );
        }

        return $entitys;
    }

    /**
     * {@inheritDoc}
     */
    public function sourceQuery(): mixed
    {
        if (true === $this->emptySourceData) {
            return $this->targetEntity->collection();
        }

        $tmps = Select::withoutPreLoadsResult(function () {
            return $this->select->findAll();
        });
        if (!$tmps) {
            return $this->targetEntity->collection();
        }

        $result = [];
        $middelEntityClass = $this->middleEntity::class;
        $targetEntityClass = $this->targetEntity::class;
        foreach ($tmps as $value) {
            $value = (array) $value;
            $middleEnity = new $middelEntityClass($this->normalizeMiddelEntityData($value), true, true);
            $targetEntity = new $targetEntityClass($value, true, true);
            $targetEntity->withMiddle($middleEnity);
            $result[] = $targetEntity;
        }

        return $this->targetEntity->collection($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getPreLoad(): mixed
    {
        return $this->getSelect()->preLoadResult($this->sourceQuery());
    }

    /**
     * 取得中间实体.
     */
    public function getMiddleEntity(): Entity
    {
        return $this->middleEntity;
    }

    /**
     * 取得目标中间实体关联字段.
     */
    public function getMiddleTargetKey(): string
    {
        return $this->middleTargetKey;
    }

    /**
     * 取得源中间实体关联字段.
     */
    public function getMiddleSourceKey(): string
    {
        return $this->middleSourceKey;
    }

    /**
     * 整理中间实体数据.
     */
    protected function normalizeMiddelEntityData(array &$value): array
    {
        $middelData = [
            $this->middleSourceKey => $value['middle_'.$this->middleSourceKey],
            $this->middleTargetKey => $value['middle_'.$this->middleTargetKey],
        ];
        unset(
            $value['middle_'.$this->middleSourceKey],
            $value['middle_'.$this->middleTargetKey]
        );

        foreach ($this->middleField as $middleFieldAlias => $middleField) {
            $middleFieldAlias = is_string($middleFieldAlias) ? $middleFieldAlias : $middleField;
            $middelData[$middleField] = $value[$middleFieldAlias];
            unset($value[$middleFieldAlias]);
        }

        return $middelData;
    }

    /**
     * 查询关联数据.
     */
    protected function selectRelationData(array $sourceValue): void
    {
        $this->emptySourceData = false;
        $middleCondition = [
            $this->middleTargetKey => '{['.$this->targetEntity->table().'.'.$this->targetKey.']}',
        ];
        $this->prepareMiddleSoftDeleted($middleCondition);

        $middleField = array_merge($this->middleField, [
            'middle_'.$this->middleTargetKey => $this->middleTargetKey,
            'middle_'.$this->middleSourceKey => $this->middleSourceKey,
        ]);

        $this->select
            ->join(
                $this->middleEntity->table(),
                $middleField,
                $middleCondition,
            )
            ->whereIn(
                $this->middleEntity->table().'.'.$this->middleSourceKey,
                $sourceValue,
            )
            ->asSome()
            ->asCollection(false);
    }

    /**
     * 中间实体软删除处理.
     */
    protected function prepareMiddleSoftDeleted(array &$middleCondition): void
    {
        if (!defined($this->middleEntity::class.'::DELETE_AT')) {
            return;
        }

        if (true === $this->middleWithSoftDeleted) {
            return;
        }

        if (true === $this->middleOnlySoftDeleted) {
            $value = ['>', 0];
        } else {
            $value = 0;
        }

        $field = $this->middleEntity->table().'.'.$this->middleEntity::deleteAtColumn();
        $middleCondition[$field] = $value;
    }

    /**
     * 取回源实体对应数据.
     */
    protected function getPreLoadSourceValue(array $entitys): array
    {
        $data = [];
        foreach ($entitys as $sourceEntity) {
            if ($value = $sourceEntity->prop($this->sourceKey)) {
                $data[] = $value;
            }
        }

        return $data;
    }

    /**
     * 实体映射数据.
     */
    protected function buildMap(Collection $result): array
    {
        $maps = [];
        foreach ($result as $entity) {
            $maps[$entity->middle()->prop($this->middleSourceKey)][] = $entity;
        }

        return $maps;
    }
}
