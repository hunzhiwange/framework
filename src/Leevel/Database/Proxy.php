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

use Closure;
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
trait Proxy
{
    /**
     * 返回 Pdo 查询连接.
     *
     * @param bool|int $master
     *                         - bool false (读服务器) true (写服务器)
     *                         - int 其它去对应服务器连接ID 0 表示主服务器
     *
     * @return mixed
     */
    public function pdo($master = false)
    {
        return $this->proxy()->pdo($master);
    }

    /**
     * 查询数据记录.
     *
     * @param string     $sql           sql 语句
     * @param array      $bindParams    sql 参数绑定
     * @param bool|int   $master
     * @param int        $fetchType
     * @param null|mixed $fetchArgument
     * @param array      $ctorArgs
     *
     * @return mixed
     */
    public function query(string $sql, array $bindParams = [], $master = false, int $fetchType = PDO::FETCH_OBJ, $fetchArgument = null, array $ctorArgs = [])
    {
        return $this->proxy()->query($sql, $bindParams, $master, $fetchType, $fetchArgument, $ctorArgs);
    }

    /**
     * 执行 sql 语句.
     *
     * @param string $sql        sql 语句
     * @param array  $bindParams sql 参数绑定
     *
     * @return int|string
     */
    public function execute(string $sql, array $bindParams = [])
    {
        return $this->proxy()->execute($sql, $bindParams);
    }

    /**
     * 执行数据库事务
     *
     * @param \Closure $action 事务回调
     *
     * @return mixed
     */
    public function transaction(Closure $action)
    {
        return $this->proxy()->transaction($action);
    }

    /**
     * 启动事务.
     */
    public function beginTransaction(): void
    {
        $this->proxy()->beginTransaction();
    }

    /**
     * 检查是否处于事务中.
     *
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->proxy()->inTransaction();
    }

    /**
     * 用于非自动提交状态下面的查询提交.
     */
    public function commit(): void
    {
        $this->proxy()->commit();
    }

    /**
     * 事务回滚.
     */
    public function rollBack(): void
    {
        $this->proxy()->rollBack();
    }

    /**
     * 获取最后插入 ID 或者列.
     *
     * @param null|string $name 自增序列名
     *
     * @return string
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->proxy()->lastInsertId($name);
    }

    /**
     * 获取最近一次查询的 sql 语句.
     *
     * @return array
     */
    public function lastSql(): array
    {
        return $this->proxy()->lastSql();
    }

    /**
     * 返回影响记录.
     *
     * @return int
     */
    public function numRows(): int
    {
        return $this->proxy()->numRows();
    }

    /**
     * 关闭数据库.
     */
    public function close(): void
    {
        $this->proxy()->close();
    }

    /**
     * 释放 PDO 预处理查询.
     */
    public function freePDOStatement(): void
    {
        $this->proxy()->freePDOStatement();
    }

    /**
     * 关闭数据库连接.
     */
    public function closeConnects(): void
    {
        $this->proxy()->closeConnects();
    }

