<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Support\Collection;

/**
 * 数据库查询器接口.
 */
interface ISelect extends ICondition
{
    public function forPage(int $page, int $perPage = 10): Select;

    public function time(string $type = 'date'): Select;

    public function endTime(): Select;

    public function reset(?string $config = null): Select;

    public function comment(string $comment): Select;

    public function prefix(string $prefix): Select;

    public function table(array|\Closure|ICondition|self|string $table, array|string $cols = '*'): Select;

    public function columns(array|string $cols = '*', ?string $table = null): Select;

    public function setColumns(array|string $cols = '*', ?string $table = null): Select;

    public function field(array|string $cols = '*', ?string $table = null): Select;

    public function middlewares(string ...$middlewares): Select;

    public function where(mixed ...$cond): Select;

    public function orWhere(mixed ...$cond): Select;

    public function whereRaw(string $raw): Select;

    public function orWhereRaw(string $raw): Select;

    public function whereExists(mixed $exists): Select;

    public function whereNotExists(mixed $exists): Select;

    public function whereBetween(mixed ...$cond): Select;

    public function whereNotBetween(mixed ...$cond): Select;

    public function whereNull(mixed ...$cond): Select;

    public function whereNotNull(mixed ...$cond): Select;

    public function whereIn(mixed ...$cond): Select;

    public function whereNotIn(mixed ...$cond): Select;

    public function whereLike(mixed ...$cond): Select;

    public function whereNotLike(mixed ...$cond): Select;

    public function whereDate(mixed ...$cond): Select;

    public function whereDay(mixed ...$cond): Select;

    public function whereMonth(mixed ...$cond): Select;

