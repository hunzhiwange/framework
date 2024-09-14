<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Database\Ddd\ISelect;
use Leevel\Database\Ddd\Select;
use Leevel\Database\ICondition;
use Leevel\Database\ISelect as IBaseSelect;

/**
 * 关联实体接口.
 */
interface IRelation extends ISelect
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
     * 返回查询.
     */
    public function getSelect(): Select;

    /**
     * 取得预载入关联实体.
     */
    public function getPreLoad(): mixed;

    /**
     * 取得关联目标实体.
     */
    public function getTargetEntity(): Entity;

    /**
     * 取得源实体.
     */
    public function getSourceEntity(): Entity;

    /**
     * 取得目标字段.
     */
    public function getTargetKey(): string;

    /**
     * 取得源字段.
     */
    public function getSourceKey(): string;

    /**
     * 获取不带关联条件的关联对象.
     */
    public static function withoutRelationCondition(\Closure $call): self;

    /**
     * 取回源实体对应数据.
     */
    public function getSourceValue(): mixed;

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition(): void;

    /**
     * 设置预载入关联查询条件.
     */
    public function preLoadCondition(array $entities): void;

    /**
     * 匹配关联查询数据到实体.
     */
    public function matchPreLoad(array $entities, EntityCollection $result, string $relation): array;

    /**
     * 查询关联对象.
     */
    public function sourceQuery(): mixed;
}
