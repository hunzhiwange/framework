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

use InvalidArgumentException;
use Leevel\Event\IDispatch;
use Leevel\Manager\Manager as Managers;
use Leevel\Protocol\Pool\IConnection;
use RuntimeException;

/**
 * Database 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.02.15
 *
 * @version 1.0
 *
 * @method static pdo($master = false)                                                                                                         返回 Pdo 查询连接.
 * @method static query(string $sql, array $bindParams = [], $master = false, int $fetchType = 5, $fetchArgument = null, array $ctorArgs = []) 查询数据记录.
 * @method static execute(string $sql, array $bindParams = [])                                                                                 执行 sql 语句.
 * @method static transaction(\Closure $action)                                                                                                执行数据库事务
 * @method static void beginTransaction()                                                                                                      启动事务.
 * @method static bool inTransaction()                                                                                                         检查是否处于事务中.
 * @method static void commit()                                                                                                                用于非自动提交状态下面的查询提交.
 * @method static void rollBack()                                                                                                              事务回滚.
 * @method static string lastInsertId(?string $name = null)                                                                                    获取最后插入 ID 或者列.
 * @method static array lastSql()                                                                                                              获取最近一次查询的 sql 语句.
 * @method static int numRows()                                                                                                                返回影响记录.
 * @method static void close()                                                                                                                 关闭数据库.
 * @method static void freePDOStatement()                                                                                                      释放 PDO 预处理查询.
 * @method static void closeConnects()                                                                                                         关闭数据库连接.
 * @method static string normalizeExpression(string $sql, string $tableName)                                                                   sql 表达式格式化.
 * @method static string normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null)                                       表或者字段格式化（支持别名）.
 * @method static string normalizeColumn(string $key, string $tableName)                                                                       字段格式化.
 * @method static normalizeColumnValue($value, bool $quotationMark = true)                                                                     字段值格式化.
 * @method static string normalizeSqlType(string $sql)                                                                                         分析 sql 类型数据.
 * @method static int normalizeBindParamType($value)                                                                                           分析绑定参数类型数据.
 * @method static string parseDsn(array $option)                                                                                               dsn 解析.
 * @method static array tableNames(string $dbName, $master = false)                                                                            取得数据库表名列表.
 * @method static array tableColumns(string $tableName, $master = false)                                                                       取得数据库表字段信息.
 * @method static string identifierColumn($name)                                                                                               sql 字段格式化.
 * @method static string limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                         分析 limit.
 * @method static \Leevel\Database\Condition databaseCondition()                                                                               查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                 返回数据库连接对象.
 * @method static \Leevel\Database\Select selfDatabaseSelect()                                                                                 占位符返回本对象.
 * @method static \Leevel\Database\Select sql(bool $flag = true)                                                                               指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Select master(bool $master = false)                                                                         设置是否查询主服务器.
 * @method static \Leevel\Database\Select fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = [])                              设置查询参数.
 * @method static \Leevel\Database\Select asClass(string $className, array $args = [])                                                         设置以类返会结果.
 * @method static \Leevel\Database\Select asDefault()                                                                                          设置默认形式返回.
 * @method static \Leevel\Database\Select asCollection(bool $acollection = true)                                                               设置是否以集合返回.
 * @method static select($data = null, array $bind = [], bool $flag = false)                                                                   原生 sql 查询数据 select.
 * @method static insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                                   插入数据 insert (支持原生 sql).
 * @method static insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                          批量插入数据 insertAll.
 * @method static update($data, array $bind = [], bool $flag = false)                                                                          更新数据 update (支持原生 sql).
 * @method static updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                                   更新某个字段的值
 * @method static updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                          字段递增.
 * @method static updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                          字段减少.
 * @method static delete(?string $data = null, array $bind = [], bool $flag = false)                                                           删除数据 delete (支持原生 sql).
 * @method static truncate(bool $flag = false)                                                                                                 清空表重置自增 ID.
 * @method static findOne(bool $flag = false)                                                                                                  返回一条记录.
 * @method static findAll(bool $flag = false)                                                                                                  返回所有记录.
 * @method static find(?int $num = null, bool $flag = false)                                                                                   返回最后几条记录.
 * @method static value(string $field, bool $flag = false)                                                                                     返回一个字段的值
 * @method static pull(string $field, bool $flag = false)                                                                                      返回一个字段的值(别名).
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                                        返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                      数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                        数据分块处理依次回调.
 * @method static findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                              总记录数.
 * @method static findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                      平均数.
 * @method static findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                      最大值.
 * @method static findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                      最小值.
 * @method static findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                      合计.
 * @method static array page(int $currentPage, int $perPage = 10, bool $flag = false, bool $withTotal = true, string $column = '*')            分页查询.
 * @method static array pageHtml(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = [])            分页查询. 可以渲染 HTML.
 * @method static array pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                 创建一个无限数据的分页查询.
 * @method static array pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                              创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                            取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                 获得查询字符串.
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 15)                                                                根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                                          时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                            时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                                        重置查询条件.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                               prefix 查询.
 * @method static \Leevel\Database\Select table($table, $cols = '*')                                                                           添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                            获取表别名.
 * @method static \Leevel\Database\Select columns($cols = '*', ?string $table = null)                                                          添加字段.
 * @method static \Leevel\Database\Select setColumns($cols = '*', ?string $table = null)                                                       设置字段.
 * @method static \Leevel\Database\Select where(...$cond)                                                                                      where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                                    orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                                Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                              Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                                 exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                              not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                               whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                            whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                                  whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                               whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                                    whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                                 whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                                  whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                               whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                                  whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                                   whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                                 whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                                  whereYear 查询条件.
 * @method static \Leevel\Database\Select bind($names, $value = null, int $type = 2)                                                           参数绑定支持
 * @method static \Leevel\Database\Select forceIndex($indexs, $type = 'FORCE')                                                                 index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex($indexs)                                                                                 index 忽略索引.
 * @method static \Leevel\Database\Select join($table, $cols, ...$cond)                                                                        join 查询.
 * @method static \Leevel\Database\Select innerJoin($table, $cols, ...$cond)                                                                   innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin($table, $cols, ...$cond)                                                                    leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin($table, $cols, ...$cond)                                                                   rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin($table, $cols, ...$cond)                                                                    fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin($table, $cols, ...$cond)                                                                   crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin($table, $cols, ...$cond)                                                                 naturalJoin 查询.
 * @method static \Leevel\Database\Select union($selects, string $type = 'UNION')                                                              添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll($selects)                                                                                   添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy($expression)                                                                                 指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                                     添加一个 HAVING 条件 < 参数规范参考 where()方法 >.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                                   orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                               Having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                             Having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                              havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                           havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                                 havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                              havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                                   havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                                havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                                 havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                              havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                                 havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                                  havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                                havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                                 havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy($expression, string $orderDefault = 'ASC')                                                   添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                                  最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                                  最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                                          创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                              总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                                      平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                                      最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                                      最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                                      合计
 * @method static \Leevel\Database\Select one()                                                                                                指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                                指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                                 查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                               limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                                         是否构造一个 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select setOption(string $name, $value)                                                                      设置查询参数.
 * @method static array getOption()                                                                                                            返回查询参数.
 * @method static array getBindParams()                                                                                                        返回参数绑定.
 */
