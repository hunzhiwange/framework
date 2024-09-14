<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Cache\ICache;
use Leevel\Database\Condition;
use Leevel\Database\Database;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Database\Ddd\Select;
use Leevel\Database\ICondition;
use Leevel\Database\IDatabase;
use Leevel\Database\ISelect as IDatabaseSelect;
use Leevel\Database\Page;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Di\IContainer;
use Leevel\Server\Pool\Pool as BasePool;
use Leevel\Support\Collection;

/**
 * 关联实体基类.
 */
abstract class Relation implements IRelation
{
    /**
     * 查询对象.
     */
    protected Select $select;

    /**
     * 关联目标实体.
     */
    protected Entity $targetEntity;

    /**
     * 源实体.
     */
    protected Entity $sourceEntity;

    /**
     * 目标关联字段.
     */
    protected string $targetKey;

    /**
     * 源关联字段.
     */
    protected string $sourceKey;

    /**
     * 是否初始化关联查询条件.
     */
    protected static bool $relationCondition = true;

    /**
     * 源数据为空.
     */
    protected bool $emptySourceData = false;

    /**
     * 构造函数.
     */
    public function __construct(Entity $targetEntity, Entity $sourceEntity, string $targetKey, string $sourceKey, ?\Closure $scope = null)
    {
        $this->targetEntity = $targetEntity;
        $this->sourceEntity = $sourceEntity;
        $this->targetKey = $targetKey;
        $this->sourceKey = $sourceKey;
        $this->getSelectFromEntity();
        $this->scope($scope);
        $this->addRelationCondition();
    }

    public function entity(): Entity
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public static function withoutPreLoadsResult(\Closure $call): mixed
    {
        return Select::withoutPreLoadsResult($call);
    }

