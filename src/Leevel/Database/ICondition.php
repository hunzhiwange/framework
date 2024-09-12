<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Support\IFlowControl;

/**
 * 条件构造器接口.
 */
interface ICondition extends IFlowControl
{
    /**
     * 根据分页设置条件.
     */
    public function forPage(int $page, int $perPage = 10): self;

    /**
     * 时间控制语句开始.
     */
    public function time(string $type = 'date'): self;

    /**
     * 时间控制语句结束.
     */
    public function endTime(): self;

    /**
     * 重置查询条件.
     */
    public function reset(?string $config = null): self;

    /**
     * 查询注释.
     */
    public function comment(string $comment): self;

    /**
     * prefix 查询.
     */
    public function prefix(string $prefix): self;

    /**
     * 添加一个要查询的表及其要查询的字段.
     */
    public function table(array|\Closure|Select|self|string $table, array|string $cols = '*'): self;

    /**
     * 添加字段.
     */
    public function columns(array|string $cols = '*', ?string $table = null): self;

    /**
     * 设置字段.
     */
    public function setColumns(array|string $cols = '*', ?string $table = null): self;

    /**
     * 设置字段别名方法.
     */
    public function field(array|string $cols = '*', ?string $table = null): self;

    /**
     * 查询中间件.
     */
    public function middlewares(string ...$middlewares): self;

    /**
     * where 查询条件.
     */
    public function where(mixed ...$cond): self;

    /**
     * orWhere 查询条件.
     */
    public function orWhere(mixed ...$cond): self;

    /**
     * Where 原生查询.
     */
    public function whereRaw(string $raw): self;

    /**
     * Where 原生 OR 查询.
     */
    public function orWhereRaw(string $raw): self;

    /**
     * exists 方法支持
     */
    public function whereExists(mixed $exists): self;

    /**
     * not exists 方法支持
     */
    public function whereNotExists(mixed $exists): self;

    /**
     * whereBetween 查询条件.
     */
    public function whereBetween(mixed ...$cond): self;

    /**
     * whereNotBetween 查询条件.
     */
    public function whereNotBetween(mixed ...$cond): self;

    /**
     * whereNull 查询条件.
     */
    public function whereNull(mixed ...$cond): self;

    /**
     * whereNotNull 查询条件.
     */
    public function whereNotNull(mixed ...$cond): self;

    /**
     * whereIn 查询条件.
     */
    public function whereIn(mixed ...$cond): self;

    /**
     * whereNotIn 查询条件.
     */
    public function whereNotIn(mixed ...$cond): self;

    /**
     * whereLike 查询条件.
     */
    public function whereLike(mixed ...$cond): self;

    /**
     * whereNotLike 查询条件.
     */
    public function whereNotLike(mixed ...$cond): self;

    /**
     * whereDate 查询条件.
     */
    public function whereDate(mixed ...$cond): self;

    /**
     * whereDay 查询条件.
     */
    public function whereDay(mixed ...$cond): self;

    /**
     * whereMonth 查询条件.
     */
    public function whereMonth(mixed ...$cond): self;

    /**
     * whereYear 查询条件.
     */
    public function whereYear(mixed ...$cond): self;

