<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Event\IDispatch;
use Leevel\Support\Manager as Managers;
use Swoole\Coroutine;

/**
 * 数据库管理器.
 *
 * @method static void                                                                   setCache(?\Leevel\Cache\Manager $cache)                                                                                                                            设置缓存.
 * @method static ?\Leevel\Cache\Manager                                                 getCache()                                                                                                                                                         获取缓存.
 * @method static \Leevel\Database\Select                                                databaseSelect()                                                                                                                                                   返回查询对象.
 * @method static ?\PDO                                                                  pdo(bool|int $master = false)                                                                                                                                      返回 PDO 查询连接.
 * @method static mixed                                                                  query(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null)     查询数据记录.
 * @method static array                                                                  procedure(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?\Leevel\Cache\ICache $cache = null) 查询存储过程数据记录.
 * @method static int|string                                                             execute(string $sql, array $bindParams = [])                                                                                                                       执行 SQL 语句.
 * @method static \Generator                                                             cursor(string $sql, array $bindParams = [], bool|int $master = false)                                                                                              游标查询.
 * @method static \PDOStatement                                                          prepare(string $sql, array $bindParams = [], bool|int $master = false)                                                                                             SQL 预处理.
 * @method static mixed                                                                  transaction(\Closure $action)                                                                                                                                      执行数据库事务.
 * @method static void                                                                   beginTransaction()                                                                                                                                                 启动事务.
 * @method static bool                                                                   inTransaction()                                                                                                                                                    检查是否处于事务中.
 * @method static void                                                                   commit()                                                                                                                                                           用于非自动提交状态下面的查询提交.
 * @method static void                                                                   rollBack()                                                                                                                                                         事务回滚.
 * @method static string                                                                 lastInsertId(?string $name = null)                                                                                                                                 获取最后插入 ID 或者列.
 * @method static string                                                                 getLastSql()                                                                                                                                                       获取最近一次查询的 SQL 语句.
 * @method static void                                                                   setRealLastSql(array $realLastSql)                                                                                                                                 设置最近一次真实查询的 SQL 语句.
 * @method static array                                                                  getRealLastSql()                                                                                                                                                   获取最近一次真实查询的 SQL 语句.
 * @method static int                                                                    numRows()                                                                                                                                                          返回影响记录.
 * @method static void                                                                   close()                                                                                                                                                            关闭数据库.
 * @method static void                                                                   freePDOStatement()                                                                                                                                                 释放 PDO 预处理查询.
 * @method static void                                                                   closeConnects()                                                                                                                                                    关闭数据库连接.
 * @method static string                                                                 getRawSql(string $sql, array $bindParams)                                                                                                                          从 PDO 预处理语句中获取原始 SQL 查询字符串.
 * @method static string                                                                 parseDsn(array $config)                                                                                                                                            DSN 解析.
 * @method static array                                                                  getTableNames(string $dbName, bool|int $master = false)                                                                                                            取得数据库表名列表.
 * @method static array                                                                  getTableColumns(string $tableName, bool|int $master = false)                                                                                                       取得数据库表字段信息.
 * @method static string                                                                 identifierColumn(string $name)                                                                                                                                     SQL 字段格式化.
 * @method static string                                                                 limitCount(?int $limitCount = null, ?int $limitOffset = null)                                                                                                      分析查询条数.
 * @method static \Leevel\Database\Condition                                             databaseCondition()                                                                                                                                                查询对象.
 * @method static \Leevel\Database\IDatabase                                             databaseConnect()                                                                                                                                                  返回数据库连接对象.
 * @method static \Leevel\Database\Select                                                master(bool|int $master = false)                                                                                                                                   设置是否查询主服务器.
 * @method static \Leevel\Database\Select                                                asSome(?\Closure $asSome = null, array $args = [])                                                                                                                 设置以某种包装返会结果.
 * @method static \Leevel\Database\Select                                                asArray(?\Closure $asArray = null)                                                                                                                                 设置返会结果为数组.
 * @method static \Leevel\Database\Select                                                asCollection(bool $asCollection = true, array $valueTypes = [])                                                                                                    设置是否以集合返回.
 * @method static mixed                                                                  select(null|callable|\Leevel\Database\Select|string $data = null, array $bind = [])                                                                                原生 SQL 查询数据.
 * @method static int|string                                                             insert(array|string $data, array $bind = [], array|bool $replace = false)                                                                                          插入数据 insert (支持原生 SQL).
 * @method static int|string                                                             insertAll(array $data, array $bind = [], array|bool $replace = false)                                                                                              批量插入数据 insertAll.
 * @method static int                                                                    update(array|string $data, array $bind = [])                                                                                                                       更新数据 update (支持原生 SQL).
 * @method static int                                                                    updateColumn(string $column, mixed $value, array $bind = [])                                                                                                       更新某个字段的值
 * @method static int                                                                    updateIncrease(string $column, int $step = 1, array $bind = [])                                                                                                    字段递增.
 * @method static int                                                                    updateDecrease(string $column, int $step = 1, array $bind = [])                                                                                                    字段减少.
 * @method static int                                                                    delete(?string $data = null, array $bind = [])                                                                                                                     删除数据 delete (支持原生 SQL).
 * @method static array|int                                                              truncate()                                                                                                                                                         清空表重置自增 ID.
 * @method static mixed                                                                  findOne()                                                                                                                                                          返回一条记录.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection|array findAll()                                                                                                                                                          返回所有记录.
 * @method static array                                                                  findArray()                                                                                                                                                        以数组返回所有记录.
 * @method static array                                                                  findAsArray()                                                                                                                                                      以数组返回所有记录（每一项也为数组）.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection       findCollection()                                                                                                                                                   以集合返回所有记录.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection|array find(?int $num = null)                                                                                                                                             返回最后几条记录.
 * @method static mixed                                                                  value(string $field)                                                                                                                                               返回一个字段的值
 * @method static array                                                                  list(mixed $fieldValue, ?string $fieldKey = null)                                                                                                                  返回一列数据.
 * @method static void                                                                   chunk(int $count, \Closure $chunk)                                                                                                                                 数据分块处理.
 * @method static void                                                                   each(int $count, \Closure $each)                                                                                                                                   数据分块处理依次回调.
 * @method static int                                                                    findCount(string $field = '*', string $alias = 'row_count')                                                                                                        总记录数.
 * @method static mixed                                                                  findAvg(string $field, string $alias = 'avg_value')                                                                                                                平均数.
 * @method static mixed                                                                  findMax(string $field, string $alias = 'max_value')                                                                                                                最大值.
 * @method static mixed                                                                  findMin(string $field, string $alias = 'min_value')                                                                                                                最小值.
 * @method static mixed                                                                  findSum(string $field, string $alias = 'sum_value')                                                                                                                合计.
 * @method static \Leevel\Database\Page                                                  page(int $currentPage, int $perPage = 10, string $column = '*', array $config = [])                                                                                分页查询.
 * @method static \Leevel\Database\Page                                                  pageMacro(int $currentPage, int $perPage = 10, array $config = [])                                                                                                 创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page                                                  pagePrevNext(int $currentPage, int $perPage = 10, array $config = [])                                                                                              创建一个只有上下页的分页查询.
 * @method static int                                                                    pageCount(string $cols = '*')                                                                                                                                      取得分页查询记录数量.
 * @method static string                                                                 makeSql(bool $withLogicGroup = false)                                                                                                                              获得查询字符串.
 * @method static \Leevel\Database\Select                                                cache(string $name, ?int $expire = null, ?\Leevel\Cache\ICache $cache = null)                                                                                      设置查询缓存.
 * @method static \Leevel\Database\Select                                                forPage(int $page, int $perPage = 10)                                                                                                                              根据分页设置条件.
 * @method static \Leevel\Database\Select                                                time(string $type = 'date')                                                                                                                                        时间控制语句开始.
 * @method static \Leevel\Database\Select                                                endTime()                                                                                                                                                          时间控制语句结束.
 * @method static \Leevel\Database\Select                                                reset(?string $config = null)                                                                                                                                      重置查询条件.
 * @method static \Leevel\Database\Select                                                comment(string $comment)                                                                                                                                           查询注释.
 * @method static \Leevel\Database\Select                                                prefix(string $prefix)                                                                                                                                             prefix 查询.
 * @method static \Leevel\Database\Select                                                table(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols = '*')                                                   添加一个要查询的表及其要查询的字段.
 * @method static string                                                                 getAlias()                                                                                                                                                         获取表别名.
 * @method static \Leevel\Database\Select                                                columns(array|string $cols = '*', ?string $table = null)                                                                                                           添加字段.
 * @method static \Leevel\Database\Select                                                setColumns(array|string $cols = '*', ?string $table = null)                                                                                                        设置字段.
 * @method static \Leevel\Database\Select                                                field(array|string $cols = '*', ?string $table = null)                                                                                                             设置字段别名方法.
 * @method static string                                                                 raw(string $raw)                                                                                                                                                   原生查询.
 * @method static \Leevel\Database\Select                                                middlewares(string ...$middlewares)                                                                                                                                查询中间件.
 * @method static array                                                                  registerMiddlewares(array $middlewares, bool $force = false)                                                                                                       注册查询中间件.
 * @method static \Leevel\Database\Select                                                where(...$cond)                                                                                                                                                    where 查询条件.
 * @method static \Leevel\Database\Select                                                orWhere(...$cond)                                                                                                                                                  orWhere 查询条件.
 * @method static \Leevel\Database\Select                                                whereRaw(string $raw)                                                                                                                                              Where 原生查询.
 * @method static \Leevel\Database\Select                                                orWhereRaw(string $raw)                                                                                                                                            Where 原生 OR 查询.
 * @method static \Leevel\Database\Select                                                whereExists($exists)                                                                                                                                               exists 方法支持
 * @method static \Leevel\Database\Select                                                whereNotExists($exists)                                                                                                                                            not exists 方法支持
 * @method static \Leevel\Database\Select                                                whereBetween(...$cond)                                                                                                                                             whereBetween 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotBetween(...$cond)                                                                                                                                          whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select                                                whereNull(...$cond)                                                                                                                                                whereNull 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotNull(...$cond)                                                                                                                                             whereNotNull 查询条件.
 * @method static \Leevel\Database\Select                                                whereIn(...$cond)                                                                                                                                                  whereIn 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotIn(...$cond)                                                                                                                                               whereNotIn 查询条件.
 * @method static \Leevel\Database\Select                                                whereLike(...$cond)                                                                                                                                                whereLike 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotLike(...$cond)                                                                                                                                             whereNotLike 查询条件.
 * @method static \Leevel\Database\Select                                                whereDate(...$cond)                                                                                                                                                whereDate 查询条件.
 * @method static \Leevel\Database\Select                                                whereDay(...$cond)                                                                                                                                                 whereDay 查询条件.
 * @method static \Leevel\Database\Select                                                whereMonth(...$cond)                                                                                                                                               whereMonth 查询条件.
 * @method static \Leevel\Database\Select                                                whereYear(...$cond)                                                                                                                                                whereYear 查询条件.
 * @method static \Leevel\Database\Select                                                bind(mixed $names, mixed $value = null, ?int $dataType = null)                                                                                                     参数绑定支持.
 * @method static \Leevel\Database\Select                                                forceIndex(array|string $indexs, string $type = 'FORCE')                                                                                                           index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select                                                ignoreIndex(array|string $indexs)                                                                                                                                  index 忽略索引.
 * @method static \Leevel\Database\Select                                                join(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                                join 查询.
 * @method static \Leevel\Database\Select                                                innerJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                           innerJoin 查询.
 * @method static \Leevel\Database\Select                                                leftJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                            leftJoin 查询.
 * @method static \Leevel\Database\Select                                                rightJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                           rightJoin 查询.
 * @method static \Leevel\Database\Select                                                fullJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                            fullJoin 查询.
 * @method static \Leevel\Database\Select                                                crossJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                           crossJoin 查询.
 * @method static \Leevel\Database\Select                                                naturalJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)                                         naturalJoin 查询.
 * @method static \Leevel\Database\Select                                                union(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects, string $type = 'UNION')                                                   添加一个 UNION 查询.
 * @method static \Leevel\Database\Select                                                unionAll(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects)                                                                        添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select                                                groupBy(array|string $expression)                                                                                                                                  指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select                                                having(...$cond)                                                                                                                                                   添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select                                                orHaving(...$cond)                                                                                                                                                 orHaving 查询条件.
 * @method static \Leevel\Database\Select                                                havingRaw(string $raw)                                                                                                                                             having 原生查询.
 * @method static \Leevel\Database\Select                                                orHavingRaw(string $raw)                                                                                                                                           having 原生 OR 查询.
 * @method static \Leevel\Database\Select                                                havingBetween(...$cond)                                                                                                                                            havingBetween 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotBetween(...$cond)                                                                                                                                         havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select                                                havingNull(...$cond)                                                                                                                                               havingNull 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotNull(...$cond)                                                                                                                                            havingNotNull 查询条件.
 * @method static \Leevel\Database\Select                                                havingIn(...$cond)                                                                                                                                                 havingIn 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotIn(...$cond)                                                                                                                                              havingNotIn 查询条件.
 * @method static \Leevel\Database\Select                                                havingLike(...$cond)                                                                                                                                               havingLike 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotLike(...$cond)                                                                                                                                            havingNotLike 查询条件.
 * @method static \Leevel\Database\Select                                                havingDate(...$cond)                                                                                                                                               havingDate 查询条件.
 * @method static \Leevel\Database\Select                                                havingDay(...$cond)                                                                                                                                                havingDay 查询条件.
 * @method static \Leevel\Database\Select                                                havingMonth(...$cond)                                                                                                                                              havingMonth 查询条件.
 * @method static \Leevel\Database\Select                                                havingYear(...$cond)                                                                                                                                               havingYear 查询条件.
 * @method static \Leevel\Database\Select                                                orderBy(array|string $expression, string $orderDefault = 'ASC')                                                                                                    添加排序.
 * @method static \Leevel\Database\Select                                                latest(string $field = 'create_at')                                                                                                                                最近排序数据.
 * @method static \Leevel\Database\Select                                                oldest(string $field = 'create_at')                                                                                                                                最早排序数据.
 * @method static \Leevel\Database\Select                                                distinct(bool $flag = true)                                                                                                                                        创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select                                                count(string $field = '*', string $alias = 'row_count')                                                                                                            总记录数.
 * @method static \Leevel\Database\Select                                                avg(string $field, string $alias = 'avg_value')                                                                                                                    平均数.
 * @method static \Leevel\Database\Select                                                max(string $field, string $alias = 'max_value')                                                                                                                    最大值.
 * @method static \Leevel\Database\Select                                                min(string $field, string $alias = 'min_value')                                                                                                                    最小值.
 * @method static \Leevel\Database\Select                                                sum(string $field, string $alias = 'sum_value')                                                                                                                    合计
 * @method static \Leevel\Database\Select                                                one()                                                                                                                                                              指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select                                                all()                                                                                                                                                              指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select                                                top(int $count = 30)                                                                                                                                               查询几条记录.
 * @method static \Leevel\Database\Select                                                limit(int $offset = 0, int $count = 0)                                                                                                                             limit 限制条数.
 * @method static \Leevel\Database\Select                                                forUpdate(bool $flag = true)                                                                                                                                       排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select                                                lockShare(bool $flag = true)                                                                                                                                       共享锁 LOCK SHARE 查询.
 * @method static array                                                                  getBindParams()                                                                                                                                                    返回参数绑定.                                                                                                         返回参数绑定.
 * @method static void                                                                   resetBindParams(array $bindParams = [])                                                                                                                            重置参数绑定.
 * @method static void                                                                   setBindParamsPrefix(string $bindParamsPrefix)                                                                                                                      设置参数绑定前缀.
 * @method static \Leevel\Database\Select                                                if(mixed $value = false)                                                                                                                                           条件语句 if.
 * @method static \Leevel\Database\Select                                                elif(mixed $value = false)                                                                                                                                         条件语句 elif.
 * @method static \Leevel\Database\Select                                                else()                                                                                                                                                             条件语句 else.
 * @method static \Leevel\Database\Select                                                fi()                                                                                                                                                               条件语句 fi.
 * @method static \Leevel\Database\Select                                                setFlowControl(bool $inFlowControl, bool $isFlowControlTrue)                                                                                                       设置当前条件表达式状态.
 * @method static bool                                                                   checkFlowControl()                                                                                                                                                 验证一下条件表达式是否通过.
 * @method static \Leevel\Di\IContainer                                                  container()                                                                                                                                                        返回 IOC 容器.
 * @method static void                                                                   disconnect(?string $connect = null)                                                                                                                                删除连接.
 * @method static array                                                                  getConnects()                                                                                                                                                      取回所有连接.
 * @method static string                                                                 getDefaultConnect()                                                                                                                                                返回默认连接.
 * @method static void                                                                   setDefaultConnect(string $name)                                                                                                                                    设置默认连接.
 * @method static mixed                                                                  getContainerConfig(?string $name = null)                                                                                                                           获取容器配置值.
 * @method static void                                                                   setContainerConfig(string $name, mixed $value)                                                                                                                     设置容器配置值.
 * @method static void                                                                   extend(string $connect, \Closure $callback)                                                                                                                        扩展自定义连接.
 * @method static array                                                                  normalizeConnectConfig(string $connect)                                                                                                                            整理连接配置.
 */