    /**
     * sql 表达式格式化.
     *
     * @param string $sql
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeExpression(string $sql, string $tableName): string
    {
        return $this->proxy()->normalizeExpression($sql, $tableName);
    }

    /**
     * 表或者字段格式化（支持别名）.
     *
     * @param string      $name
     * @param null|string $alias
     * @param null|string $as
     *
     * @return string
     */
    public function normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null): string
    {
        return $this->proxy()->normalizeTableOrColumn($name, $alias, $as);
    }

    /**
     * 字段格式化.
     *
     * @param string $key
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeColumn(string $key, string $tableName): string
    {
        return $this->proxy()->normalizeColumn($key, $tableName);
    }

    /**
     * 字段值格式化.
     *
     * @param mixed $value
     * @param bool  $quotationMark
     *
     * @return mixed
     */
    public function normalizeColumnValue($value, bool $quotationMark = true)
    {
        return $this->proxy()->normalizeColumnValue($value, $quotationMark);
    }

    /**
     * 分析 sql 类型数据.
     *
     * @param string $sql
     *
     * @return string
     */
    public function normalizeSqlType(string $sql): string
    {
        return $this->proxy()->normalizeSqlType($sql);
    }

    /**
     * 分析绑定参数类型数据.
     *
     * @param mixed $value
     *
     * @return int
     */
    public function normalizeBindParamType($value): int
    {
        return $this->proxy()->normalizeBindParamType($value);
    }

    /**
     * dsn 解析.
     *
     * @param array $option
     *
     * @return string
     */
    public function parseDsn(array $option): string
    {
        return $this->proxy()->parseDsn($option);
    }

    /**
     * 取得数据库表名列表.
     *
     * @param string   $dbName
     * @param bool|int $master
     *
     * @return array
     */
    public function tableNames(string $dbName, $master = false): array
    {
        return $this->proxy()->tableNames($dbName, $master);
    }

    /**
     * 取得数据库表字段信息.
     *
     * @param string   $tableName
     * @param bool|int $master
     *
     * @return array
     */
    public function tableColumns(string $tableName, $master = false): array
    {
        return $this->proxy()->tableColumns($tableName, $master);
    }

    /**
     * sql 字段格式化.
     *
     * @param mixed $name
     *
     * @return string
     */
    public function identifierColumn($name): string
    {
        return $this->proxy()->identifierColumn($name);
    }

    /**
     * 分析 limit.
     *
     * @param null|int $limitCount
     * @param null|int $limitOffset
     *
     * @return string
     */
    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        return $this->proxy()->limitCount($limitCount, $limitOffset);
    }

    /**
     * 查询对象
     *
     * @return \Leevel\Database\Condition
     */
    public function databaseCondition(): Condition
    {
        return $this->proxy()->databaseCondition();
    }

    /**
     * 返回数据库连接对象
     *
     * @return \Leevel\Database\IDatabase
     */
    public function databaseConnect(): IDatabase
    {
        return $this->proxy()->databaseConnect();
    }

    /**
     * 占位符返回本对象
     *
     * @return \Leevel\Database\Select
     */
    public function selfDatabaseSelect(): Select
    {
        return $this->proxy()->selfDatabaseSelect();
    }

    /**
     * 指定返回 SQL 不做任何操作.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return \Leevel\Database\Select
     */
    public function sql(bool $flag = true): Select
    {
        return $this->proxy()->sql($flag);
    }

    /**
     * 设置是否查询主服务器.
     *
     * @param bool $master
     *
     * @return \Leevel\Database\Select
     */
    public function master(bool $master = false): Select
    {
        return $this->proxy()->master($master);
    }

    /**
     * 设置查询参数.
     *
     * @param int        $fetchStyle
     * @param null|mixed $fetchArgument
     * @param array      $ctorArgs
     *
     * @return \Leevel\Database\Select
     */
    public function fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = []): Select
    {
        return $this->proxy()->fetchArgs($fetchStyle, $fetchArgument, $ctorArgs);
    }

    /**
     * 设置以类返会结果.
     *
     * @param string $className
     * @param array  $args
     *
     * @return \Leevel\Database\Select
     */
    public function asClass(string $className, array $args = []): Select
    {
        return $this->proxy()->asClass($className, $args);
    }

    /**
     * 设置默认形式返回.
     *
     * @return \Leevel\Database\Select
     */
    public function asDefault(): Select
    {
        return $this->proxy()->asDefault();
    }

    /**
     * 设置是否以集合返回.
     *
     * @param bool $acollection
     *
     * @return \Leevel\Database\Select
     */
    public function asCollection(bool $acollection = true): Select
    {
        return $this->proxy()->asCollection($acollection);
    }

    /**
     * 原生 sql 查询数据 select.
     *
     * @param null|callable|\Leevel\Database\Select|string $data
     * @param array                                        $bind
     * @param bool                                         $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function select($data = null, array $bind = [], bool $flag = false)
    {
        return $this->proxy()->select($data, $bind, $flag);
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
    public function insert($data, array $bind = [], bool $replace = false, bool $flag = false)
    {
        return $this->proxy()->insert($data, $bind, $replace, $flag);
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
    public function insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)
    {
        return $this->proxy()->insertAll($data, $bind, $replace, $flag);
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
    public function update($data, array $bind = [], bool $flag = false)
    {
        return $this->proxy()->update($data, $bind, $flag);
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
    public function updateColumn(string $column, $value, array $bind = [], bool $flag = false)
    {
        return $this->proxy()->updateColumn($column, $value, $bind, $flag);
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
    public function updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)
    {
        return $this->proxy()->updateIncrease($column, $step, $bind, $flag);
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
    public function updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)
    {
        return $this->proxy()->updateDecrease($column, $step, $bind, $flag);
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
    public function delete(?string $data = null, array $bind = [], bool $flag = false)
    {
        return $this->proxy()->delete($data, $bind, $flag);
    }

    /**
     * 清空表重置自增 ID.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return array|int
     */
    public function truncate(bool $flag = false)
    {
        return $this->proxy()->truncate($flag);
    }

    /**
     * 返回一条记录.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function findOne(bool $flag = false)
    {
        return $this->proxy()->findOne($flag);
    }

    /**
     * 返回所有记录.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function findAll(bool $flag = false)
    {
        return $this->proxy()->findAll($flag);
    }

    /**
     * 返回最后几条记录.
     *
     * @param null|int $num
     * @param bool     $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function find(?int $num = null, bool $flag = false)
    {
        return $this->proxy()->find($num, $flag);
    }

    /**
     * 返回一个字段的值
     *
     * @param string $field
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function value(string $field, bool $flag = false)
    {
        return $this->proxy()->value($field, $flag);
    }

    /**
     * 返回一个字段的值(别名).
     *
     * @param string $field
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function pull(string $field, bool $flag = false)
    {
        return $this->proxy()->pull($field, $flag);
    }

    /**
     * 返回一列数据.
     *
     * @param mixed       $fieldValue
     * @param null|string $fieldKey
     * @param bool        $flag       指示是否不做任何操作只返回 SQL
     *
     * @return array
     */
    public function list($fieldValue, ?string $fieldKey = null, bool $flag = false): array
    {
        return $this->proxy()->list($fieldValue, $fieldKey, $flag);
    }

    /**
     * 数据分块处理.
     *
     * @param int      $count
     * @param \Closure $chunk
     */
    public function chunk(int $count, Closure $chunk): void
    {
        $this->proxy()->chunk($count, $chunk);
    }

    /**
     * 数据分块处理依次回调.
     *
     * @param int     $count
     * @param Closure $each
     */
    public function each(int $count, Closure $each): void
    {
        $this->proxy()->each($count, $each);
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
    public function findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)
    {
        return $this->proxy()->findCount($field, $alias, $flag);
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
    public function findAvg(string $field, string $alias = 'avg_value', bool $flag = false)
    {
        return $this->proxy()->findAvg($field, $alias, $flag);
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
    public function findMax(string $field, string $alias = 'max_value', bool $flag = false)
    {
        return $this->proxy()->findMax($field, $alias, $flag);
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
    public function findMin(string $field, string $alias = 'min_value', bool $flag = false)
    {
        return $this->proxy()->findMin($field, $alias, $flag);
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
    public function findSum(string $field, string $alias = 'sum_value', bool $flag = false)
    {
        return $this->proxy()->findSum($field, $alias, $flag);
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
    public function page(int $currentPage, int $perPage = 10, bool $flag = false, bool $withTotal = true, string $column = '*'): array
    {
        return $this->proxy()->page($currentPage, $perPage, $flag, $withTotal, $column);
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
    public function pageHtml(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []): array
    {
        return $this->proxy()->pageHtml($currentPage, $perPage, $flag, $column, $option);
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
    public function pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): array
    {
        return $this->proxy()->pageMacro($currentPage, $perPage, $flag, $option);
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
    public function pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): array
    {
        return $this->proxy()->pagePrevNext($currentPage, $perPage, $flag, $option);
    }

    /**
     * 取得分页查询记录数量.
     *
     * @param string $cols
     *
     * @return int
     */
    public function pageCount(string $cols = '*'): int
    {
        return $this->proxy()->pageCount($cols);
    }

    /**
     * 获得查询字符串.
     *
     * @param $withLogicGroup
     *
     * @return string
     */
    public function makeSql(bool $withLogicGroup = false): string
    {
        return $this->proxy()->makeSql($withLogicGroup);
    }

    /**
     * 根据分页设置条件.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return \Leevel\Database\Condition
     */
    public function forPage(int $page, int $perPage = 15): Condition
    {
        return $this->proxy()->forPage($page, $perPage);
    }

    /**
     * 时间控制语句开始.
     *
     * @param string $type
     *
     * @return \Leevel\Database\Condition
     */
    public function time(string $type = 'date'): Condition
    {
        return $this->proxy()->time($type);
    }

    /**
     * 时间控制语句结束.
     *
     * @return \Leevel\Database\Condition
     */
    public function endTime(): Condition
    {
        return $this->proxy()->endTime();
    }

    /**
     * 重置查询条件.
     *
     * @param null|string $option
     *
     * @return \Leevel\Database\Condition
     */
    public function reset(?string $option = null): Condition
    {
        return $this->proxy()->reset($option);
    }

    /**
     * prefix 查询.
     *
     * @param string $prefix
     *
     * @return \Leevel\Database\Condition
     */
    public function prefix(string $prefix): Condition
    {
        return $this->proxy()->prefix($prefix);
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
        return $this->proxy()->table($table, $cols);
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
        return $this->proxy()->columns($cols, $table);
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
        return $this->proxy()->setColumns($cols, $table);
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
        return $this->proxy()->where(...$cond);
    }

    /**
     * orWhere 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function orWhere(...$cond): Condition
    {
        return $this->proxy()->orWhere(...$cond);
    }

    /**
     * Where 原生查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public function whereRaw(string $raw): Condition
    {
        return $this->proxy()->whereRaw($raw);
    }

    /**
     * Where 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public function orWhereRaw(string $raw): Condition
    {
        return $this->proxy()->orWhereRaw($raw);
    }

    /**
     * exists 方法支持
     *
     * @param mixed $exists
     *
     * @return \Leevel\Database\Condition
     */
    public function whereExists($exists): Condition
    {
        return $this->proxy()->whereExists($exists);
    }

    /**
     * not exists 方法支持
     *
     * @param mixed $exists
     *
     * @return \Leevel\Database\Condition
     */
    public function whereNotExists($exists): Condition
    {
        return $this->proxy()->whereNotExists($exists);
    }

    /**
     * whereBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereBetween(...$cond): Condition
    {
        return $this->proxy()->whereBetween(...$cond);
    }

    /**
     * whereNotBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereNotBetween(...$cond): Condition
    {
        return $this->proxy()->whereNotBetween(...$cond);
    }

    /**
     * whereNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereNull(...$cond): Condition
    {
        return $this->proxy()->whereNull(...$cond);
    }

    /**
     * whereNotNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereNotNull(...$cond): Condition
    {
        return $this->proxy()->whereNotNull(...$cond);
    }

    /**
     * whereIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereIn(...$cond): Condition
    {
        return $this->proxy()->whereIn(...$cond);
    }

    /**
     * whereNotIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereNotIn(...$cond): Condition
    {
        return $this->proxy()->whereNotIn(...$cond);
    }

    /**
     * whereLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereLike(...$cond): Condition
    {
        return $this->proxy()->whereLike(...$cond);
    }

    /**
     * whereNotLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereNotLike(...$cond): Condition
    {
        return $this->proxy()->whereNotLike(...$cond);
    }

    /**
     * whereDate 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereDate(...$cond): Condition
    {
        return $this->proxy()->whereDate(...$cond);
    }

    /**
     * whereDay 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereDay(...$cond): Condition
    {
        return $this->proxy()->whereDay(...$cond);
    }

    /**
     * whereMonth 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereMonth(...$cond): Condition
    {
        return $this->proxy()->whereMonth(...$cond);
    }

    /**
     * whereYear 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function whereYear(...$cond): Condition
    {
        return $this->proxy()->whereYear(...$cond);
    }

    /**
     * 参数绑定支持
     *
     * @param mixed      $names
     * @param null|mixed $value
     * @param int        $type
     *
     * @return \Leevel\Database\Condition
     */
    public function bind($names, $value = null, int $type = PDO::PARAM_STR): Condition
    {
        return $this->proxy()->bind($names, $value, $type);
    }

    /**
     * index 强制索引（或者忽略索引）.
     *
     * @param array|string $indexs
     * @param string       $type
     *
     * @return \Leevel\Database\Condition
     */
    public function forceIndex($indexs, $type = 'FORCE'): Condition
    {
        return $this->proxy()->forceIndex($indexs, $type);
    }

    /**
     * index 忽略索引.
     *
     * @param array|string $indexs
     *
     * @return \Leevel\Database\Condition
     */
    public function ignoreIndex($indexs): Condition
    {
        return $this->proxy()->ignoreIndex($indexs);
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
    public function join($table, $cols, ...$cond): Condition
    {
        return $this->proxy()->join($table, $cols, ...$cond);
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
    public function innerJoin($table, $cols, ...$cond): Condition
    {
        return $this->proxy()->innerJoin($table, $cols, ...$cond);
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
    public function leftJoin($table, $cols, ...$cond): Condition
    {
        return $this->proxy()->leftJoin($table, $cols, ...$cond);
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
    public function rightJoin($table, $cols, ...$cond): Condition
    {
        return $this->proxy()->rightJoin($table, $cols, ...$cond);
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
    public function fullJoin($table, $cols, ...$cond): Condition
    {
        return $this->proxy()->fullJoin($table, $cols, ...$cond);
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
    public function crossJoin($table, $cols, ...$cond): Condition
    {
        return $this->proxy()->crossJoin($table, $cols, ...$cond);
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
    public function naturalJoin($table, $cols, ...$cond): Condition
    {
        return $this->proxy()->naturalJoin($table, $cols, ...$cond);
    }

    /**
     * 添加一个 UNION 查询.
     *
     * @param array|callable|string $selects
     * @param string                $type
     *
     * @return \Leevel\Database\Condition
     */
    public function union($selects, string $type = 'UNION'): Condition
    {
        return $this->proxy()->union($selects, $type);
    }

    /**
     * 添加一个 UNION ALL 查询.
     *
     * @param array|callable|string $selects
     *
     * @return \Leevel\Database\Condition
     */
    public function unionAll($selects): Condition
    {
        return $this->proxy()->unionAll($selects);
    }

    /**
     * 指定 GROUP BY 子句.
     *
     * @param array|string $expression
     *
     * @return \Leevel\Database\Condition
     */
    public function groupBy($expression): Condition
    {
        return $this->proxy()->groupBy($expression);
    }

    /**
     * 添加一个 HAVING 条件
     * < 参数规范参考 where()方法 >.
     *
     * @param array $data
     *
     * @return \Leevel\Database\Condition
     */
    public function having(...$cond): Condition
    {
        return $this->proxy()->having(...$cond);
    }

    /**
     * orHaving 查询条件.
     *
     * @param array $data
     *
     * @return \Leevel\Database\Condition
     */
    public function orHaving(...$cond): Condition
    {
        return $this->proxy()->orHaving(...$cond);
    }

    /**
     * Having 原生查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public function havingRaw(string $raw): Condition
    {
        return $this->proxy()->havingRaw($raw);
    }

    /**
     * Having 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return \Leevel\Database\Condition
     */
    public function orHavingRaw(string $raw): Condition
    {
        return $this->proxy()->orHavingRaw($raw);
    }

    /**
     * havingBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingBetween(...$cond): Condition
    {
        return $this->proxy()->havingBetween(...$cond);
    }

    /**
     * havingNotBetween 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingNotBetween(...$cond): Condition
    {
        return $this->proxy()->havingNotBetween(...$cond);
    }

    /**
     * havingNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingNull(...$cond): Condition
    {
        return $this->proxy()->havingNull(...$cond);
    }

    /**
     * havingNotNull 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingNotNull(...$cond): Condition
    {
        return $this->proxy()->havingNotNull(...$cond);
    }

    /**
     * havingIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingIn(...$cond): Condition
    {
        return $this->proxy()->havingIn(...$cond);
    }

    /**
     * havingNotIn 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingNotIn(...$cond): Condition
    {
        return $this->proxy()->havingNotIn(...$cond);
    }

    /**
     * havingLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingLike(...$cond): Condition
    {
        return $this->proxy()->havingLike(...$cond);
    }

    /**
     * havingNotLike 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingNotLike(...$cond): Condition
    {
        return $this->proxy()->havingNotLike(...$cond);
    }

    /**
     * havingDate 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingDate(...$cond): Condition
    {
        return $this->proxy()->havingDate(...$cond);
    }

    /**
     * havingDay 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingDay(...$cond): Condition
    {
        return $this->proxy()->havingDay(...$cond);
    }

    /**
     * havingMonth 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingMonth(...$cond): Condition
    {
        return $this->proxy()->havingMonth(...$cond);
    }

    /**
     * havingYear 查询条件.
     *
     * @param array ...$cond
     *
     * @return \Leevel\Database\Condition
     */
    public function havingYear(...$cond): Condition
    {
        return $this->proxy()->havingYear(...$cond);
    }

    /**
     * 添加排序.
     *
     * @param array|string $expression
     * @param string       $orderDefault
     *
     * @return \Leevel\Database\Condition
     */
    public function orderBy($expression, string $orderDefault = 'ASC'): Condition
    {
        return $this->proxy()->orderBy($expression, $orderDefault);
    }

    /**
     * 最近排序数据.
     *
     * @param string $field
     *
     * @return \Leevel\Database\Condition
     */
    public function latest(string $field = 'create_at'): Condition
    {
        return $this->proxy()->latest($field);
    }

    /**
     * 最早排序数据.
     *
     * @param string $field
     *
     * @return \Leevel\Database\Condition
     */
    public function oldest(string $field = 'create_at'): Condition
    {
        return $this->proxy()->oldest($field);
    }

    /**
     * 创建一个 SELECT DISTINCT 查询.
     *
     * @param bool $flag 指示是否是一个 SELECT DISTINCT 查询（默认 true）
     *
     * @return \Leevel\Database\Condition
     */
    public function distinct(bool $flag = true): Condition
    {
        return $this->proxy()->distinct($flag);
    }

    /**
     * 总记录数.
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public function count(string $field = '*', string $alias = 'row_count'): Condition
    {
        return $this->proxy()->count($field, $alias);
    }

    /**
     * 平均数.
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public function avg(string $field, string $alias = 'avg_value'): Condition
    {
        return $this->proxy()->avg($field, $alias);
    }

    /**
     * 最大值
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public function max(string $field, string $alias = 'max_value'): Condition
    {
        return $this->proxy()->max($field, $alias);
    }

    /**
     * 最小值
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public function min(string $field, string $alias = 'min_value'): Condition
    {
        return $this->proxy()->min($field, $alias);
    }

    /**
     * 合计
     *
     * @param string $field
     * @param string $alias
     *
     * @return \Leevel\Database\Condition
     */
    public function sum(string $field, string $alias = 'sum_value'): Condition
    {
        return $this->proxy()->sum($field, $alias);
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     *
     * @return \Leevel\Database\Condition
     */
    public function one(): Condition
    {
        return $this->proxy()->one();
    }

    /**
     * 指示查询所有符合条件的记录.
     *
     * @return \Leevel\Database\Condition
     */
    public function all(): Condition
    {
        return $this->proxy()->all();
    }

    /**
     * 查询几条记录.
     *
     * @param int $count
     *
     * @return \Leevel\Database\Condition
     */
    public function top(int $count = 30): Condition
    {
        return $this->proxy()->top($count);
    }

    /**
     * limit 限制条数.
     *
     * @param int $offset
     * @param int $count
     *
     * @return \Leevel\Database\Condition
     */
    public function limit(int $offset = 0, int $count = 0): Condition
    {
        return $this->proxy()->limit($offset, $count);
    }

    /**
     * 是否构造一个 FOR UPDATE 查询.
     *
     * @param bool $flag
     *
     * @return \Leevel\Database\Condition
     */
    public function forUpdate(bool $flag = true): Condition
    {
        return $this->proxy()->forUpdate($flag);
    }

    /**
     * 设置查询参数.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Database\Condition
     */
    public function setOption(string $name, $value): Condition
    {
        return $this->proxy()->setOption($name, $value);
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
