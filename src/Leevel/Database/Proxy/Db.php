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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Proxy;

use Closure;
use Leevel\Database\Condition;
use Leevel\Database\IDatabase;
use Leevel\Database\Manager;
use Leevel\Database\Page;
use Leevel\Database\Select;
use Leevel\Di\Container;
use PDO;

/**
 * 代理 database.
 *
 * @codeCoverageIgnore
 *
 * @example php leevel make:idehelper Leevel\\Database\\Proxy\\Db
 */
class Db
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 返回 Pdo 查询连接.
     *
     * @param bool|int $master
     *                         - bool false (读服务器) true (写服务器)
     *                         - int 其它去对应服务器连接ID 0 表示主服务器
     *
     * @return mixed
     */
    public static function pdo($master = false)
    {
        return self::proxy()->pdo($master);
    }

    /**
     * 查询数据记录.
     *
     * @param string     $sql           sql 语句
     * @param array      $bindParams    sql 参数绑定
     * @param bool|int   $master
     * @param null|mixed $fetchArgument
     *
     * @return mixed
     */
    public static function query(string $sql, array $bindParams = [], $master = false, int $fetchType = PDO::FETCH_OBJ, $fetchArgument = null, array $ctorArgs = [])
    {
        return self::proxy()->query($sql, $bindParams, $master, $fetchType, $fetchArgument, $ctorArgs);
    }

    /**
     * 执行 sql 语句.
     *
     * @param string $sql        sql 语句
     * @param array  $bindParams sql 参数绑定
     *
     * @return int|string
     */
    public static function execute(string $sql, array $bindParams = [])
    {
        return self::proxy()->execute($sql, $bindParams);
    }

    /**
     * 执行数据库事务
     *
     * @param \Closure $action 事务回调
     *
     * @return mixed
     */
    public static function transaction(Closure $action)
    {
        return self::proxy()->transaction($action);
    }

    /**
     * 启动事务.
     */
    public static function beginTransaction(): void
    {
        self::proxy()->beginTransaction();
    }

    /**
     * 检查是否处于事务中.
     */
    public static function inTransaction(): bool
    {
        return self::proxy()->inTransaction();
    }

    /**
     * 用于非自动提交状态下面的查询提交.
     */
    public static function commit(): void
    {
        self::proxy()->commit();
    }

    /**
     * 事务回滚.
     */
    public static function rollBack(): void
    {
        self::proxy()->rollBack();
    }

    /**
     * 获取最后插入 ID 或者列.
     *
     * @param null|string $name 自增序列名
     */
    public static function lastInsertId(?string $name = null): string
    {
        return self::proxy()->lastInsertId($name);
    }

    /**
     * 获取最近一次查询的 sql 语句.
     */
    public static function getLastSql(): ?string
    {
        return self::proxy()->getLastSql();
    }

    /**
     * 返回影响记录.
     */
    public static function numRows(): int
    {
        return self::proxy()->numRows();
    }

    /**
     * 关闭数据库.
     */
    public static function close(): void
    {
        self::proxy()->close();
    }

    /**
     * 释放 PDO 预处理查询.
     */
    public static function freePDOStatement(): void
    {
        self::proxy()->freePDOStatement();
    }

    /**
     * 关闭数据库连接.
     */
    public static function closeConnects(): void
    {
        self::proxy()->closeConnects();
    }

    /**
     * sql 表达式格式化.
     */
    public static function normalizeExpression(string $sql, string $tableName): string
    {
        return self::proxy()->normalizeExpression($sql, $tableName);
    }

    /**
     * 表或者字段格式化（支持别名）.
     */
    public static function normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null): string
    {
        return self::proxy()->normalizeTableOrColumn($name, $alias, $as);
    }

    /**
     * 字段格式化.
     */
    public static function normalizeColumn(string $key, string $tableName): string
    {
        return self::proxy()->normalizeColumn($key, $tableName);
    }

    /**
     * 字段值格式化.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function normalizeColumnValue($value, bool $quotationMark = true)
    {
        return self::proxy()->normalizeColumnValue($value, $quotationMark);
    }

    /**
     * 分析 sql 类型数据.
     */
    public static function normalizeSqlType(string $sql): string
    {
        return self::proxy()->normalizeSqlType($sql);
    }

    /**
     * 分析绑定参数类型数据.
     *
     * @param mixed $value
     */
    public static function normalizeBindParamType($value): int
    {
        return self::proxy()->normalizeBindParamType($value);
    }

    /**
     * dsn 解析.
     */
    public static function parseDsn(array $option): string
    {
        return self::proxy()->parseDsn($option);
    }

    /**
     * 取得数据库表名列表.
     *
     * @param bool|int $master
     */
    public static function tableNames(string $dbName, $master = false): array
    {
        return self::proxy()->tableNames($dbName, $master);
    }

    /**
     * 取得数据库表字段信息.
     *
     * @param bool|int $master
     */
    public static function tableColumns(string $tableName, $master = false): array
    {
        return self::proxy()->tableColumns($tableName, $master);
    }

    /**
     * sql 字段格式化.
     *
     * @param mixed $name
     */
    public static function identifierColumn($name): string
    {
        return self::proxy()->identifierColumn($name);
    }

    /**
     * 分析 limit.
     */
    public static function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        return self::proxy()->limitCount($limitCount, $limitOffset);
    }

    /**
     * 查询对象.
     */
    public static function databaseCondition(): Condition
    {
        return self::proxy()->databaseCondition();
    }

    /**
     * 返回数据库连接对象.
     */
    public static function databaseConnect(): IDatabase
    {
        return self::proxy()->databaseConnect();
    }

    /**
     * 返回查询对象.
     */
    public static function databaseSelect(): Select
    {
        return self::proxy()->databaseSelect();
    }

    /**
     * 指定返回 SQL 不做任何操作.
     */
    public static function sql(bool $flag = true): Select
    {
        return self::proxy()->sql($flag);
    }

    /**
     * 设置是否查询主服务器.
     */
    public static function master(bool $master = false): Select
    {
        return self::proxy()->master($master);
    }

    /**
     * 设置查询参数.
     *
     * @param null|mixed $fetchArgument
     */
    public static function fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = []): Select
    {
        return self::proxy()->fetchArgs($fetchStyle, $fetchArgument, $ctorArgs);
    }

    /**
     * 设置以某种包装返会结果.
     */
    public static function asSome(?Closure $asSome = null, array $args = []): Select
    {
        return self::proxy()->asSome($asSome, $args);
    }

    /**
     * 设置是否以集合返回.
     */
    public static function asCollection(bool $asCollection = true): Select
    {
        return self::proxy()->asCollection($asCollection);
    }

    /**
     * 原生 sql 查询数据 select.
     *
     * @param null|callable|\Leevel\Database\Select|string $data
     *
     * @return mixed
     */
    public static function select($data = null, array $bind = [], bool $flag = false)
    {
        return self::proxy()->select($data, $bind, $flag);
    }

    /**
     * 插入数据 insert (支持原生 SQL).
     *
     * @param array|string $data
     *
     * @return null|array|int
     */
    public static function insert($data, array $bind = [], bool $replace = false, bool $flag = false)
    {
        return self::proxy()->insert($data, $bind, $replace, $flag);
    }

    /**
     * 批量插入数据 insertAll.
     *
     * @return null|array|int
     */
    public static function insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)
    {
        return self::proxy()->insertAll($data, $bind, $replace, $flag);
    }

    /**
     * 更新数据 update (支持原生 SQL).
     *
     * @param array|string $data
     *
     * @return array|int
     */
    public static function update($data, array $bind = [], bool $flag = false)
    {
        return self::proxy()->update($data, $bind, $flag);
    }

    /**
     * 更新某个字段的值
     *
     * @param mixed $value
     *
     * @return array|int
     */
    public static function updateColumn(string $column, $value, array $bind = [], bool $flag = false)
    {
        return self::proxy()->updateColumn($column, $value, $bind, $flag);
    }

    /**
     * 字段递增.
     *
     * @return array|int
     */
    public static function updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)
    {
        return self::proxy()->updateIncrease($column, $step, $bind, $flag);
    }

    /**
     * 字段减少.
     *
     * @return array|int
     */
    public static function updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)
    {
        return self::proxy()->updateDecrease($column, $step, $bind, $flag);
    }

    /**
     * 删除数据 delete (支持原生 SQL).
     *
     * @return array|int
     */
    public static function delete(?string $data = null, array $bind = [], bool $flag = false)
    {
        return self::proxy()->delete($data, $bind, $flag);
    }

    /**
     * 清空表重置自增 ID.
     *
     * @return array|int
     */
    public static function truncate(bool $flag = false)
    {
        return self::proxy()->truncate($flag);
    }

    /**
     * 返回一条记录.
     *
     * @return mixed
     */
    public static function findOne(bool $flag = false)
    {
        return self::proxy()->findOne($flag);
    }

    /**
     * 返回所有记录.
     *
     * @return mixed
     */
    public static function findAll(bool $flag = false)
    {
        return self::proxy()->findAll($flag);
    }

    /**
     * 返回最后几条记录.
     *
     * @return mixed
     */
    public static function find(?int $num = null, bool $flag = false)
    {
        return self::proxy()->find($num, $flag);
    }

    /**
     * 返回一个字段的值
     *
     * @return mixed
     */
    public static function value(string $field, bool $flag = false)
    {
        return self::proxy()->value($field, $flag);
    }

    /**
     * 返回一列数据.
     *
     * @param mixed $fieldValue
     */
    public static function list($fieldValue, ?string $fieldKey = null, bool $flag = false): array
    {
        return self::proxy()->list($fieldValue, $fieldKey, $flag);
    }

    /**
     * 数据分块处理.
     */
    public static function chunk(int $count, Closure $chunk): void
    {
        self::proxy()->chunk($count, $chunk);
    }

    /**
     * 数据分块处理依次回调.
     */
    public static function each(int $count, Closure $each): void
    {
        self::proxy()->each($count, $each);
    }

    /**
     * 总记录数.
     *
     * @return array|int
     */
    public static function findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)
    {
        return self::proxy()->findCount($field, $alias, $flag);
    }

    /**
     * 平均数.
     *
     * @return mixed
     */
    public static function findAvg(string $field, string $alias = 'avg_value', bool $flag = false)
    {
        return self::proxy()->findAvg($field, $alias, $flag);
    }

    /**
     * 最大值.
     *
     * @return mixed
     */
    public static function findMax(string $field, string $alias = 'max_value', bool $flag = false)
    {
        return self::proxy()->findMax($field, $alias, $flag);
    }

    /**
     * 最小值.
     *
     * @return mixed
     */
    public static function findMin(string $field, string $alias = 'min_value', bool $flag = false)
    {
        return self::proxy()->findMin($field, $alias, $flag);
    }

    /**
     * 合计.
     *
     * @return mixed
     */
    public static function findSum(string $field, string $alias = 'sum_value', bool $flag = false)
    {
        return self::proxy()->findSum($field, $alias, $flag);
    }

    /**
     * 分页查询.
     *
     * - 可以渲染 HTML.
     */
    public static function page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []): Page
    {
        return self::proxy()->page($currentPage, $perPage, $flag, $column, $option);
    }

    /**
     * 创建一个无限数据的分页查询.
     */
    public static function pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): Page
    {
        return self::proxy()->pageMacro($currentPage, $perPage, $flag, $option);
    }

    /**
     * 创建一个只有上下页的分页查询.
     */
    public static function pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): Page
    {
        return self::proxy()->pagePrevNext($currentPage, $perPage, $flag, $option);
    }

    /**
     * 取得分页查询记录数量.
     */
    public static function pageCount(string $cols = '*'): int
    {
        return self::proxy()->pageCount($cols);
    }

    /**
     * 获得查询字符串.
     */
    public static function makeSql(bool $withLogicGroup = false): string
    {
        return self::proxy()->makeSql($withLogicGroup);
    }

    /**
     * 根据分页设置条件.
     */
    public static function forPage(int $page, int $perPage = 10): Select
    {
        return self::proxy()->forPage($page, $perPage);
    }

    /**
     * 时间控制语句开始.
     */
    public static function time(string $type = 'date'): Select
    {
        return self::proxy()->time($type);
    }

    /**
     * 时间控制语句结束.
     */
    public static function endTime(): Select
    {
        return self::proxy()->endTime();
    }

    /**
     * 重置查询条件.
     */
    public static function reset(?string $option = null): Select
    {
        return self::proxy()->reset($option);
    }

    /**
     * prefix 查询.
     */
    public static function prefix(string $prefix): Select
    {
        return self::proxy()->prefix($prefix);
    }

    /**
     * 添加一个要查询的表及其要查询的字段.
     *
     * @param mixed        $table
     * @param array|string $cols
     */
    public static function table($table, $cols = '*'): Select
    {
        return self::proxy()->table($table, $cols);
    }

    /**
     * 获取表别名.
     */
    public static function getAlias(): string
    {
        return self::proxy()->getAlias();
    }

    /**
     * 添加字段.
     *
     * @param mixed $cols
     */
    public static function columns($cols = '*', ?string $table = null): Select
    {
        return self::proxy()->columns($cols, $table);
    }

    /**
     * 设置字段.
     *
     * @param mixed $cols
     */
    public static function setColumns($cols = '*', ?string $table = null): Select
    {
        return self::proxy()->setColumns($cols, $table);
    }

    /**
     * where 查询条件.
     *
     * @param array ...$cond
     */
    public static function where(...$cond): Select
    {
        return self::proxy()->where(...$cond);
    }

    /**
     * orWhere 查询条件.
     *
     * @param array ...$cond
     */
    public static function orWhere(...$cond): Select
    {
        return self::proxy()->orWhere(...$cond);
    }

    /**
     * Where 原生查询.
     */
    public static function whereRaw(string $raw): Select
    {
        return self::proxy()->whereRaw($raw);
    }

    /**
     * Where 原生 OR 查询.
     */
    public static function orWhereRaw(string $raw): Select
    {
        return self::proxy()->orWhereRaw($raw);
    }

    /**
     * exists 方法支持
     *
     * @param mixed $exists
     */
    public static function whereExists($exists): Select
    {
        return self::proxy()->whereExists($exists);
    }

    /**
     * not exists 方法支持
     *
     * @param mixed $exists
     */
    public static function whereNotExists($exists): Select
    {
        return self::proxy()->whereNotExists($exists);
    }

    /**
     * whereBetween 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereBetween(...$cond): Select
    {
        return self::proxy()->whereBetween(...$cond);
    }

    /**
     * whereNotBetween 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereNotBetween(...$cond): Select
    {
        return self::proxy()->whereNotBetween(...$cond);
    }

    /**
     * whereNull 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereNull(...$cond): Select
    {
        return self::proxy()->whereNull(...$cond);
    }

    /**
     * whereNotNull 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereNotNull(...$cond): Select
    {
        return self::proxy()->whereNotNull(...$cond);
    }

    /**
     * whereIn 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereIn(...$cond): Select
    {
        return self::proxy()->whereIn(...$cond);
    }

    /**
     * whereNotIn 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereNotIn(...$cond): Select
    {
        return self::proxy()->whereNotIn(...$cond);
    }

    /**
     * whereLike 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereLike(...$cond): Select
    {
        return self::proxy()->whereLike(...$cond);
    }

    /**
     * whereNotLike 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereNotLike(...$cond): Select
    {
        return self::proxy()->whereNotLike(...$cond);
    }

    /**
     * whereDate 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereDate(...$cond): Select
    {
        return self::proxy()->whereDate(...$cond);
    }

    /**
     * whereDay 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereDay(...$cond): Select
    {
        return self::proxy()->whereDay(...$cond);
    }

    /**
     * whereMonth 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereMonth(...$cond): Select
    {
        return self::proxy()->whereMonth(...$cond);
    }

    /**
     * whereYear 查询条件.
     *
     * @param array ...$cond
     */
    public static function whereYear(...$cond): Select
    {
        return self::proxy()->whereYear(...$cond);
    }

    /**
     * 参数绑定支持
     *
     * @param mixed      $names
     * @param null|mixed $value
     */
    public static function bind($names, $value = null, int $type = PDO::PARAM_STR): Select
    {
        return self::proxy()->bind($names, $value, $type);
    }

    /**
     * index 强制索引（或者忽略索引）.
     *
     * @param array|string $indexs
     * @param string       $type
     */
    public static function forceIndex($indexs, $type = 'FORCE'): Select
    {
        return self::proxy()->forceIndex($indexs, $type);
    }

    /**
     * index 忽略索引.
     *
     * @param array|string $indexs
     */
    public static function ignoreIndex($indexs): Select
    {
        return self::proxy()->ignoreIndex($indexs);
    }

    /**
     * join 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     */
    public static function join($table, $cols, ...$cond): Select
    {
        return self::proxy()->join($table, $cols, ...$cond);
    }

    /**
     * innerJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     */
    public static function innerJoin($table, $cols, ...$cond): Select
    {
        return self::proxy()->innerJoin($table, $cols, ...$cond);
    }

    /**
     * leftJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     */
    public static function leftJoin($table, $cols, ...$cond): Select
    {
        return self::proxy()->leftJoin($table, $cols, ...$cond);
    }

    /**
     * rightJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     */
    public static function rightJoin($table, $cols, ...$cond): Select
    {
        return self::proxy()->rightJoin($table, $cols, ...$cond);
    }

    /**
     * fullJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     */
    public static function fullJoin($table, $cols, ...$cond): Select
    {
        return self::proxy()->fullJoin($table, $cols, ...$cond);
    }

    /**
     * crossJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     */
    public static function crossJoin($table, $cols, ...$cond): Select
    {
        return self::proxy()->crossJoin($table, $cols, ...$cond);
    }

    /**
     * naturalJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     */
    public static function naturalJoin($table, $cols, ...$cond): Select
    {
        return self::proxy()->naturalJoin($table, $cols, ...$cond);
    }

    /**
     * 添加一个 UNION 查询.
     *
     * @param array|callable|string $selects
     */
    public static function union($selects, string $type = 'UNION'): Select
    {
        return self::proxy()->union($selects, $type);
    }

    /**
     * 添加一个 UNION ALL 查询.
     *
     * @param array|callable|string $selects
     */
    public static function unionAll($selects): Select
    {
        return self::proxy()->unionAll($selects);
    }

    /**
     * 指定 GROUP BY 子句.
     *
     * @param array|string $expression
     */
    public static function groupBy($expression): Select
    {
        return self::proxy()->groupBy($expression);
    }

    /**
     * 添加一个 HAVING 条件.
     *
     * - 参数规范参考 where()方法.
     *
     * @param array ...$cond
     */
    public static function having(...$cond): Select
    {
        return self::proxy()->having(...$cond);
    }

    /**
     * orHaving 查询条件.
     *
     * @param array ...$cond
     */
    public static function orHaving(...$cond): Select
    {
        return self::proxy()->orHaving(...$cond);
    }

    /**
     * having 原生查询.
     */
    public static function havingRaw(string $raw): Select
    {
        return self::proxy()->havingRaw($raw);
    }

    /**
     * having 原生 OR 查询.
     */
    public static function orHavingRaw(string $raw): Select
    {
        return self::proxy()->orHavingRaw($raw);
    }

    /**
     * havingBetween 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingBetween(...$cond): Select
    {
        return self::proxy()->havingBetween(...$cond);
    }

    /**
     * havingNotBetween 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingNotBetween(...$cond): Select
    {
        return self::proxy()->havingNotBetween(...$cond);
    }

    /**
     * havingNull 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingNull(...$cond): Select
    {
        return self::proxy()->havingNull(...$cond);
    }

    /**
     * havingNotNull 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingNotNull(...$cond): Select
    {
        return self::proxy()->havingNotNull(...$cond);
    }

    /**
     * havingIn 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingIn(...$cond): Select
    {
        return self::proxy()->havingIn(...$cond);
    }

    /**
     * havingNotIn 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingNotIn(...$cond): Select
    {
        return self::proxy()->havingNotIn(...$cond);
    }

    /**
     * havingLike 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingLike(...$cond): Select
    {
        return self::proxy()->havingLike(...$cond);
    }

    /**
     * havingNotLike 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingNotLike(...$cond): Select
    {
        return self::proxy()->havingNotLike(...$cond);
    }

    /**
     * havingDate 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingDate(...$cond): Select
    {
        return self::proxy()->havingDate(...$cond);
    }

    /**
     * havingDay 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingDay(...$cond): Select
    {
        return self::proxy()->havingDay(...$cond);
    }

    /**
     * havingMonth 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingMonth(...$cond): Select
    {
        return self::proxy()->havingMonth(...$cond);
    }

    /**
     * havingYear 查询条件.
     *
     * @param array ...$cond
     */
    public static function havingYear(...$cond): Select
    {
        return self::proxy()->havingYear(...$cond);
    }

    /**
     * 添加排序.
     *
     * @param array|string $expression
     */
    public static function orderBy($expression, string $orderDefault = 'ASC'): Select
    {
        return self::proxy()->orderBy($expression, $orderDefault);
    }

    /**
     * 最近排序数据.
     */
    public static function latest(string $field = 'create_at'): Select
    {
        return self::proxy()->latest($field);
    }

    /**
     * 最早排序数据.
     */
    public static function oldest(string $field = 'create_at'): Select
    {
        return self::proxy()->oldest($field);
    }

    /**
     * 创建一个 SELECT DISTINCT 查询.
     */
    public static function distinct(bool $flag = true): Select
    {
        return self::proxy()->distinct($flag);
    }

    /**
     * 总记录数.
     */
    public static function count(string $field = '*', string $alias = 'row_count'): Select
    {
        return self::proxy()->count($field, $alias);
    }

    /**
     * 平均数.
     */
    public static function avg(string $field, string $alias = 'avg_value'): Select
    {
        return self::proxy()->avg($field, $alias);
    }

    /**
     * 最大值.
     */
    public static function max(string $field, string $alias = 'max_value'): Select
    {
        return self::proxy()->max($field, $alias);
    }

    /**
     * 最小值.
     */
    public static function min(string $field, string $alias = 'min_value'): Select
    {
        return self::proxy()->min($field, $alias);
    }

    /**
     * 合计
     */
    public static function sum(string $field, string $alias = 'sum_value'): Select
    {
        return self::proxy()->sum($field, $alias);
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     */
    public static function one(): Select
    {
        return self::proxy()->one();
    }

    /**
     * 指示查询所有符合条件的记录.
     */
    public static function all(): Select
    {
        return self::proxy()->all();
    }

    /**
     * 查询几条记录.
     */
    public static function top(int $count = 30): Select
    {
        return self::proxy()->top($count);
    }

    /**
     * limit 限制条数.
     */
    public static function limit(int $offset = 0, int $count = 0): Select
    {
        return self::proxy()->limit($offset, $count);
    }

    /**
     * 是否构造一个 FOR UPDATE 查询.
     */
    public static function forUpdate(bool $flag = true): Select
    {
        return self::proxy()->forUpdate($flag);
    }

    /**
     * 设置查询参数.
     *
     * @param mixed $value
     */
    public static function setOption(string $name, $value): Select
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 返回查询参数.
     */
    public static function getOption(): array
    {
        return self::proxy()->getOption();
    }

    /**
     * 返回参数绑定.
     */
    public static function getBindParams(): array
    {
        return self::proxy()->getBindParams();
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('databases');
    }
}
