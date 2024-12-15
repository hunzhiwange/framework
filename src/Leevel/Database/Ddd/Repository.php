<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Cache\ICache;
use Leevel\Database\Condition;
use Leevel\Database\Database;
use Leevel\Database\ICondition;
use Leevel\Database\IDatabase;
use Leevel\Database\ISelect as IDatabaseSelect;
use Leevel\Database\Page;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Server\Pool\Pool as BasePool;
use Leevel\Support\Collection;
use Swoole\Coroutine;
use Swoole\Coroutine\WaitGroup;

/**
 * 仓储.
 */
class Repository implements ISelect
{
    /**
     * 批量插入数据事件.
     */
    public const string INSERT_ALL_EVENT = 'database.repository.insertall';

    /**
     * 批量插入回调.
     */
    protected ?\Closure $insertAllBoot = null;

    /**
     * 实体软删除数据类型.
     */
    protected int $softDeletedType = Entity::WITHOUT_SOFT_DELETED;

    /**
     * 实体查询条件.
     */
    protected ?\Closure $selectCondition = null;

    /**
     * 构造函数.
     */
    public function __construct(protected Entity $entity, protected ?IDispatch $dispatch = null) {}

    public function entity(): Entity
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public static function withoutPreLoadsResult(\Closure $call): mixed
    {
        return Select::withoutPreLoadsResult($call);
    }

    public function withSoftDeleted(): Select
    {
        $select = $this->callSelectMethodChain(__FUNCTION__);
        $this->callSelectCondition($select);

        return $select;
    }

    public function onlySoftDeleted(): Select
    {
        $select = $this->callSelectMethodChain(__FUNCTION__);
        $this->callSelectCondition($select);

        return $select;
    }

    public function eager(array $relation): Select
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

