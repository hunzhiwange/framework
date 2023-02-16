<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Database\Ddd\Select;

/**
 * 关联实体 HasMany.
 */
class HasMany extends Relation
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
    public function preLoadCondition(array $entities): void
    {
        if (!$sourceValue = $this->getEntityKey($entities, $this->sourceKey)) {
            $this->emptySourceData = true;

            return;
        }

        $this->emptySourceData = false;
        $this->select->whereIn($this->targetKey, $sourceValue);
    }

    /**
     * {@inheritDoc}
     */
    public function matchPreLoad(array $entities, EntityCollection $result, string $relation): array
    {
        return $this->matchPreLoadOneOrMany(
            $entities,
            $result,
            $relation,
            'many'
        );
    }

    /**
     * {@inheritDoc}
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
    protected function matchPreLoadOneOrMany(array $entities, EntityCollection $result, string $relation, string $type): array
    {
        $maps = $this->buildMap($result);
        foreach ($entities as &$entity) {
            $key = $entity->prop($this->sourceKey);
            $entity->withRelationProp(
                $relation,
                $this->getRelationValue($maps[$key] ?? [], $type)
            );
        }

        return $entities;
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
    protected function buildMap(EntityCollection $result): array
    {
        $maps = [];

        /** @var Entity $value */
        foreach ($result as $value) {
            $maps[$value->prop($this->targetKey)][] = $value;
        }

        return $maps;
    }
}
