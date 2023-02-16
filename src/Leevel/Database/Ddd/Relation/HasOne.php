<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Database\Ddd\EntityCollection;
use Leevel\Database\Ddd\Select;

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
        if (true === $this->emptySourceData) {
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
}