    /**
     * 参数绑定支持.
     */
    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): self;

    /**
     * index 强制索引（或者忽略索引）.
     */
    public function forceIndex(array|string $indexes, string $type = 'FORCE'): self;

    /**
     * index 忽略索引.
     */
    public function ignoreIndex(array|string $indexes): self;

    /**
     * join 查询.
     */
    public function join(array|\Closure|Select|self|string $table, array|string $cols, mixed ...$cond): self;

    /**
     * innerJoin 查询.
     */
    public function innerJoin(array|\Closure|Select|self|string $table, array|string $cols, mixed ...$cond): self;

    /**
     * leftJoin 查询.
     */
    public function leftJoin(array|\Closure|Select|self|string $table, array|string $cols, mixed ...$cond): self;

    /**
     * rightJoin 查询.
     */
    public function rightJoin(array|\Closure|Select|self|string $table, array|string $cols, mixed ...$cond): self;

    /**
     * fullJoin 查询.
     */
    public function fullJoin(array|\Closure|Select|self|string $table, array|string $cols, mixed ...$cond): self;

    /**
     * crossJoin 查询.
     */
    public function crossJoin(array|\Closure|Select|self|string $table, array|string $cols, mixed ...$cond): self;

    /**
     * naturalJoin 查询.
     */
    public function naturalJoin(array|\Closure|Select|self|string $table, array|string $cols, mixed ...$cond): self;

    /**
     * 添加一个 UNION 查询.
     */
    public function union(array|callable|Select|self|string $selects, string $type = 'UNION'): self;

    /**
     * 添加一个 UNION ALL 查询.
     */
    public function unionAll(array|callable|Select|self|string $selects): self;

    /**
     * 指定 GROUP BY 子句.
     */
    public function groupBy(array|string $expression): self;

    /**
     * 添加一个 HAVING 条件.
     *
     * - 参数规范参考 where()方法.
     */
    public function having(mixed ...$cond): self;

    /**
     * orHaving 查询条件.
     */
    public function orHaving(mixed ...$cond): self;

    /**
     * having 原生查询.
     */
    public function havingRaw(string $raw): self;

    /**
     * having 原生 OR 查询.
     */
    public function orHavingRaw(string $raw): self;

    /**
     * havingBetween 查询条件.
     */
    public function havingBetween(mixed ...$cond): self;

    /**
     * havingNotBetween 查询条件.
     */
    public function havingNotBetween(mixed ...$cond): self;

    /**
     * havingNull 查询条件.
     */
    public function havingNull(mixed ...$cond): self;

    /**
     * havingNotNull 查询条件.
     */
    public function havingNotNull(mixed ...$cond): self;

    /**
     * havingIn 查询条件.
     */
    public function havingIn(mixed ...$cond): self;

    /**
     * havingNotIn 查询条件.
     */
    public function havingNotIn(mixed ...$cond): self;

    /**
     * havingLike 查询条件.
     */
    public function havingLike(mixed ...$cond): self;

    /**
     * havingNotLike 查询条件.
     */
    public function havingNotLike(mixed ...$cond): self;

    /**
     * havingDate 查询条件.
     */
    public function havingDate(mixed ...$cond): self;

    /**
     * havingDay 查询条件.
     */
    public function havingDay(mixed ...$cond): self;

    /**
     * havingMonth 查询条件.
     */
    public function havingMonth(mixed ...$cond): self;

    /**
     * havingYear 查询条件.
     */
    public function havingYear(mixed ...$cond): self;

    /**
     * 添加排序.
     */
    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): self;

    /**
     * 最近排序数据.
     */
    public function latest(string $field = 'create_at'): self;

    /**
     * 最早排序数据.
     */
    public function oldest(string $field = 'create_at'): self;

    /**
     * 创建一个 SELECT DISTINCT 查询.
     */
    public function distinct(bool $flag = true): self;

    /**
     * 总记录数.
     */
    public function count(string $field = '*', string $alias = 'row_count'): self;

    /**
     * 平均数.
     */
    public function avg(string $field, string $alias = 'avg_value'): self;

    /**
     * 最大值.
     */
    public function max(string $field, string $alias = 'max_value'): self;

    /**
     * 最小值.
     */
    public function min(string $field, string $alias = 'min_value'): self;

    /**
     * 合计.
     */
    public function sum(string $field, string $alias = 'sum_value'): self;

    /**
     * 指示仅查询第一个符合条件的记录.
     */
    public function one(): self;

    /**
     * 指示查询所有符合条件的记录.
     */
    public function all(): self;

    /**
     * 查询几条记录.
     */
    public function top(int $count = 30): self;

    /**
     * limit 限制条数.
     */
    public function limit(int $offset = 0, int $count = 0): self;

    /**
     * 排它锁 FOR UPDATE 查询.
     */
    public function forUpdate(bool $flag = true): self;

    /**
     * 共享锁 LOCK SHARE 查询.
     */
    public function lockShare(bool $flag = true): self;

    /**
     * 重置参数绑定.
     */
    public function resetBindParams(array $bindParams = []): self;

    /**
     * 设置参数绑定前缀.
     */
    public function setBindParamsPrefix(string $bindParamsPrefix): self;

    /**
     * 获取表别名.
     */
    public function getAlias(): string;

    /**
     * 原生查询.
     */
    public static function raw(string $raw): string;

    /**
     * 注册查询中间件.
     *
     * @todo 命名很怪，也可以改为非工有函数
     */
    public static function registerMiddlewares(array $middlewares, bool $force = false): array;

    /**
     * 获得查询字符串.
     */
    public function makeSql(bool $withLogicGroup = false): string;

    /**
     * 返回参数绑定.
     */
    public function getBindParams(): array;
}