    public function withSoftDeleted(): self
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function onlySoftDeleted(): self
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function eager(array $relation): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $relation);
    }

    public function preLoadResult(mixed $result): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $result);
    }

    public function findEntity(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $idOrCondition, $column);
    }

    public function findMany(null|array|\Closure $idsOrCondition = null, array $column = ['*']): EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $idsOrCondition, $column);
    }

    public function findOrFail(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $idOrCondition, $column);
    }

    public function getDatabaseSelect(): DatabaseSelect
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function getContainer(): IContainer
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function setCache(?ICache $cache): void
    {
        $this->callSelectMethod(__FUNCTION__, $cache);
    }

    public function getCache(): ?ICache
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function databaseSelect(): DatabaseSelect
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function getPdo(bool $master = false): \PDO
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $master);
    }

    public function query(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $sql, $bindParams, $master, $cacheName, $cacheExpire, $cache);
    }

    public function procedure(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $sql, $bindParams, $master, $cacheName, $cacheExpire, $cache);
    }

    public function execute(string $sql, array $bindParams = [], bool $master = false, bool $insert = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $sql, $bindParams, $master, $insert);
    }

    public function cursor(string $sql, array $bindParams = [], bool $master = false): \Generator
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $sql, $bindParams, $master);
    }

    public function prepare(string $sql, array $bindParams = [], bool $master = false): \PDOStatement
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $sql, $bindParams, $master);
    }

    public function transaction(\Closure $action): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $action);
    }

    public function beginTransaction(): void
    {
        $this->callSelectMethod(__FUNCTION__);
    }

    public function inTransaction(): bool
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function commit(): void
    {
        $this->callSelectMethod(__FUNCTION__);
    }

    public function rollBack(): void
    {
        $this->callSelectMethod(__FUNCTION__);
    }

    public function setSavepoints(bool $savepoints): void
    {
        $this->callSelectMethod(__FUNCTION__, $savepoints);
    }

    public function hasSavepoints(): bool
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function getLastSql(): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function setRealLastSql(array $realLastSql): void
    {
        $this->callSelectMethod(__FUNCTION__, $realLastSql);
    }

    public function getRealLastSql(): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function close(): void
    {
        $this->callSelectMethod(__FUNCTION__);
    }

    public static function getRawSql(string $sql, array $bindParams): string
    {
        return Database::getRawSql($sql, $bindParams);
    }

    public function parseDsn(array $config): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $config);
    }

    public function getTableNames(string $dbName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $dbName, $master);
    }

    public function getTableColumns(string $tableName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $tableName, $master);
    }

    public function getUniqueIndex(string $tableName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $tableName, $master);
    }

    public function identifierColumn(string $name): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $name);
    }

    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $limitCount, $limitOffset);
    }

    public function release(): void
    {
        $this->callSelectMethod(__FUNCTION__);
    }

    public function setPool(BasePool $pool): void
    {
        $this->callSelectMethod(__FUNCTION__, $pool);
    }

    public function forPage(int $page, int $perPage = 10): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $page, $perPage);
    }

    public function time(string $type = 'date'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $type);
    }

    public function endTime(): self
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function reset(?string $config = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $config);
    }

    public function comment(string $comment): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $comment);
    }

    public function prefix(string $prefix): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $prefix);
    }

    public function table(array|\Closure|ICondition|IDatabaseSelect|string $table, array|string $cols = '*'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols);
    }

    public function columns(array|string $cols = '*', ?string $table = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function setColumns(array|string $cols = '*', ?string $table = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function field(array|string $cols = '*', ?string $table = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function middlewares(string ...$middlewares): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$middlewares);
    }

    public function where(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orWhere(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereRaw(string $raw): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orWhereRaw(string $raw): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function whereExists(mixed $exists): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereNotExists(mixed $exists): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereBetween(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotBetween(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNull(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotNull(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereIn(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotIn(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereLike(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotLike(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDate(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDay(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereMonth(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereYear(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $names, $value, $dataType);
    }

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes, $type);
    }

    public function ignoreIndex(array|string $indexes): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes);
    }

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects, $type);
    }

    public function unionAll(array|callable|ICondition|self|string $selects): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects);
    }

    public function groupBy(array|string $expression): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression);
    }

    public function having(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orHaving(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingRaw(string $raw): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orHavingRaw(string $raw): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function havingBetween(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotBetween(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNull(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotNull(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingIn(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotIn(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingLike(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotLike(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDate(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDay(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingMonth(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingYear(mixed ...$cond): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression, $orderDefault);
    }

    public function latest(string $field = 'create_at'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function oldest(string $field = 'create_at'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function distinct(bool $flag = true): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function count(string $field = '*', string $alias = 'row_count'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function avg(string $field, string $alias = 'avg_value'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function max(string $field, string $alias = 'max_value'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function min(string $field, string $alias = 'min_value'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function sum(string $field, string $alias = 'sum_value'): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function one(): self
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function all(): self
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function top(int $count = 30): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $count);
    }

    public function limit(int $offset = 0, int $count = 0): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $offset, $count);
    }

    public function forUpdate(bool $flag = true): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function lockShare(bool $flag = true): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function resetBindParams(array $bindParams = []): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParams);
    }

    public function setBindParamsPrefix(string $bindParamsPrefix): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParamsPrefix);
    }

    public function if(mixed $condition): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function elif(mixed $condition): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function else(): self
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function fi(): self
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function getAlias(): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public static function raw(string $raw): string
    {
        return Condition::raw($raw);
    }

    public function makeSql(bool $withLogicGroup = false): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $withLogicGroup);
    }

    public function getBindParams(): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function databaseCondition(): Condition
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function databaseConnect(): IDatabase
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function master(bool $master = false): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $master);
    }

    public function asSome(?\Closure $asSome = null, array $args = []): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asSome, $args);
    }

    public function asArray(?\Closure $asArray = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asArray);
    }

    public function asCollection(bool $asCollection = true, array $valueTypes = []): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asCollection, $valueTypes);
    }

    public function select(null|callable|IDatabaseSelect|string $data = null, array $bind = []): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $data, $bind);
    }

    public function insert(array|string $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind, $replace);
    }

    public function insertAll(array $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind, $replace);
    }

    public function update(array|string $data, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind);
    }

    public function updateColumn(string $column, mixed $value, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $column, $value, $bind);
    }

    public function updateIncrease(string $column, int $step = 1, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $column, $step, $bind);
    }

    public function updateDecrease(string $column, int $step = 1, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $column, $step, $bind);
    }

    public function delete(?string $data = null, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind);
    }

    public function truncate(): array|int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findOne(): mixed
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findArray(): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findAsArray(): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findCollection(): Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findAll(?\Closure $condition = null): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $condition);
    }

    public function find(?int $num = null, ?\Closure $condition = null): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $num, $condition);
    }

    public function value(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findList(mixed $fieldValue, ?string $fieldKey = null, ?\Closure $condition = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $fieldValue, $fieldKey, $condition);
    }

    public function chunk(int $count, \Closure $chunk): void
    {
        $this->callSelectMethod(__FUNCTION__, $count, $chunk);
    }

    public function each(int $count, \Closure $each): void
    {
        $this->callSelectMethod(__FUNCTION__, $count, $each);
    }

    public function findCount(string $field = '*', ?\Closure $condition = null): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findAvg(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findMax(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findMin(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findSum(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findPageList(int $currentPage, int $perPage = 10, ?int $count = null, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $count, $config, $condition);
    }

    public function findPageMacro(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $config, $condition);
    }

    public function findPagePrevNext(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $config, $condition);
    }

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $name, $expire, $cache);
    }

    public function getSelect(): Select
    {
        return $this->select;
    }

    public function getPreLoad(): mixed
    {
        if ($this->emptySourceData) {
            $data = $this->targetEntity->collection();
        } else {
            $data = $this->findAll();
        }

        return $this->getSelect()->preLoadResult($data);
    }

    public function getTargetEntity(): Entity
    {
        return $this->targetEntity;
    }

    public function getSourceEntity(): Entity
    {
        return $this->sourceEntity;
    }

    public function getTargetKey(): string
    {
        return $this->targetKey;
    }

    public function getSourceKey(): string
    {
        return $this->sourceKey;
    }

    public static function withoutRelationCondition(\Closure $call): self
    {
        $old = static::$relationCondition;
        static::$relationCondition = false;

        try {
            $relation = $call();
            static::$relationCondition = $old;
        } catch (\Throwable $e) {
            static::$relationCondition = $old;

            throw $e;
        }

        return $relation;
    }

    public function getSourceValue(): mixed
    {
        return $this->sourceEntity->prop($this->sourceKey);
    }

    protected function callSelectMethodChain(string $method, mixed ...$args): self
    {
        $this->select->{$method}(...$args);

        return $this;
    }

    protected function callSelectMethod(string $method, mixed ...$args): mixed
    {
        $select = $this->select->{$method}(...$args);
        if ($this->getSelect() === $select) {
            return $this;
        }

        return $select;
    }

    /**
     * 准备关联查询条件.
     */
    protected function prepareRelationCondition(\Closure $call): void
    {
        if (!static::$relationCondition) {
            return;
        }

        if (!$sourceValue = $this->getSourceValue()) {
            $this->emptySourceData = true;
        } else {
            $this->emptySourceData = false;
            $call($sourceValue);
        }
    }

    /**
     * 返回实体数组指定键的值数组.
     */
    protected function getEntityKey(array $entities, string $key): array
    {
        $values = array_map(static function (Entity $entity) use ($key): mixed {
            return $entity->prop($key);
        }, $entities);

        return array_unique(array_values($values));
    }

    /**
     * 从实体返回查询.
     */
    protected function getSelectFromEntity(): void
    {
        $this->select = $this->targetEntity->select();
    }

    /**
     * 关联查询作用域.
     */
    protected function scope(?\Closure $scope = null): void
    {
        if (!$scope) {
            return;
        }

        $scope($this);
    }
}
