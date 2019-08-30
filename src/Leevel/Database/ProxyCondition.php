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
        $this->proxy()->forPage($page, $perPage);

        return $this;
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
        $this->proxy()->time($type);

        return $this;
    }

    /**
     * 时间控制语句结束.
     *
     * @return \Leevel\Database\Select
     */
    public function endTime(): Select
    {
        $this->proxy()->endTime();

        return $this;
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
        $this->proxy()->reset($option);

        return $this;
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
        $this->proxy()->prefix($prefix);

        return $this;
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
        $this->proxy()->table($table, $cols);

        return $this;
    }

    /**
     * 获取表别名.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->proxy()->getAlias();
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
        $this->proxy()->columns($cols, $table);

        return $this;
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
        $this->proxy()->setColumns($cols, $table);

        return $this;
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
        $this->proxy()->where(...$cond);

        return $this;
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
        $this->proxy()->orWhere(...$cond);

        return $this;
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
        $this->proxy()->whereRaw($raw);

        return $this;
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
        $this->proxy()->orWhereRaw($raw);

        return $this;
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
        $this->proxy()->whereExists($exists);

        return $this;
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
        $this->proxy()->whereNotExists($exists);

        return $this;
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
        $this->proxy()->whereBetween(...$cond);

        return $this;
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
        $this->proxy()->whereNotBetween(...$cond);

        return $this;
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
        $this->proxy()->whereNull(...$cond);

        return $this;
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
        $this->proxy()->whereNotNull(...$cond);

        return $this;
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
        $this->proxy()->whereIn(...$cond);

        return $this;
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
        $this->proxy()->whereNotIn(...$cond);

        return $this;
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
        $this->proxy()->whereLike(...$cond);

        return $this;
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
        $this->proxy()->whereNotLike(...$cond);

        return $this;
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
        $this->proxy()->whereDate(...$cond);

        return $this;
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
        $this->proxy()->whereDay(...$cond);

        return $this;
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
        $this->proxy()->whereMonth(...$cond);

        return $this;
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
        $this->proxy()->whereYear(...$cond);

        return $this;
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
        $this->proxy()->bind($names, $value, $type);

        return $this;
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
        $this->proxy()->forceIndex($indexs, $type);

        return $this;
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
        $this->proxy()->ignoreIndex($indexs);

        return $this;
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
        $this->proxy()->join($table, $cols, ...$cond);

        return $this;
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
        $this->proxy()->innerJoin($table, $cols, ...$cond);

        return $this;
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
        $this->proxy()->leftJoin($table, $cols, ...$cond);

        return $this;
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
        $this->proxy()->rightJoin($table, $cols, ...$cond);

        return $this;
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
        $this->proxy()->fullJoin($table, $cols, ...$cond);

        return $this;
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
        $this->proxy()->crossJoin($table, $cols, ...$cond);

        return $this;
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
        $this->proxy()->naturalJoin($table, $cols, ...$cond);

        return $this;
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
        $this->proxy()->union($selects, $type);

        return $this;
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
        $this->proxy()->unionAll($selects);

        return $this;
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
        $this->proxy()->groupBy($expression);

        return $this;
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
        $this->proxy()->having(...$cond);

        return $this;
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
        $this->proxy()->orHaving(...$cond);

        return $this;
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
        $this->proxy()->havingRaw($raw);

        return $this;
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
        $this->proxy()->orHavingRaw($raw);

        return $this;
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
        $this->proxy()->havingBetween(...$cond);

        return $this;
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
        $this->proxy()->havingNotBetween(...$cond);

        return $this;
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
        $this->proxy()->havingNull(...$cond);

        return $this;
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
        $this->proxy()->havingNotNull(...$cond);

        return $this;
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
        $this->proxy()->havingIn(...$cond);

        return $this;
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
        $this->proxy()->havingNotIn(...$cond);

        return $this;
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
        $this->proxy()->havingLike(...$cond);

        return $this;
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
        $this->proxy()->havingNotLike(...$cond);

        return $this;
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
        $this->proxy()->havingDate(...$cond);

        return $this;
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
        $this->proxy()->havingDay(...$cond);

        return $this;
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
        $this->proxy()->havingMonth(...$cond);

        return $this;
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
        $this->proxy()->havingYear(...$cond);

        return $this;
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
        $this->proxy()->orderBy($expression, $orderDefault);

        return $this;
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
        $this->proxy()->latest($field);

        return $this;
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
        $this->proxy()->oldest($field);

        return $this;
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
        $this->proxy()->distinct($flag);

        return $this;
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
        $this->proxy()->count($field, $alias);

        return $this;
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
        $this->proxy()->avg($field, $alias);

        return $this;
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
        $this->proxy()->max($field, $alias);

        return $this;
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
        $this->proxy()->min($field, $alias);

        return $this;
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
        $this->proxy()->sum($field, $alias);

        return $this;
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     *
     * @return \Leevel\Database\Select
     */
    public function one(): Select
    {
        $this->proxy()->one();

        return $this;
    }

    /**
     * 指示查询所有符合条件的记录.
     *
     * @return \Leevel\Database\Select
     */
    public function all(): Select
    {
        $this->proxy()->all();

        return $this;
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
        $this->proxy()->top($count);

        return $this;
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
        $this->proxy()->limit($offset, $count);

        return $this;
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
        $this->proxy()->forUpdate($flag);

        return $this;
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
        $this->proxy()->setOption($name, $value);

        return $this;
    }

    /**
     * 返回查询参数.
     *
     * @return array
     */
    public function getOption(): array
    {
        return $this->proxy()->getOption();
    }

    /**
     * 返回参数绑定.
     *
     * @return array
     */
    public function getBindParams(): array
    {
        return $this->proxy()->getBindParams();
    }
}
