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
 * IDatabase 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 *
 * @method static \Leevel\Database\Condition databaseCondition()                                                                    查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                      返回数据库连接对象.
 * @method static \Leevel\Database\Select selfDatabaseSelect()                                                                      占位符返回本对象.
 * @method static \Leevel\Database\Select sql(bool $flag = true)                                                                    指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Select master(bool $master = false)                                                              设置是否查询主服务器.
 * @method static \Leevel\Database\Select fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = [])                   设置查询参数.
 * @method static \Leevel\Database\Select asClass(string $className, array $args = [])                                              设置以类返会结果.
 * @method static \Leevel\Database\Select asDefault()                                                                               设置默认形式返回.
 * @method static \Leevel\Database\Select asCollection(bool $acollection = true)                                                    设置是否以集合返回.
 * @method static select($data = null, array $bind = [], bool $flag = false)                                                        原生 sql 查询数据 select.
 * @method static insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                        插入数据 insert (支持原生 sql).
 * @method static insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                               批量插入数据 insertAll.
 * @method static update($data, array $bind = [], bool $flag = false)                                                               更新数据 update (支持原生 sql).
 * @method static updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                        更新某个字段的值
 * @method static updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                               字段递增.
 * @method static updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                               字段减少.
 * @method static delete(?string $data = null, array $bind = [], bool $flag = false)                                                删除数据 delete (支持原生 sql).
 * @method static truncate(bool $flag = false)                                                                                      清空表重置自增 ID.
 * @method static findOne(bool $flag = false)                                                                                       返回一条记录.
 * @method static findAll(bool $flag = false)                                                                                       返回所有记录.
 * @method static find(?int $num = null, bool $flag = false)                                                                        返回最后几条记录.
 * @method static value(string $field, bool $flag = false)                                                                          返回一个字段的值
 * @method static pull(string $field, bool $flag = false)                                                                           返回一个字段的值(别名).
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                             返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                           数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                             数据分块处理依次回调.
 * @method static findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                   总记录数.
 * @method static findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                           平均数.
 * @method static findMax(string $field, string $alias = 'max_value', bool $flag = false)                                           最大值.
 * @method static findMin(string $field, string $alias = 'min_value', bool $flag = false)                                           最小值.
 * @method static findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                           合计.
 * @method static array page(int $currentPage, int $perPage = 10, bool $flag = false, bool $withTotal = true, string $column = '*') 分页查询.
 * @method static array pageHtml(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []) 分页查询. 可以渲染 HTML.
 * @method static array pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                      创建一个无限数据的分页查询.
 * @method static array pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                   创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                 取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                      获得查询字符串.
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 15)                                                     根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                               时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                 时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                             重置查询条件.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                    prefix 查询.
 * @method static \Leevel\Database\Select table($table, $cols = '*')                                                                添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                 获取表别名.
 * @method static \Leevel\Database\Select columns($cols = '*', ?string $table = null)                                               添加字段.
 * @method static \Leevel\Database\Select setColumns($cols = '*', ?string $table = null)                                            设置字段.
 * @method static \Leevel\Database\Select where(...$cond)                                                                           where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                         orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                     Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                   Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                      exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                   not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                    whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                 whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                       whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                    whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                         whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                      whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                       whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                    whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                       whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                        whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                      whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                       whereYear 查询条件.
 * @method static \Leevel\Database\Select bind($names, $value = null, int $type = 2)                                                参数绑定支持
 * @method static \Leevel\Database\Select forceIndex($indexs, $type = 'FORCE')                                                      index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex($indexs)                                                                      index 忽略索引.
 * @method static \Leevel\Database\Select join($table, $cols, ...$cond)                                                             join 查询.
 * @method static \Leevel\Database\Select innerJoin($table, $cols, ...$cond)                                                        innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin($table, $cols, ...$cond)                                                         leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin($table, $cols, ...$cond)                                                        rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin($table, $cols, ...$cond)                                                         fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin($table, $cols, ...$cond)                                                        crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin($table, $cols, ...$cond)                                                      naturalJoin 查询.
 * @method static \Leevel\Database\Select union($selects, string $type = 'UNION')                                                   添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll($selects)                                                                        添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy($expression)                                                                      指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                          添加一个 HAVING 条件 < 参数规范参考 where()方法 >.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                        orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                    Having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                  Having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                   havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                      havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                   havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                        havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                     havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                      havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                   havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                      havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                       havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                     havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                      havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy($expression, string $orderDefault = 'ASC')                                        添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                       最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                       最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                               创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                   总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                           平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                           最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                           最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                           合计
 * @method static \Leevel\Database\Select one()                                                                                     指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                     指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                      查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                    limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                              是否构造一个 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select setOption(string $name, $value)                                                           设置查询参数.
 * @method static array getOption()                                                                                                 返回查询参数.
 * @method static array getBindParams()                                                                                             返回参数绑定.
 */