    public function whereYear(mixed ...$cond): Select;

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): Select;

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): Select;

    public function ignoreIndex(array|string $indexes): Select;

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select;

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select;

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select;

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select;

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select;

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select;

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select;

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): Select;

    public function unionAll(array|callable|ICondition|self|string $selects): Select;

    public function groupBy(array|string $expression): Select;

    public function having(mixed ...$cond): Select;

    public function orHaving(mixed ...$cond): Select;

    public function havingRaw(string $raw): Select;

    public function orHavingRaw(string $raw): Select;

    public function havingBetween(mixed ...$cond): Select;

    public function havingNotBetween(mixed ...$cond): Select;

    public function havingNull(mixed ...$cond): Select;

    public function havingNotNull(mixed ...$cond): Select;

    public function havingIn(mixed ...$cond): Select;

    public function havingNotIn(mixed ...$cond): Select;

    public function havingLike(mixed ...$cond): Select;

    public function havingNotLike(mixed ...$cond): Select;

    public function havingDate(mixed ...$cond): Select;

    public function havingDay(mixed ...$cond): Select;

    public function havingMonth(mixed ...$cond): Select;

    public function havingYear(mixed ...$cond): Select;

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): Select;

    public function latest(string $field = 'create_at'): Select;

    public function oldest(string $field = 'create_at'): Select;

    public function distinct(bool $flag = true): Select;

    public function count(string $field = '*', string $alias = 'row_count'): Select;

    public function avg(string $field, string $alias = 'avg_value'): Select;

    public function max(string $field, string $alias = 'max_value'): Select;

    public function min(string $field, string $alias = 'min_value'): Select;

    public function sum(string $field, string $alias = 'sum_value'): Select;

    public function one(): Select;

    public function all(): Select;

    public function top(int $count = 30): Select;

    public function limit(int $offset = 0, int $count = 0): Select;

    public function forUpdate(bool $flag = true): Select;

    public function lockShare(bool $flag = true): Select;

    public function resetBindParams(array $bindParams = []): Select;

    public function setBindParamsPrefix(string $bindParamsPrefix): Select;

    public function if(mixed $condition): Select;

    public function elif(mixed $condition): Select;

    public function else(): Select;

    public function fi(): Select;

    /**
     * 查询对象.
     */
    public function databaseCondition(): Condition;

    /**
     * 返回数据库连接对象.
     */
    public function databaseConnect(): IDatabase;

    /**
     * 设置是否查询主服务器.
     */
    public function master(bool $master = false): Select;

    /**
     * 设置以某种包装返会结果.
     */
    public function asSome(?\Closure $asSome = null, array $args = []): Select;

    /**
     * 设置返会结果为数组.
     */
    public function asArray(?\Closure $asArray = null): Select;

    /**
     * 设置是否以集合返回.
     */
    public function asCollection(bool $asCollection = true, array $valueTypes = []): Select;

    /**
     * 原生 SQL 查询数据.
     */
    public function select(null|callable|self|string $data = null, array $bind = []): mixed;

    /**
     * 插入数据 insert (支持原生 SQL).
     */
    public function insert(array|string $data, array $bind = [], array|bool $replace = false): int|string;

    /**
     * 批量插入数据 insertAll.
     */
    public function insertAll(array $data, array $bind = [], array|bool $replace = false): int|string;

    /**
     * 更新数据 update (支持原生 SQL).
     */
    public function update(array|string $data, array $bind = []): int;

    /**
     * 更新某个字段的值.
     */
    public function updateColumn(string $column, mixed $value, array $bind = []): int;

    /**
     * 字段递增.
     */
    public function updateIncrease(string $column, int $step = 1, array $bind = []): int;

    /**
     * 字段减少.
     */
    public function updateDecrease(string $column, int $step = 1, array $bind = []): int;

    /**
     * 删除数据 delete (支持原生 SQL).
     */
    public function delete(?string $data = null, array $bind = []): int;

    /**
     * 清空表重置自增 ID.
     */
    public function truncate(): array|int;

    /**
     * 返回一条记录.
     */
    public function findOne(): mixed;

    /**
     * 以数组返回所有记录.
     */
    public function findArray(): array;

    /**
     * 以数组返回所有记录（每一项也为数组）.
     */
    public function findAsArray(): array;

    /**
     * 以集合返回所有记录.
     */
    public function findCollection(): Collection|EntityCollection;

    /**
     * 返回所有记录.
     */
    public function findAll(): array|Collection|EntityCollection;

    /**
     * 返回最后几条记录.
     */
    public function find(?int $num = null): array|Collection|EntityCollection;

    /**
     * 返回一个字段的值.
     */
    public function value(string $field): mixed;

    /**
     * 返回一列数据.
     */
    public function list(mixed $fieldValue, ?string $fieldKey = null): array;

    /**
     * 数据分块处理.
     */
    public function chunk(int $count, \Closure $chunk): void;

    /**
     * 数据分块处理依次回调.
     */
    public function each(int $count, \Closure $each): void;

    /**
     * 总记录数.
     */
    public function findCount(string $field = '*', string $alias = 'row_count'): int;

    /**
     * 平均数.
     */
    public function findAvg(string $field, string $alias = 'avg_value'): mixed;

    /**
     * 最大值.
     */
    public function findMax(string $field, string $alias = 'max_value'): mixed;

    /**
     * 最小值.
     */
    public function findMin(string $field, string $alias = 'min_value'): mixed;

    /**
     * 合计.
     */
    public function findSum(string $field, string $alias = 'sum_value'): mixed;

    /**
     * 分页查询.
     */
    public function page(int $currentPage, int $perPage = 10, ?int $count = null, array $config = []): Page;

    /**
     * 创建一个无限数据的分页查询.
     */
    public function pageMacro(int $currentPage, int $perPage = 10, array $config = []): Page;

    /**
     * 创建一个只有上下页的分页查询.
     */
    public function pagePrevNext(int $currentPage, int $perPage = 10, array $config = []): Page;

    /**
     * 设置查询缓存.
     */
    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): Select;

    /**
     * 查询单条记录获得结果.
     */
    public function queryOne(): mixed;

    /**
     * 查询多条记录获得结果.
     */
    public function queryAll(): array|Collection|EntityCollection;
}
