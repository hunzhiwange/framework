<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Cache\ICache;
use Leevel\Database\Condition;
use Leevel\Database\Database;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\ICondition;
use Leevel\Database\IDatabase;
use Leevel\Database\ISelect as IDatabaseSelect;
use Leevel\Database\Page;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Di\IContainer;
use Leevel\Server\Pool\Pool as BasePool;
use Leevel\Support\Collection;

/**
 * 实体查询.
 */
class Select implements ISelect
{
    /**
     * 查询.
     */
    protected DatabaseSelect $select;

    /**
     * 关联预载入.
     */
    protected array $preLoads = [];

    /**
     * 是否执行预载入查询.
     */
    protected static bool $preLoadsResult = true;

    /**
     * 构造函数.
     */
    public function __construct(protected Entity $entity, int $softDeletedType = Entity::WITHOUT_SOFT_DELETED)
    {
        $this->initSelect($softDeletedType);
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

    public function findAll(): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function find(?int $num = null): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $num);
    }

    public function value(string $field): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field);
    }

    public function list(mixed $fieldValue, ?string $fieldKey = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $fieldValue, $fieldKey);
    }

    public function chunk(int $count, \Closure $chunk): void
    {
        $this->callSelectMethod(__FUNCTION__, $count, $chunk);
    }

    public function each(int $count, \Closure $each): void
    {
        $this->callSelectMethod(__FUNCTION__, $count, $each);
    }

    public function findCount(string $field = '*', string $alias = 'row_count'): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $field, $alias);
    }

    public function findAvg(string $field, string $alias = 'avg_value'): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $alias);
    }

    public function findMax(string $field, string $alias = 'max_value'): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $alias);
    }

    public function findMin(string $field, string $alias = 'min_value'): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $alias);
    }

    public function findSum(string $field, string $alias = 'sum_value'): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $alias);
    }

    public function page(int $currentPage, int $perPage = 10, ?int $count = null, array $config = []): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $count, $config);
    }

    public function pageMacro(int $currentPage, int $perPage = 10, array $config = []): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $config);
    }

    public function pagePrevNext(int $currentPage, int $perPage = 10, array $config = []): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $config);
    }

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): self
    {
        return $this->callSelectMethodChain(__FUNCTION__, $name, $expire, $cache);
    }

    public function queryOne(): mixed
    {
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function queryAll(): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function entity(): Entity
    {
        return $this->entity;
    }

    public static function withoutPreLoadsResult(\Closure $call): mixed
    {
        $old = static::$preLoadsResult;
        static::$preLoadsResult = false;

        try {
            $result = $call();
            static::$preLoadsResult = $old;
        } catch (\Throwable $e) {
            static::$preLoadsResult = $old;

            throw $e;
        }

        return $result;
    }

    public function withSoftDeleted(): self
    {
        $this->initSelect(Entity::WITH_SOFT_DELETED);

        return $this;
    }

    public function onlySoftDeleted(): self
    {
        $this->initSelect(Entity::ONLY_SOFT_DELETED);

        return $this;
    }

    public function eager(array $relation): self
    {
        $this->preLoads = array_merge(
            $this->preLoads,
            $this->parseWithRelation($relation)
        );

        return $this;
    }

    public function preLoadResult(mixed $result): mixed
    {
        [$result, $type, $collectionType] = $this->conversionToEntitys($result);
        if ($type) {
            $result = $this->preLoadRelation($result);
            if ('entity' === $type) {
                $result = reset($result);
            } elseif ('collection' === $type) {
                $result = new EntityCollection($result, $collectionType[0] ?? null);
            }
        }

        return $result;
    }

    public function findEntity(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity
    {
        $result = $this->select
            ->if(\is_int($idOrCondition) || \is_string($idOrCondition))
            ->where($this->entity->singlePrimaryKey(), '=', $idOrCondition)
            ->elif($idOrCondition instanceof \Closure || \is_array($idOrCondition))
            ->where($idOrCondition)
            ->fi()
            ->setColumns($column)
            ->findOne()
        ;

        // @phpstan-ignore-next-line
        return $this->normalizeSelectResult($result);
    }

    public function findMany(null|array|\Closure $idsOrCondition = null, array $column = ['*']): EntityCollection
    {
        // @todo 需要删除掉，这里设计很别扭，空直接抛出异常
        if (\is_array($idsOrCondition) && empty($idsOrCondition)) {
            return $this->entity->collection();
        }

        $result = $this->select
            ->if(\is_array($idsOrCondition))
            ->whereIn($this->entity->singlePrimaryKey(), $idsOrCondition)
            ->elif($idsOrCondition instanceof \Closure)
            ->where($idsOrCondition)
            ->fi()
            ->setColumns($column)
            ->findAll()
        ;

        // @phpstan-ignore-next-line
        return $this->normalizeSelectResult($result);
    }

    public function findOrFail(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity
    {
        // 没有查询主键字段，自动补上主键自动
        $singlePrimaryKey = $this->entity->singlePrimaryKey();
        if (!\in_array('*', $column, true) && !\in_array($singlePrimaryKey, $column, true)) {
            $column[] = $singlePrimaryKey;
        }

        $result = $this->findEntity($idOrCondition, $column);
        if (null !== $result->prop($singlePrimaryKey)) {
            return $result;
        }

        throw (new EntityNotFoundException())->setEntity($this->entity::class);
    }

    protected function callSelectMethodChain(string $method, mixed ...$args): self
    {
        $this->select->{$method}(...$args);

        return $this;
    }

    protected function callSelectMethod(string $method, mixed ...$args): mixed
    {
        $result = $this->select->{$method}(...$args);

        return $this->normalizeSelectResult($result);
    }

    /**
     * 初始化查询.
     */
    protected function initSelect(int $softDeletedType): void
    {
        $this->select = $this->entity->selectCollection($softDeletedType);
    }

    /**
     * 预载入实体.
     */
    protected function preLoadRelation(array $entities): array
    {
        foreach ($this->preLoads as $name => $relationScope) {
            if (!str_contains($name, '.')) {
                $entities = $this->loadRelation($entities, $name, $relationScope);
            }
        }

        return $entities;
    }

    /**
     * 取得关联实体.
     */
    protected function getRelation(string $name, null|array|\Closure|string $relationScope = null): Relation
    {
        $relation = Relation::withoutRelationCondition(function () use ($name, $relationScope): Relation {
            return $this->entity->relation($name, $relationScope);
        });

        $nested = $this->nestedRelation($name);
        if (\count($nested) > 0) {
            $relation->getSelect()->eager($nested);
        }

        return $relation;
    }

    /**
     * 尝试取得嵌套关联.
     */
    protected function nestedRelation(string $relation): array
    {
        $nested = [];
        foreach ($this->preLoads as $name => $relationScope) {
            if ($this->isNested($name, $relation)) {
                $nested[substr($name, \strlen($relation.'.'))] = $relationScope;
            }
        }

        return $nested;
    }

    /**
     * 判断是否存在嵌套关联.
     */
    protected function isNested(string $name, string $relation): bool
    {
        return str_contains($name, '.') && str_starts_with($name, $relation.'.');
    }

    /**
     * 格式化预载入关联.
     */
    protected function parseWithRelation(array $relation): array
    {
        $data = [];
        foreach ($relation as $name => $relationScope) {
            if (\is_int($name)) {
                [$name, $relationScope] = [$relationScope, null];
            }

            $data = $this->parseNestedWith($name, $data);
            $data[$name] = $relationScope;
        }

        return $data;
    }

    /**
     * 解析嵌套关联.
     */
    protected function parseNestedWith(string $name, array $result): array
    {
        $progress = [];
        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;
            if (!isset($result[$last = implode('.', $progress)])) {
                $result[$last] = null;
            }
        }

        return $result;
    }

    /**
     * 转换结果到实体类型.
     */
    protected function conversionToEntitys(mixed $result): array
    {
        $type = '';
        $collectionType = [];
        if ($result instanceof EntityCollection) {
            $data = [];
            foreach ($result as $entity) {
                $data[] = $entity;
            }
            $collectionType = $result->getValueTypes();
            $result = $data;
            $type = 'collection';
        } elseif ($result instanceof Entity) {
            $result = [$result];
            $type = 'entity';
        }

        return [$result, $type, $collectionType];
    }

    /**
     * 关联数据设置到实体上.
     */
    protected function loadRelation(array $entities, string $name, null|array|\Closure|string $relationScope = null): array
    {
        $relation = $this->getRelation($name, $relationScope);
        $relation->preLoadCondition($entities);

        return $relation->matchPreLoad($entities, $relation->getPreLoad(), $name);
    }

    /**
     * 整理查询结果.
     */
    protected function normalizeSelectResult(mixed $result): mixed
    {
        if ($result instanceof DatabaseSelect) {
            return $this;
        }

        if (false === static::$preLoadsResult || !$this->preLoads) {
            return $result;
        }

        if ($result instanceof Page) {
            $result->setData($this->preLoadResult($result->getData()));

            return $result;
        }

        return $this->preLoadResult($result);
    }
}
