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

namespace Leevel\Database;

use InvalidArgumentException;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Manager\Manager as Managers;
use Leevel\Protocol\Pool\IConnection;
use RuntimeException;

/**
 * 数据库管理器.
 *
 * @method static void setCache(?\Leevel\Cache\Manager $cache)                                                                                                             设置缓存管理.
 * @method static ?\Leevel\Cache\Manager getCache()                                                                                                                        获取缓存管理.
 * @method static \Leevel\Database\Select databaseSelect()                                                                                                                 返回查询对象.
 * @method static mixed pdo($master = false)                                                                                                                               返回 PDO 查询连接.
 * @method static mixed query(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null)     查询数据记录.
 * @method static array procedure(string $sql, array $bindParams = [], $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null) 查询存储过程数据记录.
 * @method static mixed execute(string $sql, array $bindParams = [])                                                                                                       执行 SQL 语句.
 * @method static \Generator cursor(string $sql, array $bindParams = [], $master = false)                                                                                  游标查询.
 * @method static \PDOStatement prepare(string $sql, array $bindParams = [], $master = false)                                                                              SQL 预处理.
 * @method static mixed transaction(\Closure $action)                                                                                                                      执行数据库事务.
 * @method static void beginTransaction()                                                                                                                                  启动事务.
 * @method static bool inTransaction()                                                                                                                                     检查是否处于事务中.
 * @method static void commit()                                                                                                                                            用于非自动提交状态下面的查询提交.
 * @method static void rollBack()                                                                                                                                          事务回滚.
 * @method static string lastInsertId(?string $name = null)                                                                                                                获取最后插入 ID 或者列.
 * @method static ?string getLastSql()                                                                                                                                     获取最近一次查询的 SQL 语句.
 * @method static int numRows()                                                                                                                                            返回影响记录.
 * @method static void close()                                                                                                                                             关闭数据库.
 * @method static void freePDOStatement()                                                                                                                                  释放 PDO 预处理查询.
 * @method static void closeConnects()                                                                                                                                     关闭数据库连接.
 * @method static string getRawSql(string $sql, array $bindParams)                                                                                                         从 PDO 预处理语句中获取原始 SQL 查询字符串.
 * @method static string parseDsn(array $option)                                                                                                                           DSN 解析.
 * @method static array getTableNames(string $dbName, $master = false)                                                                                                     取得数据库表名列表.
 * @method static array getTableColumns(string $tableName, $master = false)                                                                                                取得数据库表字段信息.
 * @method static string identifierColumn($name)                                                                                                                           SQL 字段格式化.
 * @method static string limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                                                     分析查询条数.
 * @method static \Leevel\Database\Condition databaseCondition()                                                                                                           查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                                             返回数据库连接对象.
 * @method static \Leevel\Database\Select sql(bool $flag = true)                                                                                                           指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Select master($master = false)                                                                                                          设置是否查询主服务器.
 * @method static \Leevel\Database\Select asSome(?\Closure $asSome = null, array $args = [])                                                                               设置以某种包装返会结果.
 * @method static \Leevel\Database\Select asArray(?\Closure $asArray = null)                                                                                               设置返会结果为数组.
 * @method static \Leevel\Database\Select asCollection(bool $asCollection = true)                                                                                          设置是否以集合返回.
 * @method static mixed select($data = null, array $bind = [], bool $flag = false)                                                                                         原生 SQL 查询数据.
 * @method static mixed insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                                                         插入数据 insert (支持原生 SQL).
 * @method static mixed insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                                                批量插入数据 insertAll.
 * @method static mixed update($data, array $bind = [], bool $flag = false)                                                                                                更新数据 update (支持原生 SQL).
 * @method static mixed updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                                                         更新某个字段的值
 * @method static mixed updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                字段递增.
 * @method static mixed updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                                                字段减少.
 * @method static mixed delete(?string $data = null, array $bind = [], bool $flag = false)                                                                                 删除数据 delete (支持原生 SQL).
 * @method static mixed truncate(bool $flag = false)                                                                                                                       清空表重置自增 ID.
 * @method static mixed findOne(bool $flag = false)                                                                                                                        返回一条记录.
 * @method static mixed findAll(bool $flag = false)                                                                                                                        返回所有记录.
 * @method static mixed find(?int $num = null, bool $flag = false)                                                                                                         返回最后几条记录.
 * @method static mixed value(string $field, bool $flag = false)                                                                                                           返回一个字段的值
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                                                                    返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                                                  数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                                                    数据分块处理依次回调.
 * @method static mixed findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                                                    总记录数.
 * @method static mixed findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                                            平均数.
 * @method static mixed findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                                            最大值.
 * @method static mixed findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                                            最小值.
 * @method static mixed findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                                            合计.
 * @method static \Leevel\Database\Page page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = [])                            分页查询.
 * @method static \Leevel\Database\Page pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                             创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                                          创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                                                        取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                                             获得查询字符串.
 * @method static \Leevel\Database\Select cache(string $name, ?int $expire = null, ?string $connect = null)                                                                设置查询缓存.
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 10)                                                                                            根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                                                                      时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                                                        时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                                                                    重置查询条件.
 * @method static \Leevel\Database\Select comment(string $comment)                                                                                                         查询注释.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                                                           prefix 查询.
 * @method static \Leevel\Database\Select table($table, $cols = '*')                                                                                                       添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                                                        获取表别名.
 * @method static \Leevel\Database\Select columns($cols = '*', ?string $table = null)                                                                                      添加字段.
 * @method static \Leevel\Database\Select setColumns($cols = '*', ?string $table = null)                                                                                   设置字段.
 * @method static string raw(string $raw)                                                                                                                                  原生查询.
 * @method static \Leevel\Database\Select where(...$cond)                                                                                                                  where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                                                                orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                                                            Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                                                          Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                                                             exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                                                          not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                                                           whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                                                        whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                                                              whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                                                           whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                                                                whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                                                             whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                                                              whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                                                           whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                                                              whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                                                               whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                                                             whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                                                              whereYear 查询条件.
 * @method static \Leevel\Database\Select bind($names, $value = null, ?int $dataType = null)                                                                               参数绑定支持.
 * @method static \Leevel\Database\Select forceIndex($indexs, $type = 'FORCE')                                                                                             index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex($indexs)                                                                                                             index 忽略索引.
 * @method static \Leevel\Database\Select join($table, $cols, ...$cond)                                                                                                    join 查询.
 * @method static \Leevel\Database\Select innerJoin($table, $cols, ...$cond)                                                                                               innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin($table, $cols, ...$cond)                                                                                                leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin($table, $cols, ...$cond)                                                                                               rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin($table, $cols, ...$cond)                                                                                                fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin($table, $cols, ...$cond)                                                                                               crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin($table, $cols, ...$cond)                                                                                             naturalJoin 查询.
 * @method static \Leevel\Database\Select union($selects, string $type = 'UNION')                                                                                          添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll($selects)                                                                                                               添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy($expression)                                                                                                             指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                                                                 添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                                                               orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                                                           having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                                                         having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                                                          havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                                                       havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                                                             havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                                                          havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                                                               havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                                                            havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                                                             havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                                                          havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                                                             havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                                                              havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                                                            havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                                                             havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy($expression, string $orderDefault = 'ASC')                                                                               添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                                                              最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                                                              最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                                                                      创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                                                          总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                                                                  平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                                                                  最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                                                                  最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                                                                  合计
 * @method static \Leevel\Database\Select one()                                                                                                                            指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                                                            指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                                                             查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                                                           limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                                                                     排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select lockShare(bool $flag = true)                                                                                                     共享锁 LOCK SHARE 查询.
 * @method static array getBindParams()                                                                                                                                    返回参数绑定.                                                                                                         返回参数绑定.
 * @method static void resetBindParams(array $bindParams = [])                                                                                                             重置参数绑定.
 * @method static void setBindParamsPrefix(string $bindParamsPrefix)                                                                                                       设置参数绑定前缀.
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
     */
    public function setTransactionConnection(IConnection $connection): void
    {
        $this->container->instance(self::TRANSACTION_SERVICE, $connection, IContainer::DEFAULT_COROUTINE_ID);
    }

    /**
     * 是否处于当前协程事务中.
     */
    public function inTransactionConnection(): bool
    {
        return $this->container->exists(self::TRANSACTION_SERVICE);
    }

    /**
     * 获取当前协程事务中的连接.
     *
     * @throws \RuntimeException
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
     */
    protected function getOptionNamespace(): string
    {
        return 'database';
    }

    /**
     * 创建 MySQL 连接.
     *
     * @return \Leevel\Database\Mysql
     */
    protected function makeConnectMysql(string $connect): Mysql
    {
        $mysql = new Mysql(
            $this->normalizeDatabaseOption($this->normalizeConnectOption($connect)),
            $this->container->make(IDispatch::class),
            $this->container->getCoroutine() ? $this : null,
        );
        $mysql->setCache($this->container->make('caches'));

        return $mysql;
    }

    /**
     * 创建 mysqlPool 连接.
     *
     * @throws \RuntimeException
     *
     * @return \Leevel\Database\MysqlPool
     */
    protected function makeConnectMysqlPool(): MysqlPool
    {
        if (!$this->container->getCoroutine()) {
            $e = 'MySQL pool can only be used in swoole scenarios.';

            throw new RuntimeException($e);
        }

        return new MysqlPool($this->container->make('mysql.pool'));
    }

    /**
     * 分析数据库配置参数.
     *
     * @throws \InvalidArgumentException
     */
    protected function normalizeDatabaseOption(array $option): array
    {
        $source = $option;
        $type = ['distributed', 'separate', 'driver', 'master', 'slave'];

        foreach (array_keys($option) as $t) {
            if (in_array($t, $type, true)) {
                if (isset($source[$t])) {
                    unset($source[$t]);
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

        $option['master'] = array_merge($option['master'], $source);

        if (!$option['distributed']) {
            $option['slave'] = [];
        } elseif ($option['slave']) {
            if (count($option['slave']) === count($option['slave'], COUNT_RECURSIVE)) {
                $option['slave'] = [$option['slave']];
            }

            foreach ($option['slave'] as &$slave) {
                $slave = array_merge($slave, $source);
            }
        }

        return $option;
    }
}
