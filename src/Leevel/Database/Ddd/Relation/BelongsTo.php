<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Select;
use Leevel\Support\Collection;

/**
 * 关联实体 BelongsTo.
 */
class BelongsTo extends Relation
{
    /**
     * {@inheritDoc}
     */
    public function addRelationCondition(): void
    {
        $this->prepareRelationCondition(function ($sourceValue): void {
            $this->select->where($this->targetKey, $sourceValue);
        });
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function sourceQuery(): mixed
    {
        if (true === $this->emptySourceData) {
            return $this->targetEntity->make();
        }

        return Select::withoutPreLoadsResult(function () {
            return $this->select->findOne();
        });
    }

    /**
     * 实体映射数据.
     */
    protected function buildMap(Collection $result): array
    {
        $maps = [];

        /** @var Entity $entity */
        foreach ($result as $entity) {
            $maps[$entity->prop($this->targetKey)] = $entity;
        }

        return $maps;
    }

    /**
     * 分析预载入实体中对应的源数据.
     */
    protected function getPreLoadEntityValue(array $entitys): array
    {
        $data = [];
        foreach ($entitys as $value) {
            if ($tmp = $value->prop($this->sourceKey)) {
                $data[] = $tmp;
            }
        }

        if (!$data) {
            return [];
        }

        return array_values(array_unique($data));
    }
}