interface IDatabase
{
    /**
     * 断线重连尝试次数.
     *
     * @var int
     */
    const RECONNECT_MAX = 3;

    /**
     * 主服务 PDO 标识.
     *
     * @var int
     */
    const MASTER = 999999999;

    /**
     * SQL 日志事件.
     *
     * @var string
     */
    const SQL_EVENT = 'database.sql';

    /**
     * 返回 Pdo 查询连接.
     *
     * @param bool|int $master
     *                         - bool,false (读服务器),true (写服务器)
     *                         - int,其它去对应服务器连接 ID,0 表示主服务器
     *
     * @return mixed
     */
    public function pdo($master = false);

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
    public function query(string $sql, array $bindParams = [], $master = false, int $fetchType = PDO::FETCH_OBJ, $fetchArgument = null, array $ctorArgs = []);

    /**
     * 执行 sql 语句.
     *
     * @param string $sql        sql 语句
     * @param array  $bindParams sql 参数绑定
     *
     * @return int|string
     */
    public function execute(string $sql, array $bindParams = []);

    /**
     * 执行数据库事务
     *
     * @param \Closure $action 事务回调
     *
     * @return mixed
     */
    public function transaction(Closure $action);

    /**
     * 启动事务.
     */
    public function beginTransaction(): void;

    /**
     * 检查是否处于事务中.
     *
     * @return bool
     */
    public function inTransaction(): bool;

    /**
     * 用于非自动提交状态下面的查询提交.
     */
    public function commit(): void;

    /**
     * 事务回滚.
     */
    public function rollBack(): void;

    /**
     * 设置是否启用部分事务.
     *
     * @param bool $savepoints
     */
    public function setSavepoints(bool $savepoints): void;

    /**
     * 获取是否启用部分事务.
     *
     * @return bool
     */
    public function hasSavepoints(): bool;

    /**
     * 获取最后插入 ID 或者列.
     *
     * @param null|string $name 自增序列名
     *
     * @return string
     */
    public function lastInsertId(?string $name = null): string;

    /**
     * 获取最近一次查询的 sql 语句.
     *
     * @return string
     */
    public function getLastSql(): string;

    /**
     * 返回影响记录.
     *
     * @return int
     */
    public function numRows(): int;

    /**
     * 关闭数据库.
     */
    public function close(): void;

    /**
     * 释放 PDO 预处理查询.
     */
    public function freePDOStatement(): void;

    /**
     * 关闭数据库连接.
     */
    public function closeConnects(): void;

    /**
     * 归还连接池.
     */
    public function release(): void;

    /**
     * sql 表达式格式化.
     *
     * @param string $sql
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeExpression(string $sql, string $tableName): string;

    /**
     * 表或者字段格式化（支持别名）.
     *
     * @param string      $name
     * @param null|string $alias
     * @param null|string $as
     *
     * @return string
     */
    public function normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null): string;

    /**
     * 字段格式化.
     *
     * @param string $key
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeColumn(string $key, string $tableName): string;

    /**
     * 字段值格式化.
     *
     * @param mixed $value
     * @param bool  $quotationMark
     *
     * @return mixed
     */
    public function normalizeColumnValue($value, bool $quotationMark = true);

    /**
     * 分析 sql 类型数据.
     *
     * @param string $sql
     *
     * @return string
     */
    public function normalizeSqlType(string $sql): string;

    /**
     * 分析绑定参数类型数据.
     *
     * @param mixed $value
     *
     * @return int
     */
    public function normalizeBindParamType($value): int;

    /**
     * dsn 解析.
     *
     * @param array $option
     *
     * @return string
     */
    public function parseDsn(array $option): string;

    /**
     * 取得数据库表名列表.
     *
     * @param string   $dbName
     * @param bool|int $master
     *
     * @return array
     */
    public function tableNames(string $dbName, $master = false): array;

    /**
     * 取得数据库表字段信息.
     *
     * @param string   $tableName
     * @param bool|int $master
     *
     * @return array
     */
    public function tableColumns(string $tableName, $master = false): array;

    /**
     * sql 字段格式化.
     *
     * @param mixed $name
     *
     * @return string
     */
    public function identifierColumn($name): string;

    /**
     * 分析 limit.
     *
     * @param null|int $limitCount
     * @param null|int $limitOffset
     *
     * @return string
     */
    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string;
}
