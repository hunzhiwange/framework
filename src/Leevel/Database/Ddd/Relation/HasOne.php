<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Database\Ddd\Select;
use Leevel\Support\Collection;

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
    public function matchPreLoad(array $entitys, Collection $result, string $relation): array
    {
        return $this->matchPreLoadOneOrMany($entitys, $result, $relation, 'one');
    }
}
