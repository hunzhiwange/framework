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

namespace Leevel\Database\Proxy;

use Closure;
use Leevel\Database\Condition;
use Leevel\Database\IDatabase;
use Leevel\Database\Manager;
use Leevel\Database\Select;
use Leevel\Di\Container;
use PDO;

/**
 * 代理 database.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 */
class Db implements IDb
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
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
     * @param string   $sql           sql 语句
     * @param array    $bindParams    sql 参数绑定
     * @param bool|int $master
     * @param int      $fetchType
     * @param mixed    $fetchArgument
     * @param array    $ctorArgs
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
     *
     * @return bool
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
     * @param string $name 自增序列名
     *
     * @return string
     */
    public static function lastInsertId(?string $name = null): string
    {
        return self::proxy()->lastInsertId($name);
    }

    /**
     * 获取最近一次查询的 sql 语句.
     *
     * @return array
     */
    public static function lastSql(): array
    {
        return self::proxy()->lastSql();
    }

    /**
     * 返回影响记录.
     *
     * @return int
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
     *
     * @param string $sql
     * @param string $tableName
     *
     * @return string
     */
    public static function normalizeExpression(string $sql, string $tableName): string
    {
        return self::proxy()->normalizeExpression($sql, $tableName);
    }

    /**
     * 表或者字段格式化（支持别名）.
     *
     * @param string $name
     * @param string $alias
     * @param string $as
     *
     * @return string
     */
    public static function normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null): string
    {
        return self::proxy()->normalizeTableOrColumn($name, $alias, $as);
    }

    /**
     * 字段格式化.
     *
     * @param string $key
     * @param string $tableName
     *
     * @return string
     */
    public static function normalizeColumn(string $key, string $tableName): string
    {
        return self::proxy()->normalizeColumn($key, $tableName);
    }

    /**
     * 字段值格式化.
     *
     * @param mixed $value
     * @param bool  $quotationMark
     *
     * @return mixed
     */
    public static function normalizeColumnValue($value, bool $quotationMark = true)
    {
        return self::proxy()->normalizeColumnValue($value, $quotationMark);
    }

    /**
     * 分析 sql 类型数据.
     *
     * @param string $sql
     *
     * @return string
     */
    public static function normalizeSqlType(string $sql): string
    {
        return self::proxy()->normalizeSqlType($sql);
    }

    /**
     * 分析绑定参数类型数据.
     *
     * @param mixed $value
     *
     * @return int
     */
    public static function normalizeBindParamType($value): int
    {
        return self::proxy()->normalizeBindParamType($value);
    }

    /**
     * dsn 解析.
     *
     * @param array $option
     *
     * @return string
     */
    public static function parseDsn(array $option): string
    {
        return self::proxy()->parseDsn($option);
    }

    /**
     * 取得数据库表名列表.
     *
     * @param string   $dbName
     * @param bool|int $master
     *
     * @return array
     */
    public static function tableNames(string $dbName, $master = false): array
    {
        return self::proxy()->tableNames($dbName, $master);
    }

    /**
     * 取得数据库表字段信息.
     *
     * @param string   $tableName
     * @param bool|int $master
     *
     * @return array
     */
    public static function tableColumns(string $tableName, $master = false): array
    {
        return self::proxy()->tableColumns($tableName, $master);
    }

    /**
     * sql 字段格式化.
     *
     * @param mixed $name
     *
     * @return string
     */
    public static function identifierColumn($name): string
    {
        return self::proxy()->identifierColumn($name);
    }

    /**
     * 分析 limit.
     *
     * @param null|int $limitCount
     * @param null|int $limitOffset
     *
     * @return string
     */
    public static function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        return self::proxy()->limitCount($limitCount, $limitOffset);
    }

    /**
     * 查询对象
     *
     * @return \Leevel\Database\Condition
     */
    public static function databaseCondition(): Condition
    {
        return self::proxy()->databaseCondition();
    }

    /**
     * 返回数据库连接对象
     *
     * @return \Leevel\Database\IDatabase
     */
    public static function databaseConnect(): IDatabase
    {
        return self::proxy()->databaseConnect();
    }

    /**
     * 占位符返回本对象
     *
     * @return \Leevel\Database\Select
     */
    public static function selfDatabaseSelect(): Select
    {
        return self::proxy()->selfDatabaseSelect();
    }

    /**
     * 指定返回 SQL 不做任何操作.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return \Leevel\Database\Select
     */
    public static function sql(bool $flag = true): Select
    {
        return self::proxy()->sql($flag);
    }

    /**
     * 设置是否查询主服务器.
     *
     * @param bool $master
     *
     * @return \Leevel\Database\Select
     */
    public static function master(bool $master = false): Select
    {
        return self::proxy()->master($master);
    }

    /**
     * 设置查询参数.
     *
     * @param int   $fetchStyle
     * @param mixed $fetchArgument
     * @param array $ctorArgs
     *
     * @return \Leevel\Database\Select
     */
    public static function fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = []): Select
    {
        return self::proxy()->fetchArgs($fetchStyle, $fetchArgument, $ctorArgs);
    }

    /**
     * 设置以类返会结果.
     *
     * @param string $className
     * @param array  $args
     *
     * @return \Leevel\Database\Select
     */
    public static function asClass(string $className, array $args = []): Select
    {
        return self::proxy()->asClass($className, $args);
    }

    /**
     * 设置默认形式返回.
     *
     * @return \Leevel\Database\Select
     */
    public static function asDefault(): Select
    {
        return self::proxy()->asDefault();
    }

    /**
     * 设置是否以集合返回.
     *
     * @param bool $acollection
     *
     * @return \Leevel\Database\Select
     */
    public static function asCollection(bool $acollection = true): Select
    {
        return self::proxy()->asCollection($acollection);
    }

    /**
     * 原生 sql 查询数据 select.
     *
     * @param null|callable|select|string $data
     * @param array                       $bind
     * @param bool                        $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public static function select($data = null, array $bind = [], bool $flag = false)
    {
        return self::proxy()->select($data, $bind, $flag);
    }

    /**
     * 插入数据 insert (支持原生 sql).
     *
     * @param array|string $data
     * @param array        $bind
     * @param bool         $replace
     * @param bool         $flag    指示是否不做任何操作只返回 SQL
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
     * @param array $data
     * @param array $bind
     * @param bool  $replace
     * @param bool  $flag    指示是否不做任何操作只返回 SQL
     *
     * @return null|array|int
     */
    public static function insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)
    {
        return self::proxy()->insertAll($data, $bind, $replace, $flag);
    }

    /**
     * 更新数据 update (支持原生 sql).
     *
     * @param array|string $data
     * @param array        $bind
     * @param bool         $flag 指示是否不做任何操作只返回 SQL
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
     * @param string $column
     * @param mixed  $value
     * @param array  $bind
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
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
     * @param string $column
     * @param int    $step
     * @param array  $bind
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
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
     * @param string $column
     * @param int    $step
     * @param array  $bind
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
     *
     * @return array|int
     */
    public static function updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)
    {
        return self::proxy()->updateDecrease($column, $step, $bind, $flag);
    }

    /**
     * 删除数据 delete (支持原生 sql).
     *
     * @param null|string $data
     * @param array       $bind
     * @param bool        $flag 指示是否不做任何操作只返回 SQL
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
     * @param bool $flag 指示是否不做任何操作只返回 SQL
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
     * @param bool $flag 指示是否不做任何操作只返回 SQL
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
     * @param bool $flag 指示是否不做任何操作只返回 SQL
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
     * @param int  $num
     * @param bool $flag 指示是否不做任何操作只返回 SQL
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
     * @param string $field
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public static function value(string $field, bool $flag = false)
    {
        return self::proxy()->value($field, $flag);
    }

    /**
     * 返回一个字段的值(别名).
     *
     * @param string $field
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public static function pull(string $field, bool $flag = false)
    {
        return self::proxy()->pull($field, $flag);
    }

    /**
     * 返回一列数据.
     *
     * @param mixed  $fieldValue
     * @param string $fieldKey
     * @param bool   $flag       指示是否不做任何操作只返回 SQL
     *
     * @return array
     */
    public static function list($fieldValue, ?string $fieldKey = null, bool $flag = false): array
    {
        return self::proxy()->list($fieldValue, $fieldKey, $flag);
    }

    /**
     * 数据分块处理.
     *
     * @param int      $count
     * @param \Closure $chunk
     */
    public static function chunk(int $count, Closure $chunk): void
    {
        self::proxy()->chunk($count, $chunk);
    }

    /**
     * 数据分块处理依次回调.
     *
     * @param int     $count
     * @param Closure $each
     */
    public static function each(int $count, Closure $each): void
    {
        self::proxy()->each($count, $each);
    }

    /**
     * 总记录数.
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
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
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public static function findAvg(string $field, string $alias = 'avg_value', bool $flag = false)
    {
        return self::proxy()->findAvg($field, $alias, $flag);
    }

    /**
     * 最大值
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public static function findMax(string $field, string $alias = 'max_value', bool $flag = false)
    {
        return self::proxy()->findMax($field, $alias, $flag);
    }

    /**
     * 最小值
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public static function findMin(string $field, string $alias = 'min_value', bool $flag = false)
    {
        return self::proxy()->findMin($field, $alias, $flag);
    }

    /**
     * 合计
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
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
     * @param int    $currentPage
     * @param int    $perPage
     * @param bool   $flag
     * @param bool   $withTotal
     * @param string $column
     *
     * @return array
     */
    public static function page(int $currentPage, int $perPage = 10, bool $flag = false, bool $withTotal = true, string $column = '*'): array
    {
        return self::proxy()->page($currentPage, $perPage, $flag, $withTotal, $column);
    }

    /**
     * 分页查询.
     * 可以渲染 HTML.
     *
     * @param int    $currentPage
     * @param int    $perPage
     * @param bool   $flag
     * @param string $column
     * @param array  $option
     *
     * @return array
     */
    public static function pageHtml(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []): array
    {
        return self::proxy()->pageHtml($currentPage, $perPage, $flag, $column, $option);
    }

    /**
     * 创建一个无限数据的分页查询.
     *
     * @param int   $currentPage
     * @param int   $perPage
     * @param bool  $flag
     * @param array $option
     *
     * @return array
     */
    public static function pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): array
    {
        return self::proxy()->pageMacro($currentPage, $perPage, $flag, $option);
    }

    /**
     * 创建一个只有上下页的分页查询.
     *
     * @param int   $currentPage
     * @param int   $perPage
     * @param bool  $flag
     * @param array $option
     *
     * @return array
     */
    public static function pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): array
    {
        return self::proxy()->pagePrevNext($currentPage, $perPage, $flag, $option);
    }

    /**
     * 取得分页查询记录数量.
     *
     * @param string $cols
     *
     * @return int
     */
    public static function pageCount(string $cols = '*'): int
    {
        return self::proxy()->pageCount($cols);
    }

    /**
     * 获得查询字符串.
     *
     * @param $withLogicGroup
     *
     * @return string
     */
    public static function makeSql(bool $withLogicGroup = false): string
    {
        return self::proxy()->makeSql($withLogicGroup);
    }

    /**
     * 根据分页设置条件.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return \Leevel\Database\Condition
     */
    public static function forPage(int $page, int $perPage = 15): Condition
    {
        return self::proxy()->forPage($page, $perPage);
    }

    /**
     * 时间控制语句开始.
     *
     * @param string $type
     *
     * @return \Leevel\Database\Condition
     */
    public static function time(string $type = 'date'): Condition
    {
        return self::proxy()->time($type);
    }

    /**
     * 时间控制语句结束.
     *
     * @return \Leevel\Database\Condition
     */
    public static function endTime(): Condition
    {
        return self::proxy()->endTime();
    }

    /**
     * 重置查询条件.
     *
     * @param null|string $option
     *
     * @return \Leevel\Database\Condition
     */
    public static function reset(?string $option = null): Condition
    {
        return self::proxy()->reset($option);
    }

    /**
     * prefix 查询.
     *
     * @param string $prefix
     *
     * @return \Leevel\Database\Condition
     */
    public static function prefix(string $prefix): Condition
    {
        return self::proxy()->prefix($prefix);
    }

    /**
     * 添加一个要查询的表及其要查询的字段.
     *
     * @param mixed        $table
     * @param array|string $cols
     *
     * @return \Leevel\Database\Select
     */
    public static function table($table, $cols = '*'): Select
    {
        return self::proxy()->table($table, $cols);
    }

    /**
     * 获取表别名.
     *
     * @return string
     */
    public static function getAlias(): string
    {
        return self::proxy()->getAlias();
    }

    /**
     * 添加字段.
     *
     * @param mixed  $cols
     * @param string $table
     *
     * @return \Leevel\Database\Select
     */
    public static function columns($cols = '*', ?string $table = null): Select
    {
        return self::proxy()->columns($cols, $table);
    }

    /**
     * 设置字段.
     *
     * @param mixed  $cols
     * @param string $table
     *
     * @return \Leevel\Database\Select
     */
    public static function setColumns($cols = '*', ?string $table = null): Select
    {
        return self::proxy()->setColumns($cols, $table);
    }

    /**
     * where 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Select
     */
    public static function where(...$cond): Select
    {
        return self::proxy()->where(...$cond);
    }

    /**
     * orWhere 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function orWhere(...$cond): Condition
    {
        return self::proxy()->orWhere(...$cond);
    }

    /**
     * Where 原生查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereRaw(string $raw): Condition
    {
        return self::proxy()->whereRaw($raw);
    }

    /**
     * Where 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public static function orWhereRaw(string $raw): Condition
    {
        return self::proxy()->orWhereRaw($raw);
    }

    /**
     * exists 方法支持
     *
     * @param mixed $exists
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereExists($exists): Condition
    {
        return self::proxy()->whereExists($exists);
    }

    /**
     * not exists 方法支持
     *
     * @param mixed $exists
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereNotExists($exists): Condition
    {
        return self::proxy()->whereNotExists($exists);
    }

    /**
     * whereBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereBetween(...$cond): Condition
    {
        return self::proxy()->whereBetween(...$cond);
    }

    /**
     * whereNotBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereNotBetween(...$cond): Condition
    {
        return self::proxy()->whereNotBetween(...$cond);
    }

    /**
     * whereNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereNull(...$cond): Condition
    {
        return self::proxy()->whereNull(...$cond);
    }

    /**
     * whereNotNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereNotNull(...$cond): Condition
    {
        return self::proxy()->whereNotNull(...$cond);
    }

    /**
     * whereIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereIn(...$cond): Condition
    {
        return self::proxy()->whereIn(...$cond);
    }

    /**
     * whereNotIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereNotIn(...$cond): Condition
    {
        return self::proxy()->whereNotIn(...$cond);
    }

    /**
     * whereLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereLike(...$cond): Condition
    {
        return self::proxy()->whereLike(...$cond);
    }

    /**
     * whereNotLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereNotLike(...$cond): Condition
    {
        return self::proxy()->whereNotLike(...$cond);
    }

    /**
     * whereDate 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereDate(...$cond): Condition
    {
        return self::proxy()->whereDate(...$cond);
    }

    /**
     * whereDay 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereDay(...$cond): Condition
    {
        return self::proxy()->whereDay(...$cond);
    }

    /**
     * whereMonth 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereMonth(...$cond): Condition
    {
        return self::proxy()->whereMonth(...$cond);
    }

    /**
     * whereYear 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function whereYear(...$cond): Condition
    {
        return self::proxy()->whereYear(...$cond);
    }

    /**
     * 参数绑定支持
     *
     * @param mixed $names
     * @param mixed $value
     * @param int   $type
     *
     * @return \Leevel\Database\Condition
     */
    public static function bind($names, $value = null, int $type = PDO::PARAM_STR): Condition
    {
        return self::proxy()->bind($names, $value, $type);
    }

    /**
     * index 强制索引（或者忽略索引）.
     *
     * @param array|string $indexs
     * @param string       $type
     *
     * @return \Leevel\Database\Condition
     */
    public static function forceIndex($indexs, $type = 'FORCE'): Condition
    {
        return self::proxy()->forceIndex($indexs, $type);
    }

    /**
     * index 忽略索引.
     *
     * @param array|string $indexs
     *
     * @return \Leevel\Database\Condition
     */
    public static function ignoreIndex($indexs): Condition
    {
        return self::proxy()->ignoreIndex($indexs);
    }

    /**
     * join 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function join($table, $cols, ...$cond): Condition
    {
        return self::proxy()->join($table, $cols, ...$cond);
    }

    /**
     * innerJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function innerJoin($table, $cols, ...$cond): Condition
    {
        return self::proxy()->innerJoin($table, $cols, ...$cond);
    }

    /**
     * leftJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function leftJoin($table, $cols, ...$cond): Condition
    {
        return self::proxy()->leftJoin($table, $cols, ...$cond);
    }

    /**
     * rightJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function rightJoin($table, $cols, ...$cond): Condition
    {
        return self::proxy()->rightJoin($table, $cols, ...$cond);
    }

    /**
     * fullJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function fullJoin($table, $cols, ...$cond): Condition
    {
        return self::proxy()->fullJoin($table, $cols, ...$cond);
    }

    /**
     * crossJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function crossJoin($table, $cols, ...$cond): Condition
    {
        return self::proxy()->crossJoin($table, $cols, ...$cond);
    }

    /**
     * naturalJoin 查询.
     *
     * @param mixed        $table   同 table $table
     * @param array|string $cols    同 table $cols
     * @param array        ...$cond 同 where $cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function naturalJoin($table, $cols, ...$cond): Condition
    {
        return self::proxy()->naturalJoin($table, $cols, ...$cond);
    }

    /**
     * 添加一个 UNION 查询.
     *
     * @param array|callable|string $selects
     * @param string                $type
     *
     * @return \Leevel\Database\Condition
     */
    public static function union($selects, string $type = 'UNION'): Condition
    {
        return self::proxy()->union($selects, $type);
    }

    /**
     * 添加一个 UNION ALL 查询.
     *
     * @param array|callable|string $selects
     *
     * @return \Leevel\Database\Condition
     */
    public static function unionAll($selects): Condition
    {
        return self::proxy()->unionAll($selects);
    }

    /**
     * 指定 GROUP BY 子句.
     *
     * @param array|string $expression
     *
     * @return \Leevel\Database\Condition
     */
    public static function groupBy($expression): Condition
    {
        return self::proxy()->groupBy($expression);
    }

    /**
     * 添加一个 HAVING 条件
     * < 参数规范参考 where()方法 >.
     *
     * @param array $data
     *
     * @return \Leevel\Database\Condition
     */
    public static function having(...$cond): Condition
    {
        return self::proxy()->having(...$cond);
    }

    /**
     * orHaving 查询条件.
     *
     * @param array $data
     *
     * @return \Leevel\Database\Condition
     */
    public static function orHaving(...$cond): Condition
    {
        return self::proxy()->orHaving(...$cond);
    }

    /**
     * Having 原生查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingRaw(string $raw): Condition
    {
        return self::proxy()->havingRaw($raw);
    }

    /**
     * Having 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public static function orHavingRaw(string $raw): Condition
    {
        return self::proxy()->orHavingRaw($raw);
    }

    /**
     * havingBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingBetween(...$cond): Condition
    {
        return self::proxy()->havingBetween(...$cond);
    }

    /**
     * havingNotBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingNotBetween(...$cond): Condition
    {
        return self::proxy()->havingNotBetween(...$cond);
    }

    /**
     * havingNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingNull(...$cond): Condition
    {
        return self::proxy()->havingNull(...$cond);
    }

    /**
     * havingNotNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingNotNull(...$cond): Condition
    {
        return self::proxy()->havingNotNull(...$cond);
    }

    /**
     * havingIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingIn(...$cond): Condition
    {
        return self::proxy()->havingIn(...$cond);
    }

    /**
     * havingNotIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingNotIn(...$cond): Condition
    {
        return self::proxy()->havingNotIn(...$cond);
    }

    /**
     * havingLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingLike(...$cond): Condition
    {
        return self::proxy()->havingLike(...$cond);
    }

    /**
     * havingNotLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingNotLike(...$cond): Condition
    {
        return self::proxy()->havingNotLike(...$cond);
    }

    /**
     * havingDate 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingDate(...$cond): Condition
    {
        return self::proxy()->havingDate(...$cond);
    }

    /**
     * havingDay 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingDay(...$cond): Condition
    {
        return self::proxy()->havingDay(...$cond);
    }

    /**
     * havingMonth 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingMonth(...$cond): Condition
    {
        return self::proxy()->havingMonth(...$cond);
    }

    /**
     * havingYear 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public static function havingYear(...$cond): Condition
    {
        return self::proxy()->havingYear(...$cond);
    }

    /**
     * 添加排序.
     *
     * @param array|string $expression
     * @param string       $orderDefault
     *
     * @return \Leevel\Database\Condition
     */
    public static function orderBy($expression, string $orderDefault = 'ASC'): Condition
    {
        return self::proxy()->orderBy($expression, $orderDefault);
    }

    /**
     * 最近排序数据.
     *
     * @param string $field
     *
     * @return \Leevel\Database\Condition
     */
    public static function latest(string $field = 'create_at'): Condition
    {
        return self::proxy()->latest($field);
    }

    /**
     * 最早排序数据.
     *
     * @param string $field
     *
     * @return \Leevel\Database\Condition
     */
    public static function oldest(string $field = 'create_at'): Condition
    {
        return self::proxy()->oldest($field);
    }

    /**
     * 创建一个 SELECT DISTINCT 查询.
     *
     * @param bool $flag 指示是否是一个 SELECT DISTINCT 查询（默认 true）
     *
     * @return \Leevel\Database\Condition
     */
    public static function distinct(bool $flag = true): Condition
    {
        return self::proxy()->distinct($flag);
    }

    /**
     * 总记录数.
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public static function count(string $field = '*', string $alias = 'row_count'): Condition
    {
        return self::proxy()->count($field, $alias);
    }

    /**
     * 平均数.
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public static function avg(string $field, string $alias = 'avg_value'): Condition
    {
        return self::proxy()->avg($field, $alias);
    }

    /**
     * 最大值
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public static function max(string $field, string $alias = 'max_value'): Condition
    {
        return self::proxy()->max($field, $alias);
    }

    /**
     * 最小值
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public static function min(string $field, string $alias = 'min_value'): Condition
    {
        return self::proxy()->min($field, $alias);
    }

    /**
     * 合计
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public static function sum(string $field, string $alias = 'sum_value'): Condition
    {
        return self::proxy()->sum($field, $alias);
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     *
     * @return \Leevel\Database\Condition
     */
    public static function one(): Condition
    {
        return self::proxy()->one();
    }

    /**
     * 指示查询所有符合条件的记录.
     *
     * @return \Leevel\Database\Condition
     */
    public static function all(): Condition
    {
        return self::proxy()->all();
    }

    /**
     * 查询几条记录.
     *
     * @param int $count
     *
     * @return \Leevel\Database\Condition
     */
    public static function top(int $count = 30): Condition
    {
        return self::proxy()->top($count);
    }

    /**
     * limit 限制条数.
     *
     * @param int $offset
     * @param int $count
     *
     * @return \Leevel\Database\Condition
     */
    public static function limit(int $offset = 0, int $count = 0): Condition
    {
        return self::proxy()->limit($offset, $count);
    }

    /**
     * 是否构造一个 FOR UPDATE 查询.
     *
     * @param bool $flag
     *
     * @return \Leevel\Database\Condition
     */
    public static function forUpdate(bool $flag = true): Condition
    {
        return self::proxy()->forUpdate($flag);
    }

    /**
     * 设置查询参数.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Database\Condition
     */
    public static function setOption(string $name, $value): Condition
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 返回查询参数.
     *
     * @return array
     */
    public static function getOption(): array
    {
        return self::proxy()->getOption();
    }

    /**
     * 返回参数绑定.
     *
     * @return array
     */
    public static function getBindParams(): array
    {
        return self::proxy()->getBindParams();
    }

    /**
     * 代理服务
     *
     * @return \Leevel\Database\Manager
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('databases');
    }
}
