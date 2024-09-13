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
    public function forPage(int $page, int $perPage = 10): Select;

    public function time(string $type = 'date'): Select;

    public function endTime(): Select;

    public function reset(?string $config = null): Select;

    public function comment(string $comment): Select;

    public function prefix(string $prefix): Select;

    public function table(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols = '*'): Select;

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

    public function join(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): Select;

    public function innerJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): Select;

    public function leftJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): Select;

    public function rightJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): Select;

    public function fullJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): Select;

    public function crossJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): Select;

    public function naturalJoin(array|\Closure|IBaseSelect|ICondition|string $table, array|string $cols, mixed ...$cond): Select;

    public function union(array|callable|IBaseSelect|ICondition|string $selects, string $type = 'UNION'): Select;

    public function unionAll(array|callable|IBaseSelect|ICondition|string $selects): Select;

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

    public function master(bool $master = false): Select;

    public function asSome(?\Closure $asSome = null, array $args = []): Select;

    public function asArray(?\Closure $asArray = null): Select;

    public function asCollection(bool $asCollection = true, array $valueTypes = []): Select;

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): Select;

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
