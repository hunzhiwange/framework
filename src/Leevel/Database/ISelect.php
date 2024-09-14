<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Support\Collection;

/**
 * 数据库查询器接口.
 */
interface ISelect extends ICondition, IBaseDatabase
{
    public function forPage(int $page, int $perPage = 10): self;

    public function time(string $type = 'date'): self;

    public function endTime(): self;

    public function reset(?string $config = null): self;

    public function comment(string $comment): self;

    public function prefix(string $prefix): self;

    public function table(array|\Closure|ICondition|self|string $table, array|string $cols = '*'): self;

    public function columns(array|string $cols = '*', ?string $table = null): self;

    public function setColumns(array|string $cols = '*', ?string $table = null): self;

    public function field(array|string $cols = '*', ?string $table = null): self;

    public function middlewares(string ...$middlewares): self;

    public function where(mixed ...$cond): self;

    public function orWhere(mixed ...$cond): self;

    public function whereRaw(string $raw): self;

    public function orWhereRaw(string $raw): self;

    public function whereExists(mixed $exists): self;

    public function whereNotExists(mixed $exists): self;

    public function whereBetween(mixed ...$cond): self;

    public function whereNotBetween(mixed ...$cond): self;

    public function whereNull(mixed ...$cond): self;

    public function whereNotNull(mixed ...$cond): self;

    public function whereIn(mixed ...$cond): self;

    public function whereNotIn(mixed ...$cond): self;

    public function whereLike(mixed ...$cond): self;

    public function whereNotLike(mixed ...$cond): self;

    public function whereDate(mixed ...$cond): self;

    public function whereDay(mixed ...$cond): self;

    public function whereMonth(mixed ...$cond): self;

    public function whereYear(mixed ...$cond): self;

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): self;

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): self;

    public function ignoreIndex(array|string $indexes): self;

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self;

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self;

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self;

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self;

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self;

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self;

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self;

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): self;

    public function unionAll(array|callable|ICondition|self|string $selects): self;

    public function groupBy(array|string $expression): self;

    public function having(mixed ...$cond): self;

    public function orHaving(mixed ...$cond): self;

    public function havingRaw(string $raw): self;

    public function orHavingRaw(string $raw): self;

    public function havingBetween(mixed ...$cond): self;

    public function havingNotBetween(mixed ...$cond): self;

    public function havingNull(mixed ...$cond): self;

    public function havingNotNull(mixed ...$cond): self;

    public function havingIn(mixed ...$cond): self;

    public function havingNotIn(mixed ...$cond): self;

    public function havingLike(mixed ...$cond): self;

    public function havingNotLike(mixed ...$cond): self;

    public function havingDate(mixed ...$cond): self;

    public function havingDay(mixed ...$cond): self;

    public function havingMonth(mixed ...$cond): self;

    public function havingYear(mixed ...$cond): self;

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): self;

    public function latest(string $field = 'create_at'): self;

    public function oldest(string $field = 'create_at'): self;

    public function distinct(bool $flag = true): self;

    public function count(string $field = '*', string $alias = 'row_count'): self;

    public function avg(string $field, string $alias = 'avg_value'): self;

    public function max(string $field, string $alias = 'max_value'): self;

    public function min(string $field, string $alias = 'min_value'): self;

    public function sum(string $field, string $alias = 'sum_value'): self;

    public function one(): self;

    public function all(): self;

    public function top(int $count = 30): self;

    public function limit(int $offset = 0, int $count = 0): self;

    public function forUpdate(bool $flag = true): self;

    public function lockShare(bool $flag = true): self;

    public function resetBindParams(array $bindParams = []): self;

    public function setBindParamsPrefix(string $bindParamsPrefix): self;

    public function if(mixed $condition): self;

    public function elif(mixed $condition): self;

    public function else(): self;

    public function fi(): self;

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
    public function master(bool $master = false): self;

    /**
     * 设置以某种包装返会结果.
     */
    public function asSome(?\Closure $asSome = null, array $args = []): self;

    /**
     * 设置返会结果为数组.
     */
    public function asArray(?\Closure $asArray = null): self;

    /**
     * 设置是否以集合返回.
     */
    public function asCollection(bool $asCollection = true, array $valueTypes = []): self;

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
    public function findAll(?\Closure $condition = null): array|Collection|EntityCollection;

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
    public function findList(mixed $fieldValue, ?string $fieldKey = null, ?\Closure $condition = null): array;

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
    public function findCount(string $field = '*', ?\Closure $condition = null): int;

    /**
     * 平均数.
     */
    public function findAvg(string $field): mixed;

    /**
     * 最大值.
     */
    public function findMax(string $field): mixed;

    /**
     * 最小值.
     */
    public function findMin(string $field): mixed;

    /**
     * 合计.
     */
    public function findSum(string $field): mixed;

    /**
     * 分页列表查询.
     */
    public function findPageList(int $currentPage, int $perPage = 10, ?int $count = null, array $config = [], ?\Closure $condition = null): Page;

    /**
     * 创建一个无限数据的分页查询.
     */
    public function findPageMacro(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page;

    /**
     * 创建一个只有上下页的分页查询.
     */
    public function findPagePrevNext(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page;

    /**
     * 设置查询缓存.
     */
    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): self;
}
