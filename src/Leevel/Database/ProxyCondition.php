<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database;

use PDO;

/**
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.06.05
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait ProxyCondition
{
    /**
     * 根据分页设置条件.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return \Leevel\Database\Select
     */
    public function forPage(int $page, int $perPage = 15): Select
    {
        $this->proxyCondition()->forPage($page, $perPage);

        return $this->proxyConditionReturn();
    }

    /**
     * 时间控制语句开始.
     *
     * @param string $type
     *
     * @return \Leevel\Database\Select
     */
    public function time(string $type = 'date'): Select
    {
        $this->proxyCondition()->time($type);

        return $this->proxyConditionReturn();
    }

    /**
     * 时间控制语句结束.
     *
     * @return \Leevel\Database\Select
     */
    public function endTime(): Select
    {
        $this->proxyCondition()->endTime();

        return $this->proxyConditionReturn();
    }

    /**
     * 重置查询条件.
     *
     * @param null|string $option
     *
     * @return \Leevel\Database\Select
     */
    public function reset(?string $option = null): Select
    {
        $this->proxyCondition()->reset($option);

        return $this->proxyConditionReturn();
    }

    /**
     * prefix 查询.
     *
     * @param string $prefix
     *
     * @return \Leevel\Database\Select
     */
    public function prefix(string $prefix): Select
    {
        $this->proxyCondition()->prefix($prefix);

        return $this->proxyConditionReturn();
    }

    /**
     * 添加一个要查询的表及其要查询的字段.
     *
     * @param mixed        $table
     * @param array|string $cols
     *
     * @return \Leevel\Database\Select
     */
    public function table($table, $cols = '*'): Select
    {
        $this->proxyCondition()->table($table, $cols);

        return $this->proxyConditionReturn();
    }

    /**
     * 获取表别名.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->proxyCondition()->getAlias();
    }

    /**
     * 添加字段.
     *
     * @param mixed       $cols
     * @param null|string $table
     *
     * @return \Leevel\Database\Select
     */
    public function columns($cols = '*', ?string $table = null): Select
    {
        $this->proxyCondition()->columns($cols, $table);

        return $this->proxyConditionReturn();
    }

    /**
     * 设置字段.
     *
     * @param mixed       $cols
     * @param null|string $table
     *
     * @return \Leevel\Database\Select
     */
    public function setColumns($cols = '*', ?string $table = null): Select
    {
        $this->proxyCondition()->setColumns($cols, $table);

        return $this->proxyConditionReturn();
    }

    /**
     * where 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function where(...$cond): Select
    {
        $this->proxyCondition()->where(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * orWhere 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function orWhere(...$cond): Select
    {
        $this->proxyCondition()->orWhere(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * Where 原生查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Select
     */
    public function whereRaw(string $raw): Select
    {
        $this->proxyCondition()->whereRaw($raw);

        return $this->proxyConditionReturn();
    }

    /**
     * Where 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Select
     */
    public function orWhereRaw(string $raw): Select
    {
        $this->proxyCondition()->orWhereRaw($raw);

        return $this->proxyConditionReturn();
    }

    /**
     * exists 方法支持
     *
     * @param mixed $exists
     *
     * @return \Leevel\Database\Select
     */
    public function whereExists($exists): Select
    {
        $this->proxyCondition()->whereExists($exists);

        return $this->proxyConditionReturn();
    }

    /**
     * not exists 方法支持
     *
     * @param mixed $exists
     *
     * @return \Leevel\Database\Select
     */
    public function whereNotExists($exists): Select
    {
        $this->proxyCondition()->whereNotExists($exists);

        return $this->proxyConditionReturn();
    }

    /**
     * whereBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereBetween(...$cond): Select
    {
        $this->proxyCondition()->whereBetween(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereNotBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereNotBetween(...$cond): Select
    {
        $this->proxyCondition()->whereNotBetween(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereNull(...$cond): Select
    {
        $this->proxyCondition()->whereNull(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereNotNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereNotNull(...$cond): Select
    {
        $this->proxyCondition()->whereNotNull(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereIn(...$cond): Select
    {
        $this->proxyCondition()->whereIn(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereNotIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereNotIn(...$cond): Select
    {
        $this->proxyCondition()->whereNotIn(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereLike(...$cond): Select
    {
        $this->proxyCondition()->whereLike(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereNotLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereNotLike(...$cond): Select
    {
        $this->proxyCondition()->whereNotLike(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereDate 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereDate(...$cond): Select
    {
        $this->proxyCondition()->whereDate(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereDay 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereDay(...$cond): Select
    {
        $this->proxyCondition()->whereDay(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereMonth 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereMonth(...$cond): Select
    {
        $this->proxyCondition()->whereMonth(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * whereYear 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function whereYear(...$cond): Select
    {
        $this->proxyCondition()->whereYear(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * 参数绑定支持
     *
     * @param mixed      $names
     * @param null|mixed $value
     * @param int        $type
     *
     * @return \Leevel\Database\Select
     */
    public function bind($names, $value = null, int $type = PDO::PARAM_STR): Select
    {
        $this->proxyCondition()->bind($names, $value, $type);

        return $this->proxyConditionReturn();
    }

    /**
     * index 强制索引（或者忽略索引）.
     *
     * @param array|string $indexs
     * @param string       $type
     *
     * @return \Leevel\Database\Select
     */
    public function forceIndex($indexs, $type = 'FORCE'): Select
    {
        $this->proxyCondition()->forceIndex($indexs, $type);

        return $this->proxyConditionReturn();
    }

    /**
     * index 忽略索引.
     *
     * @param array|string $indexs
     *
     * @return \Leevel\Database\Select
     */
    public function ignoreIndex($indexs): Select
    {
        $this->proxyCondition()->ignoreIndex($indexs);

        return $this->proxyConditionReturn();
    }

    /**
     * join 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Select
     */
    public function join($table, $cols, ...$cond): Select
    {
        $this->proxyCondition()->join($table, $cols, ...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * innerJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Select
     */
    public function innerJoin($table, $cols, ...$cond): Select
    {
        $this->proxyCondition()->innerJoin($table, $cols, ...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * leftJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Select
     */
    public function leftJoin($table, $cols, ...$cond): Select
    {
        $this->proxyCondition()->leftJoin($table, $cols, ...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * rightJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Select
     */
    public function rightJoin($table, $cols, ...$cond): Select
    {
        $this->proxyCondition()->rightJoin($table, $cols, ...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * fullJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Select
     */
    public function fullJoin($table, $cols, ...$cond): Select
    {
        $this->proxyCondition()->fullJoin($table, $cols, ...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * crossJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Select
     */
    public function crossJoin($table, $cols, ...$cond): Select
    {
        $this->proxyCondition()->crossJoin($table, $cols, ...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * naturalJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Select
     */
    public function naturalJoin($table, $cols, ...$cond): Select
    {
        $this->proxyCondition()->naturalJoin($table, $cols, ...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * 添加一个 UNION 查询.
     *
     * @param array|callable|string $selects
     * @param string                $type
     *
     * @return \Leevel\Database\Select
     */
    public function union($selects, string $type = 'UNION'): Select
    {
        $this->proxyCondition()->union($selects, $type);

        return $this->proxyConditionReturn();
    }

    /**
     * 添加一个 UNION ALL 查询.
     *
     * @param array|callable|string $selects
     *
     * @return \Leevel\Database\Select
     */
    public function unionAll($selects): Select
    {
        $this->proxyCondition()->unionAll($selects);

        return $this->proxyConditionReturn();
    }

    /**
     * 指定 GROUP BY 子句.
     *
     * @param array|string $expression
     *
     * @return \Leevel\Database\Select
     */
    public function groupBy($expression): Select
    {
        $this->proxyCondition()->groupBy($expression);

        return $this->proxyConditionReturn();
    }

    /**
     * 添加一个 HAVING 条件
     * < 参数规范参考 where()方法 >.
     *
     * @param array $data
     *
     * @return \Leevel\Database\Select
     */
    public function having(...$cond): Select
    {
        $this->proxyCondition()->having(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * orHaving 查询条件.
     *
     * @param array $data
     *
     * @return \Leevel\Database\Select
     */
    public function orHaving(...$cond): Select
    {
        $this->proxyCondition()->orHaving(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * Having 原生查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Select
     */
    public function havingRaw(string $raw): Select
    {
        $this->proxyCondition()->havingRaw($raw);

        return $this->proxyConditionReturn();
    }

    /**
     * Having 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Select
     */
    public function orHavingRaw(string $raw): Select
    {
        $this->proxyCondition()->orHavingRaw($raw);

        return $this->proxyConditionReturn();
    }

    /**
     * havingBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingBetween(...$cond): Select
    {
        $this->proxyCondition()->havingBetween(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingNotBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingNotBetween(...$cond): Select
    {
        $this->proxyCondition()->havingNotBetween(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingNull(...$cond): Select
    {
        $this->proxyCondition()->havingNull(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingNotNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingNotNull(...$cond): Select
    {
        $this->proxyCondition()->havingNotNull(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingIn(...$cond): Select
    {
        $this->proxyCondition()->havingIn(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingNotIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingNotIn(...$cond): Select
    {
        $this->proxyCondition()->havingNotIn(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingLike(...$cond): Select
    {
        $this->proxyCondition()->havingLike(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingNotLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingNotLike(...$cond): Select
    {
        $this->proxyCondition()->havingNotLike(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingDate 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingDate(...$cond): Select
    {
        $this->proxyCondition()->havingDate(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingDay 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingDay(...$cond): Select
    {
        $this->proxyCondition()->havingDay(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingMonth 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingMonth(...$cond): Select
    {
        $this->proxyCondition()->havingMonth(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * havingYear 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public function havingYear(...$cond): Select
    {
        $this->proxyCondition()->havingYear(...$cond);

        return $this->proxyConditionReturn();
    }

    /**
     * 添加排序.
     *
     * @param array|string $expression
     * @param string       $orderDefault
     *
     * @return \Leevel\Database\Select
     */
    public function orderBy($expression, string $orderDefault = 'ASC'): Select
    {
        $this->proxyCondition()->orderBy($expression, $orderDefault);

        return $this->proxyConditionReturn();
    }

    /**
     * 最近排序数据.
     *
     * @param string $field
     *
     * @return \Leevel\Database\Select
     */
    public function latest(string $field = 'create_at'): Select
    {
        $this->proxyCondition()->latest($field);

        return $this->proxyConditionReturn();
    }

    /**
     * 最早排序数据.
     *
     * @param string $field
     *
     * @return \Leevel\Database\Select
     */
    public function oldest(string $field = 'create_at'): Select
    {
        $this->proxyCondition()->oldest($field);

        return $this->proxyConditionReturn();
    }

    /**
     * 创建一个 SELECT DISTINCT 查询.
     *
     * @param bool $flag 指示是否是一个 SELECT DISTINCT 查询（默认 true）
     *
     * @return \Leevel\Database\Select
     */
    public function distinct(bool $flag = true): Select
    {
        $this->proxyCondition()->distinct($flag);

        return $this->proxyConditionReturn();
    }

    /**
     * 总记录数.
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Select
     */
    public function count(string $field = '*', string $alias = 'row_count'): Select
    {
        $this->proxyCondition()->count($field, $alias);

        return $this->proxyConditionReturn();
    }

    /**
     * 平均数.
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Select
     */
    public function avg(string $field, string $alias = 'avg_value'): Select
    {
        $this->proxyCondition()->avg($field, $alias);

        return $this->proxyConditionReturn();
    }

    /**
     * 最大值
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Select
     */
    public function max(string $field, string $alias = 'max_value'): Select
    {
        $this->proxyCondition()->max($field, $alias);

        return $this->proxyConditionReturn();
    }

    /**
     * 最小值
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Select
     */
    public function min(string $field, string $alias = 'min_value'): Select
    {
        $this->proxyCondition()->min($field, $alias);

        return $this->proxyConditionReturn();
    }

    /**
     * 合计
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Select
     */
    public function sum(string $field, string $alias = 'sum_value'): Select
    {
        $this->proxyCondition()->sum($field, $alias);

        return $this->proxyConditionReturn();
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     *
     * @return \Leevel\Database\Select
     */
    public function one(): Select
    {
        $this->proxyCondition()->one();

        return $this->proxyConditionReturn();
    }

    /**
     * 指示查询所有符合条件的记录.
     *
     * @return \Leevel\Database\Select
     */
    public function all(): Select
    {
        $this->proxyCondition()->all();

        return $this->proxyConditionReturn();
    }

    /**
     * 查询几条记录.
     *
     * @param int $count
     *
     * @return \Leevel\Database\Select
     */
    public function top(int $count = 30): Select
    {
        $this->proxyCondition()->top($count);

        return $this->proxyConditionReturn();
    }

    /**
     * limit 限制条数.
     *
     * @param int $offset
     * @param int $count
     *
     * @return \Leevel\Database\Select
     */
    public function limit(int $offset = 0, int $count = 0): Select
    {
        $this->proxyCondition()->limit($offset, $count);

        return $this->proxyConditionReturn();
    }

    /**
     * 是否构造一个 FOR UPDATE 查询.
     *
     * @param bool $flag
     *
     * @return \Leevel\Database\Select
     */
    public function forUpdate(bool $flag = true): Select
    {
        $this->proxyCondition()->forUpdate($flag);

        return $this->proxyConditionReturn();
    }

    /**
     * 设置查询参数.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Database\Select
     */
    public function setOption(string $name, $value): Select
    {
        $this->proxyCondition()->setOption($name, $value);

        return $this->proxyConditionReturn();
    }

    /**
     * 返回查询参数.
     *
     * @return array
     */
    public function getOption(): array
    {
        return $this->proxyCondition()->getOption();
    }

    /**
     * 返回参数绑定.
     *
     * @return array
     */
    public function getBindParams(): array
    {
        return $this->proxyCondition()->getBindParams();
    }
}