class Manager extends Managers
{
    /**
     * 当前协程事务服务标识.
     *
     * @var string
     */
    const TRANSACTION_SERVICE = 'transaction.service';

    /**
     * 设置当前协程事务中的连接.
     *
     * @param \Leevel\Protocol\Pool\IConnection $connection
     */
    public function setTransactionConnection(IConnection $connection): void
    {
        $this->container->instance(self::TRANSACTION_SERVICE, $connection, true);
    }

    /**
     * 是否处于当前协程事务中.
     *
     * @return bool
     */
    public function inTransactionConnection(): bool
    {
        return $this->container->exists(self::TRANSACTION_SERVICE);
    }

    /**
     * 获取当前协程事务中的连接.
     *
     * @return \Leevel\Protocol\Pool\IConnection
     */
    public function getTransactionConnection(): IConnection
    {
        $connection = $this->container->make(self::TRANSACTION_SERVICE);

        if (!is_object($connection) || !$connection instanceof IConnection) {
            $e = 'There was no active transaction.';

            throw new RuntimeException($e);
        }

        return $connection;
    }

    /**
     * 删除当前协程事务中的连接.
     */
    public function removeTransactionConnection(): void
    {
        $this->container->remove(self::TRANSACTION_SERVICE);
    }

    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'database';
    }

    /**
     * 创建 MySQL 连接.
     *
     * @param array $option
     *
     * @return \Leevel\Database\Mysql
     */
    protected function makeConnectMysql(array $option = []): Mysql
    {
        return new Mysql(
            $this->normalizeConnectOption('mysql', $option),
            $this->container->make(IDispatch::class),
            $this->container->getCoroutine() ? $this : null,
        );
    }

    /**
     * 创建 mysqlPool 缓存.
     *
     * @param array $options
     *
     * @return \Leevel\Database\MysqlPool
     */
    protected function makeConnectMysqlPool(array $options = []): MysqlPool
    {
        if (!$this->container->getCoroutine()) {
            $e = 'Mysql pool can only be used in swoole scenarios.';

            throw new RuntimeException($e);
        }

        $mysqlPool = $this->container->make('mysql.pool');

        return new MysqlPool($mysqlPool);
    }

    /**
     * 读取默认配置.
     *
     * @param string $connect
     * @param array  $extendOption
     *
     * @return array
     */
    protected function normalizeConnectOption(string $connect, array $extendOption = []): array
    {
        return $this->parseDatabaseOption(
            parent::normalizeConnectOption($connect, $extendOption)
        );
    }

    /**
     * 分析数据库配置参数.
     *
     * @param array $option
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function parseDatabaseOption(array $option): array
    {
        $temp = $option;
        $type = ['distributed', 'separate', 'driver', 'master', 'slave'];

        foreach (array_keys($option) as $t) {
            if (in_array($t, $type, true)) {
                if (isset($temp[$t])) {
                    unset($temp[$t]);
                }
            } elseif (isset($option[$t])) {
                unset($option[$t]);
            }
        }

        foreach (['master', 'slave'] as $t) {
            if (!is_array($option[$t])) {
                $e = sprintf('Database option `%s` must be an array.', $t);

                throw new InvalidArgumentException($e);
            }
        }

        $option['master'] = array_merge($option['master'], $temp);

        if (!$option['distributed']) {
            $option['slave'] = [];
        } elseif ($option['slave']) {
            if (count($option['slave']) === count($option['slave'], COUNT_RECURSIVE)) {
                $option['slave'] = [$option['slave']];
            }

            foreach ($option['slave'] as &$slave) {
                $slave = array_merge($slave, $temp);
            }
        }

        return $option;
    }
}