class Manager extends Managers
{
    /**
     * 数据库连接池.
     */
    protected array $pools = [];

    /**
     * 数据库连接池事务管理器.
     */
    protected array $poolTransactions = [];

    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): IDatabase
    {
        // 协程环境每次从创建驱动中获取连接
        if ($this->container->enabledCoroutine()) {
            $newConnect = true;
        }

        return parent::connect($connect, $newConnect, ...$arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null, ...$arguments): IDatabase
    {
        return parent::reconnect($connect, ...$arguments);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'database';
    }

    /**
     * 创建 MySQL 连接.
     */
    protected function makeConnectMysql(string $connect, ?string $driverClass = null, bool $newConnect = false): Mysql
    {
        $configs = $this->normalizeDatabaseConfig($this->normalizeConnectConfig($connect));
        $enabledCoroutine = $this->container->enabledCoroutine();
        $poolTransaction = null;
        if ($enabledCoroutine) {
            $poolTransaction = $this->getPoolTransaction($connect);
        }

        if (!$newConnect && $enabledCoroutine) {
            // @phpstan-ignore-next-line
            return $this->getConnectionFromFool($configs, $connect, $poolTransaction);
        }

        return $this->createMysql($configs, $driverClass, $poolTransaction);
    }

    protected function createMysql(array $configs, ?string $driverClass = null, ?PoolTransaction $poolTransaction = null): Mysql
    {
        $driverClass = $this->getDriverClass(Mysql::class, $driverClass);

        /** @var Mysql $mysql */
        $mysql = new $driverClass(
            $configs,
            $this->container->make(IDispatch::class),
            $poolTransaction,
        );
        $mysql->setCache($this->container->make('cache'));

        return $mysql;
    }

    /**
     * 创建连接池.
     */
    protected function getPool(string $connect, array $configs): Pool
    {
        if (isset($this->pools[$connect])) {
            return $this->pools[$connect];
        }

        return $this->pools[$connect] = new Pool($this, $connect, $configs);
    }

    protected function getPoolTransaction(string $connect): PoolTransaction
    {
        if (isset($this->poolTransactions[$connect])) {
            return $this->poolTransactions[$connect];
        }

        return $this->poolTransactions[$connect] = new PoolTransaction($this->container, $connect);
    }

    protected function getConnectionFromFool(array $configs, string $connect, PoolTransaction $poolTransaction): IDatabase
    {
        if ($poolTransaction->in()) {
            return $poolTransaction->get();
        }

        $pool = $this->getPool($connect, $configs['master'] ?? []);
        $connection = $pool->get();

        // 协程关闭前归还当前连接到数据库连接池
        Coroutine::defer(fn () => $connection->releaseConnect());

        return $connection;
    }

    /**
     * 分析数据库配置参数.
     *
     * @throws \InvalidArgumentException
     */
    protected function normalizeDatabaseConfig(array $config): array
    {
        $source = $config;
        $type = ['distributed', 'separate', 'driver', 'master', 'slave'];

        foreach (array_keys($config) as $t) {
            if (\in_array($t, $type, true)) {
                if (isset($source[$t])) {
                    unset($source[$t]);
                }
            } elseif (isset($config[$t])) {
                unset($config[$t]);
            }
        }

        foreach (['master', 'slave'] as $t) {
            if (!\is_array($config[$t])) {
                throw new \InvalidArgumentException(sprintf('Database config `%s` must be an array.', $t));
            }
        }

        $config['master'] = array_merge($config['master'], $source);

        if (!$config['distributed']) {
            $config['slave'] = [];
        } elseif ($config['slave']) {
            if (\count($config['slave']) === \count($config['slave'], COUNT_RECURSIVE)) {
                $config['slave'] = [$config['slave']];
            }

            foreach ($config['slave'] as &$slave) {
                $slave = array_merge($slave, $source);
            }
        }

        return $config;
    }
}
