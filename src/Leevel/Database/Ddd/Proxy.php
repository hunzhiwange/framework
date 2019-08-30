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

namespace Leevel\Database\Ddd;

use Closure;
use Leevel\Database\IDatabase;
use PDO;

/**
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.28
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
     * 查询对象.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function databaseCondition(): Select
    {
        $this->proxy()->databaseCondition();

        return $this;
    }

    /**
     * 返回数据库连接对象.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function databaseConnect(): IDatabase
    {
        return $this->proxy()->databaseConnect();
    }

    /**
     * 占位符返回本对象.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function selfSelect(): Select
    {
        $this->proxy()->selfSelect();

        return $this;
    }

    /**
     * 指定返回 SQL 不做任何操作.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function sql(bool $flag = true): Select
    {
        $this->proxy()->sql($flag);

        return $this;
    }

    /**
     * 设置是否查询主服务器.
     *
     * @param bool $master
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function master(bool $master = false): Select
    {
        $this->proxy()->master($master);

        return $this;
    }

    /**
     * 设置查询参数.
     *
     * @param int        $fetchStyle
     * @param null|mixed $fetchArgument
     * @param array      $ctorArgs
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = []): Select
    {
        $this->proxy()->fetchArgs($fetchStyle, $fetchArgument, $ctorArgs);

        return $this;
    }

    /**
     * 设置以类返会结果.
     *
     * @param string $className
     * @param array  $args
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function asClass(string $className, array $args = []): Select
    {
        $this->proxy()->asClass($className, $args);

        return $this;
    }

    /**
     * 设置默认形式返回.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function asDefault(): Select
    {
        $this->proxy()->asDefault();

        return $this;
    }

    /**
     * 设置是否以集合返回.
     *
     * @param bool $acollection
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function asCollection(bool $acollection = true): Select
    {
        $this->proxy()->asCollection($acollection);

        return $this;
    }

    /**
     * 原生 sql 查询数据 select.
     *
     * @param null|callable|\Leevel\Database\Ddd\Select|string $data
     * @param array                                            $bind
     * @param bool                                             $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function select($data = null, array $bind = [], bool $flag = false)
    {
        $result = $this->proxy()->select($data, $bind, $flag);

        return $this->normalizeSelectResult($result);
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
        $result = $this->proxy()->findOne($flag);

        return $this->normalizeSelectResult($result);
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
        $result = $this->proxy()->findAll($flag);

        return $this->normalizeSelectResult($result);
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
        $result = $this->proxy()->find($num, $flag);

        return $this->normalizeSelectResult($result);
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
        $result = $this->proxy()->page($currentPage, $perPage, $flag, $withTotal, $column);

        return $this->normalizeSelectResult($result);
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
        $result = $this->proxy()->pageHtml($currentPage, $perPage, $flag, $column, $option);

        return $this->normalizeSelectResult($result);
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
        $result = $this->proxy()->pageMacro($currentPage, $perPage, $flag, $option);

        return $this->normalizeSelectResult($result);
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
        $result = $this->proxy()->pagePrevNext($currentPage, $perPage, $flag, $option);

        return $this->normalizeSelectResult($result);
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
     */
    public function time(string $type = 'date'): Select
    {
        $this->proxy()->time($type);

        return $this;
    }

    /**
     * 时间控制语句结束.
     *
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
     */
    public function sum(string $field, string $alias = 'sum_value'): Select
    {
        $this->proxy()->sum($field, $alias);

        return $this;
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function one(): Select
    {
        $this->proxy()->one();

        return $this;
    }

    /**
     * 指示查询所有符合条件的记录.
     *
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
     * @return \Leevel\Database\Ddd\Select
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
