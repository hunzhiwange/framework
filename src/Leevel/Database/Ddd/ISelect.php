<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Cache\ICache;
use Leevel\Database\ICondition;
use Leevel\Database\ISelect as IBaseSelect;

/**
 * 实体查询接口.
 */
interface ISelect extends IBaseSelect
{
    public function forPage(int $page, int $perPage = 10): self;

    public function time(string $type = 'date'): self;

    public function endTime(): self;

    public function reset(?string $config = null): self;

    public function comment(string $comment): self;

    public function prefix(string $prefix): self;

    public function table(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols = '*'): self;

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

    public function join(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): self;

    public function innerJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): self;

    public function leftJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): self;

    public function rightJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): self;

    public function fullJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): self;

    public function crossJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): self;

    public function naturalJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): self;

    public function union(array|callable|IBaseSelect|ICondition|string $selects, string $type = 'UNION'): self;

    public function unionAll(array|callable|IBaseSelect|ICondition|string $selects): self;

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

    public function master(bool $master = false): self;

    public function asSome(?\Closure $asSome = null, array $args = []): self;

    public function asArray(?\Closure $asArray = null): self;

    public function asCollection(bool $asCollection = true, array $valueTypes = []): self;

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): self;

    /**
     * 获取实体.
     */
    public function entity(): Entity;

    /**
     * 获取不执行预载入的查询结果.
     */
    public static function withoutPreLoadsResult(\Closure $call): mixed;

    /**
     * 包含软删除数据的实体查询对象.
     *
     * - 获取包含软删除的数据.
     * - 会覆盖查询条件，需要首先调用.
     *
     * @todo remove this(注意关联模型)
     */
    public function withSoftDeleted(): self;

    /**
     * 仅仅包含软删除数据的实体查询对象.
     *
     * - 获取只包含软删除的数据.
     * - 会覆盖查询条件，需要首先调用.
     *
     * @todo remove this(注意关联模型)
     */
    public function onlySoftDeleted(): self;

    /**
     * 添加预载入关联查询.
     */
    public function eager(array $relation): self;

    /**
     * 尝试解析结果预载.
     */
    public function preLoadResult(mixed $result): mixed;

    /**
     * 通过主键或条件查找实体.
     */
    public function findEntity(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity;

    /**
     * 通过主键或条件查找多个实体.
     */
    public function findMany(null|array|\Closure $idsOrCondition = null, array $column = ['*']): EntityCollection;

    /**
     * 通过主键或条件查找实体，未找到则抛出异常.
     */
    public function findOrFail(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity;
}