    public function forPage(int $page, int $perPage = 10): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $page, $perPage);
    }

    public function time(string $type = 'date'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $type);
    }

    public function endTime(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function reset(?string $config = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $config);
    }

    public function comment(string $comment): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $comment);
    }

    public function prefix(string $prefix): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $prefix);
    }

    public function table(array|\Closure|ICondition|IDatabaseSelect|string $table, array|string $cols = '*'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols);
    }

    public function columns(array|string $cols = '*', ?string $table = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function setColumns(array|string $cols = '*', ?string $table = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function field(array|string $cols = '*', ?string $table = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function middlewares(string ...$middlewares): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$middlewares);
    }

    public function where(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orWhere(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orWhereRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function whereExists(mixed $exists): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereNotExists(mixed $exists): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDate(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDay(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereMonth(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereYear(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $names, $value, $dataType);
    }

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes, $type);
    }

    public function ignoreIndex(array|string $indexes): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes);
    }

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects, $type);
    }

    public function unionAll(array|callable|ICondition|self|string $selects): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects);
    }

    public function groupBy(array|string $expression): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression);
    }

    public function having(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orHaving(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orHavingRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function havingBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDate(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDay(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingMonth(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingYear(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression, $orderDefault);
    }

    public function latest(string $field = 'create_at'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function oldest(string $field = 'create_at'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function distinct(bool $flag = true): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function count(string $field = '*', string $alias = 'row_count'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function avg(string $field, string $alias = 'avg_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function max(string $field, string $alias = 'max_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function min(string $field, string $alias = 'min_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function sum(string $field, string $alias = 'sum_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function one(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function all(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function top(int $count = 30): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $count);
    }

    public function limit(int $offset = 0, int $count = 0): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $offset, $count);
    }

    public function forUpdate(bool $flag = true): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function lockShare(bool $flag = true): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function resetBindParams(array $bindParams = []): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParams);
    }

    public function setBindParamsPrefix(string $bindParamsPrefix): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParamsPrefix);
    }

    public function if(mixed $condition): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function elif(mixed $condition): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function else(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function fi(): Select
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

    public function master(bool $master = false): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $master);
    }

    public function asSome(?\Closure $asSome = null, array $args = []): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asSome, $args);
    }

    public function asArray(?\Closure $asArray = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asArray);
    }

    public function asCollection(bool $asCollection = true, array $valueTypes = []): Select
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
        return $this->callSelectMethodCondition(__FUNCTION__, $condition);
    }

    public function find(?int $num = null, ?\Closure $condition = null): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethodCondition(__FUNCTION__, $num, $condition);
    }

    public function value(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethodCondition(__FUNCTION__, $field, $condition);
    }

    public function findList(mixed $fieldValue, ?string $fieldKey = null, ?\Closure $condition = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethodCondition(__FUNCTION__, $fieldValue, $fieldKey, $condition);
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
        return $this->callSelectMethodCondition(__FUNCTION__, $field, $condition);
    }

    public function findAvg(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethodCondition(__FUNCTION__, $field, $condition);
    }

    public function findMax(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethodCondition(__FUNCTION__, $field, $condition);
    }

    public function findMin(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethodCondition(__FUNCTION__, $field, $condition);
    }

    public function findSum(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethodCondition(__FUNCTION__, $field, $condition);
    }

    public function findPageList(int $currentPage, int $perPage = 10, ?int $count = null, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethodCondition(__FUNCTION__, $currentPage, $perPage, $count, $config, $condition);
    }

    public function findPageMacro(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethodCondition(__FUNCTION__, $currentPage, $perPage, $config, $condition);
    }

    public function findPagePrevNext(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethodCondition(__FUNCTION__, $currentPage, $perPage, $config, $condition);
    }

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $name, $expire, $cache);
    }

    /**
     * 设置批量插入回调.
     */
    public function setInsertAllBoot(?\Closure $boot = null): void
    {
        $this->insertAllBoot = $boot;
    }

    /**
     * 获取批量插入回调.
     */
    public function getInsertAllBoot(): ?\Closure
    {
        return $this->insertAllBoot;
    }

    public function insertAll(array $data, array $bind = [], array|bool $replace = false): int|string
    {
        if ($this->dispatch) {
            $this->dispatch->handle(
                self::INSERT_ALL_EVENT,
                $this,
            );
        }

        if ($this->insertAllBoot) {
            $insertAllBoot = $this->insertAllBoot;
            $insertAllBoot($this, $data, $bind, $replace);
        }

        return $this->getEntitySelect()->insertAll($data, $bind, $replace);
    }

    /**
     * 分页查询.
     */
    public function findPage(int $currentPage, int $perPage = 10, string $column = '*', array $config = [], ?\Closure $condition = null): Page
    {
        if (!$this
            ->getEntitySelect()
            ->getContainer()
            ->enabledCoroutine()) {
            $count = $this->findCount($column, $condition);
            $page = $this->findPageList($currentPage, $perPage, null, $config, $condition);
            $page->setTotalRecord($count);

            return $page;
        }

        // 协程模式
        $wg = new WaitGroup();
        $exceptions = [];

        // 启动一个协程
        $wg->add();
        $count = 0;
        Coroutine::create(function () use ($wg, $condition, $column, &$count, &$exceptions): void {
            Container::parentContext();

            try {
                $count = $this->findCount($column, $condition);
            } catch (\Throwable $e) {
                $exceptions[] = $e;
            }

            // 标记协程完成
            $wg->done();
        });

        // 启动一个协程
        $wg->add();
        $page = null;
        Coroutine::create(function () use ($wg, $condition, $currentPage, $perPage, $config, &$page, &$exceptions): void {
            Container::parentContext();

            try {
                $page = $this->findPageList($currentPage, $perPage, null, $config, $condition);
            } catch (\Throwable $e) {
                $exceptions[] = $e;
            }

            // 标记协程完成
            $wg->done();
        });

        // 挂起当前协程，等待所有任务完成后恢复
        $wg->wait();

        if ($exceptions) {
            throw $exceptions[0];
        }

        $page->setTotalRecord($count);

        return $page;
    }

    /**
     * 判断指定数组数据值是否存在.
     *
     * @throws DataNotFoundException
     */
    public function validateDataExists(array $data, ?string $field = null, string $countField = '*', ?\Closure $condition = null): void
    {
        if (!$field) {
            $field = $this->entity->getSinglePrimaryKey();
        }

        $select = $this->getEntitySelect();
        $select->whereIn($field, $data = array_values(array_unique($data)));
        $count = $select->findCount($countField, $condition);
        if ($count === \count($data)) {
            return;
        }

        throw new DataNotFoundException(sprintf('Data of `%s` was not found.', \get_class($this->entity)));
    }

    /**
     * 返回基础查询.
     */
    public function getEntitySelect(): Select
    {
        return $this->entity->select($this->softDeletedType);
    }

    /**
     * 新增实体.
     */
    public function createEntity(Entity $entity): int|string
    {
        return $entity->create()->flush();
    }

    /**
     * 更新实体.
     */
    public function updateEntity(Entity $entity): int|string
    {
        return $entity->update()->flush();
    }

    /**
     * 替换实体.
     */
    public function replaceEntity(Entity $entity): int|string
    {
        return $entity->replace()->flush();
    }

    /**
     * 响应删除.
     */
    public function deleteEntity(Entity $entity, bool $forceDelete = false): int|string
    {
        return $entity->delete($forceDelete)->flush();
    }

    /**
     * 强制删除实体.
     */
    public function forceDeleteEntity(Entity $entity): int|string
    {
        return $entity->delete(true)->flush();
    }

    /**
     * 从数据库重新读取当前对象的属性.
     */
    public function refreshEntity(Entity $entity, array|string $cols = '*'): void
    {
        $entity->refresh($cols);
    }

    /**
     * 返回实体.
     */
    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * 实体查询条件.
     */
    public function selectCondition(\Closure $selectCondition): self
    {
        $this->selectCondition = $selectCondition;

        return $this;
    }

    /**
     * 实体查询包含软删除数据的实体查询对象.
     */
    public function selectWithSoftDeleted(): self
    {
        $this->softDeletedType = Entity::WITH_SOFT_DELETED;

        return $this;
    }

    /**
     * 实体查询仅仅包含软删除数据的实体查询对象.
     */
    public function selectOnlySoftDeleted(): self
    {
        $this->softDeletedType = Entity::ONLY_SOFT_DELETED;

        return $this;
    }

    protected function callSelectMethodChain(string $method, mixed ...$args): Select
    {
        // @phpstan-ignore-next-line
        return $this->getEntitySelect()->{$method}(...$args);
    }

    protected function callSelectMethod(string $method, mixed ...$args): mixed
    {
        return $this->getEntitySelect()->{$method}(...$args);
    }

    protected function callSelectMethodCondition(string $method, mixed ...$args): mixed
    {
        $select = $this->getEntitySelect();
        $this->callSelectCondition($select);

        return $select->{$method}(...$args);
    }

    protected function callSelectCondition(Select $select): void
    {
        if (!$this->selectCondition) {
            return;
        }

        $selectCondition = $this->selectCondition;
        $selectCondition($select);
    }
}
