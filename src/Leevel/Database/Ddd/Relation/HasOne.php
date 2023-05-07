<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Database\Ddd\Select;
use Leevel\Database\Ddd\UnitOfWork;

/**
 * 关联实体 HasOne.
 */
class HasOne extends HasMany
{
    /**
     * {@inheritDoc}
     */
    public function sourceQuery(): mixed
    {
        if ($this->emptySourceData) {
            return $this->targetEntity->make();
        }

        return Select::withoutPreLoadsResult(function () {
            return $this->select->findOne();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function matchPreLoad(array $entities, EntityCollection $result, string $relation): array
    {
        return $this->matchPreLoadOneOrMany($entities, $result, $relation, 'one');
    }

    public function storeNewRelation(UnitOfWork $unitOfWork, array $relationData = []): void
    {
        $unitOfWork->on($this->sourceEntity, function () use ($relationData, $unitOfWork): void {
            $unitOfWork->persist($this->newRelationEntity($relationData));
        });
    }

    public function newRelationEntity(array $relationData): Entity
    {
        $relationData[$this->targetKey] = $this->sourceEntity->prop($this->sourceKey);

        return $this->targetEntity->make($relationData);
    }
}
