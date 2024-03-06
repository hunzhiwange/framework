<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Server\Pool\Connection;
use Leevel\Support\Arr\Normalize;

/**
 * 数据库抽象层.
 *
 * @method static \Leevel\Database\Condition                                             databaseCondition()                                                                                                        查询对象.
 * @method static \Leevel\Database\IDatabase                                             databaseConnect()                                                                                                          返回数据库连接对象.
 * @method static \Leevel\Database\Select                                                master(bool $master = false)                                                                                               设置是否查询主服务器.
 * @method static \Leevel\Database\Select                                                asSome(?\Closure $asSome = null, array $args = [])                                                                         设置以某种包装返会结果.
 * @method static \Leevel\Database\Select                                                asArray(?\Closure $asArray = null)                                                                                         设置返会结果为数组.
 * @method static \Leevel\Database\Select                                                asCollection(bool $asCollection = true, array $valueTypes = [])                                                            设置是否以集合返回.
 * @method static mixed                                                                  select(null|callable|\Leevel\Database\Select|string $data = null, array $bind = [])                                        原生 SQL 查询数据.
 * @method static int|string                                                             insert(array|string $data, array $bind = [], array|bool $replace = false)                                                  插入数据 insert (支持原生 SQL).
 * @method static int|string                                                             insertAll(array $data, array $bind = [], array|bool $replace = false)                                                      批量插入数据 insertAll.
 * @method static int                                                                    update(array|string $data, array $bind = [])                                                                               更新数据 update (支持原生 SQL).
 * @method static int                                                                    updateColumn(string $column, mixed $value, array $bind = [])                                                               更新某个字段的值
 * @method static int                                                                    updateIncrease(string $column, int $step = 1, array $bind = [])                                                            字段递增.
 * @method static int                                                                    updateDecrease(string $column, int $step = 1, array $bind = [])                                                            字段减少.
 * @method static int                                                                    delete(?string $data = null, array $bind = [])                                                                             删除数据 delete (支持原生 SQL).
 * @method static array|int                                                              truncate()                                                                                                                 清空表重置自增 ID.
 * @method static mixed                                                                  findOne()                                                                                                                  返回一条记录.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection|array findAll()                                                                                                                  返回所有记录.
 * @method static array                                                                  findArray()                                                                                                                以数组返回所有记录.
 * @method static array                                                                  findAsArray()                                                                                                              以数组返回所有记录（每一项也为数组）.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection       findCollection()                                                                                                           以集合返回所有记录.
 * @method static \Leevel\Database\Ddd\EntityCollection|\Leevel\Support\Collection|array find(?int $num = null)                                                                                                     返回最后几条记录.
 * @method static mixed                                                                  value(string $field)                                                                                                       返回一个字段的值
 * @method static array                                                                  list(mixed $fieldValue, ?string $fieldKey = null)                                                                          返回一列数据.
 * @method static void                                                                   chunk(int $count, \Closure $chunk)                                                                                         数据分块处理.
 * @method static void                                                                   each(int $count, \Closure $each)                                                                                           数据分块处理依次回调.
 * @method static int                                                                    findCount(string $field = '*', string $alias = 'row_count')                                                                总记录数.
 * @method static mixed                                                                  findAvg(string $field, string $alias = 'avg_value')                                                                        平均数.
 * @method static mixed                                                                  findMax(string $field, string $alias = 'max_value')                                                                        最大值.
 * @method static mixed                                                                  findMin(string $field, string $alias = 'min_value')                                                                        最小值.
 * @method static mixed                                                                  findSum(string $field, string $alias = 'sum_value')                                                                        合计.
 * @method static \Leevel\Database\Page                                                  page(int $currentPage, int $perPage = 10, ?int $count = null, array $config = [])                                          分页查询.
 * @method static \Leevel\Database\Page                                                  pageMacro(int $currentPage, int $perPage = 10, array $config = [])                                                         创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page                                                  pagePrevNext(int $currentPage, int $perPage = 10, array $config = [])                                                      创建一个只有上下页的分页查询.
 * @method static int                                                                    pageCount(string $cols = '*')                                                                                              取得分页查询记录数量.
 * @method static string                                                                 makeSql(bool $withLogicGroup = false)                                                                                      获得查询字符串.
 * @method static \Leevel\Database\Select                                                cache(string $name, ?int $expire = null, ?\Leevel\Cache\ICache $cache = null)                                              设置查询缓存.
 * @method static \Leevel\Database\Select                                                forPage(int $page, int $perPage = 10)                                                                                      根据分页设置条件.
 * @method static \Leevel\Database\Select                                                time(string $type = 'date')                                                                                                时间控制语句开始.
 * @method static \Leevel\Database\Select                                                endTime()                                                                                                                  时间控制语句结束.
 * @method static \Leevel\Database\Select                                                reset(?string $config = null)                                                                                              重置查询条件.
 * @method static \Leevel\Database\Select                                                comment(string $comment)                                                                                                   查询注释.
 * @method static \Leevel\Database\Select                                                prefix(string $prefix)                                                                                                     prefix 查询.
 * @method static \Leevel\Database\Select                                                table(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols = '*')           添加一个要查询的表及其要查询的字段.
 * @method static string                                                                 getAlias()                                                                                                                 获取表别名.
 * @method static \Leevel\Database\Select                                                columns(array|string $cols = '*', ?string $table = null)                                                                   添加字段.
 * @method static \Leevel\Database\Select                                                setColumns(array|string $cols = '*', ?string $table = null)                                                                设置字段.
 * @method static \Leevel\Database\Select                                                field(array|string $cols = '*', ?string $table = null)                                                                     设置字段别名方法.
 * @method static string                                                                 raw(string $raw)                                                                                                           原生查询.
 * @method static \Leevel\Database\Condition                                             middlewares(string ...$middlewares)                                                                                        查询中间件.
 * @method static array                                                                  registerMiddlewares(array $middlewares, bool $force = false)                                                               注册查询中间件.
 * @method static \Leevel\Database\Select                                                where(...$cond)                                                                                                            where 查询条件.
 * @method static \Leevel\Database\Select                                                orWhere(...$cond)                                                                                                          orWhere 查询条件.
 * @method static \Leevel\Database\Select                                                whereRaw(string $raw)                                                                                                      Where 原生查询.
 * @method static \Leevel\Database\Select                                                orWhereRaw(string $raw)                                                                                                    Where 原生 OR 查询.
 * @method static \Leevel\Database\Select                                                whereExists($exists)                                                                                                       exists 方法支持
 * @method static \Leevel\Database\Select                                                whereNotExists($exists)                                                                                                    not exists 方法支持
 * @method static \Leevel\Database\Select                                                whereBetween(...$cond)                                                                                                     whereBetween 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotBetween(...$cond)                                                                                                  whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select                                                whereNull(...$cond)                                                                                                        whereNull 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotNull(...$cond)                                                                                                     whereNotNull 查询条件.
 * @method static \Leevel\Database\Select                                                whereIn(...$cond)                                                                                                          whereIn 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotIn(...$cond)                                                                                                       whereNotIn 查询条件.
 * @method static \Leevel\Database\Select                                                whereLike(...$cond)                                                                                                        whereLike 查询条件.
 * @method static \Leevel\Database\Select                                                whereNotLike(...$cond)                                                                                                     whereNotLike 查询条件.
 * @method static \Leevel\Database\Select                                                whereDate(...$cond)                                                                                                        whereDate 查询条件.
 * @method static \Leevel\Database\Select                                                whereDay(...$cond)                                                                                                         whereDay 查询条件.
 * @method static \Leevel\Database\Select                                                whereMonth(...$cond)                                                                                                       whereMonth 查询条件.
 * @method static \Leevel\Database\Select                                                whereYear(...$cond)                                                                                                        whereYear 查询条件.
 * @method static \Leevel\Database\Select                                                bind(mixed $names, mixed $value = null, ?int $dataType = null)                                                             参数绑定支持.
 * @method static \Leevel\Database\Select                                                forceIndex(array|string $indexs, string $type = 'FORCE')                                                                   index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select                                                ignoreIndex(array|string $indexs)                                                                                          index 忽略索引.
 * @method static \Leevel\Database\Select                                                join(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)        join 查询.
 * @method static \Leevel\Database\Select                                                innerJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)   innerJoin 查询.
 * @method static \Leevel\Database\Select                                                leftJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)    leftJoin 查询.
 * @method static \Leevel\Database\Select                                                rightJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)   rightJoin 查询.
 * @method static \Leevel\Database\Select                                                fullJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)    fullJoin 查询.
 * @method static \Leevel\Database\Select                                                crossJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)   crossJoin 查询.
 * @method static \Leevel\Database\Select                                                naturalJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond) naturalJoin 查询.
 * @method static \Leevel\Database\Select                                                union(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects, string $type = 'UNION')           添加一个 UNION 查询.
 * @method static \Leevel\Database\Select                                                unionAll(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects)                                添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select                                                groupBy(array|string $expression)                                                                                          指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select                                                having(...$cond)                                                                                                           添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select                                                orHaving(...$cond)                                                                                                         orHaving 查询条件.
 * @method static \Leevel\Database\Select                                                havingRaw(string $raw)                                                                                                     having 原生查询.
 * @method static \Leevel\Database\Select                                                orHavingRaw(string $raw)                                                                                                   having 原生 OR 查询.
 * @method static \Leevel\Database\Select                                                havingBetween(...$cond)                                                                                                    havingBetween 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotBetween(...$cond)                                                                                                 havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select                                                havingNull(...$cond)                                                                                                       havingNull 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotNull(...$cond)                                                                                                    havingNotNull 查询条件.
 * @method static \Leevel\Database\Select                                                havingIn(...$cond)                                                                                                         havingIn 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotIn(...$cond)                                                                                                      havingNotIn 查询条件.
 * @method static \Leevel\Database\Select                                                havingLike(...$cond)                                                                                                       havingLike 查询条件.
 * @method static \Leevel\Database\Select                                                havingNotLike(...$cond)                                                                                                    havingNotLike 查询条件.
 * @method static \Leevel\Database\Select                                                havingDate(...$cond)                                                                                                       havingDate 查询条件.
 * @method static \Leevel\Database\Select                                                havingDay(...$cond)                                                                                                        havingDay 查询条件.
 * @method static \Leevel\Database\Select                                                havingMonth(...$cond)                                                                                                      havingMonth 查询条件.
 * @method static \Leevel\Database\Select                                                havingYear(...$cond)                                                                                                       havingYear 查询条件.
 * @method static \Leevel\Database\Select                                                orderBy(array|string $expression, string $orderDefault = 'ASC')                                                            添加排序.
 * @method static \Leevel\Database\Select                                                latest(string $field = 'create_at')                                                                                        最近排序数据.
 * @method static \Leevel\Database\Select                                                oldest(string $field = 'create_at')                                                                                        最早排序数据.
 * @method static \Leevel\Database\Select                                                distinct(bool $flag = true)                                                                                                创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select                                                count(string $field = '*', string $alias = 'row_count')                                                                    总记录数.
 * @method static \Leevel\Database\Select                                                avg(string $field, string $alias = 'avg_value')                                                                            平均数.
 * @method static \Leevel\Database\Select                                                max(string $field, string $alias = 'max_value')                                                                            最大值.
 * @method static \Leevel\Database\Select                                                min(string $field, string $alias = 'min_value')                                                                            最小值.
 * @method static \Leevel\Database\Select                                                sum(string $field, string $alias = 'sum_value')                                                                            合计
 * @method static \Leevel\Database\Select                                                one()                                                                                                                      指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select                                                all()                                                                                                                      指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select                                                top(int $count = 30)                                                                                                       查询几条记录.
 * @method static \Leevel\Database\Select                                                limit(int $offset = 0, int $count = 0)                                                                                     limit 限制条数.
 * @method static \Leevel\Database\Select                                                forUpdate(bool $flag = true)                                                                                               排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select                                                lockShare(bool $flag = true)                                                                                               共享锁 LOCK SHARE 查询.
 * @method static array                                                                  getBindParams()                                                                                                            返回参数绑定.                                                                                                        返回参数绑定.
 * @method static void                                                                   resetBindParams(array $bindParams = [])                                                                                    重置参数绑定.
 * @method static void                                                                   setBindParamsPrefix(string $bindParamsPrefix)                                                                              设置参数绑定前缀.
 * @method static \Leevel\Database\Select                                                if(mixed $value = false)                                                                                                   条件语句 if.
 * @method static \Leevel\Database\Select                                                elif(mixed $value = false)                                                                                                 条件语句 elif.
 * @method static \Leevel\Database\Select                                                else()                                                                                                                     条件语句 else.
 * @method static \Leevel\Database\Select                                                fi()                                                                                                                       条件语句 fi.
 */
abstract class Database implements IDatabase
{
    use Connection;

    /**
     * 当前数据库主连接.
     */
    protected ?\PDO $masterPdo = null;

    /**
     * 当前数据库从连接.
     */
    protected ?\PDO $slavePdo = null;

    /**
     * 数据库连接参数.
     *
     * - separate:数据库读写是否分离
     * - sticky:如果该配置项设置为 true 的话，在同一个事务中，写入的数据会被立刻读取到
     * - master:分布式服务部署主服务器
     * - master.host:数据库 host，默认为 localhost
     * - master.port:端口
     * - master.name:数据库名字
     * - master.user:数据库用户名
     * - master.password:数据库密码
     * - master.charset:数据库编码
     * - master.configs:连接参数
     * - slave:分布式服务部署模式中，附属服务器列表
     */
    protected array $config = [
        'separate' => false,
        'sticky' => true,
        'master' => [
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => '',
            'user' => '',
            'password' => '',
            'charset' => 'utf8',
            'configs' => [
                \PDO::ATTR_PERSISTENT => false,
                \PDO::ATTR_CASE => \PDO::CASE_NATURAL,
                \PDO::ATTR_ORACLE_NULLS => \PDO::NULL_NATURAL,
                \PDO::ATTR_STRINGIFY_FETCHES => false,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_TIMEOUT => 30,
            ],
        ],
        'slave' => [],
    ];

    /**
     * SQL 最后查询语句.
     */
    protected string $sql = '';

    /**
     * 最近一次真实查询的 SQL 语句.
     */
    protected array $realLastSql = [];

    /**
     * 事务等级.
     */
    protected int $transactionLevel = 0;

    /**
     * 是否开启部分事务.
     *
     * - 依赖数据库是否支持部分事务.
     */
    protected bool $transactionWithSavepoints = false;

    /**
     * 是否仅仅允许事务回滚.
     *
     * - 嵌套事务一旦回滚，后续操作将仅仅允许回滚事务，不再允许提交事务，否则业务将不完整
     * - 嵌套事务提交后，后续是可以允许回滚的，也可以继续提交
     */
    protected bool $isRollbackOnly = false;

    /**
     * 断线重连次数.
     */
    protected int $reconnectRetry = 0;

    /**
     * 事件处理器.
     */
    protected ?IDispatch $dispatch = null;

    /**
     * 缓存管理.
     */
    protected ?ICache $cache = null;

    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 数据库连接池事务管理器.
     */
    protected ?PoolTransaction $poolTransaction = null;

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container, array $config, ?IDispatch $dispatch = null, ?PoolTransaction $poolTransaction = null)
    {
        $this->container = $container;
        $this->config = array_merge($this->config, $config);
        $this->dispatch = $dispatch;
        $this->poolTransaction = $poolTransaction;
    }

    /**
     * 析构方法.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        return $this->databaseSelect()->{$method}(...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function getContainer(): IContainer
    {
        return $this->container;
    }

    /**
     * {@inheritDoc}
     */
    public function setCache(?ICache $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function getCache(): ?ICache
    {
        return $this->cache;
    }

    /**
     * {@inheritDoc}
     */
    public function databaseSelect(): Select
    {
        return new Select($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getPdo(bool $master = false): \PDO
    {
        return $master ? $this->getMasterPdo() : $this->getSlavePdo();
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): mixed
    {
        if ($cacheName && false !== ($result = $this->getDataFromCache($cacheName, $cache))) {
            return $result;
        }

        // 兼容阿里云 RDS MySQL 主从
        // RDS MySQL 读写分离如何确保数据读取的时效性
        // https://help.aliyun.com/zh/rds/support/how-do-i-ensure-the-timeliness-of-reading-data-on-an-apsaradb-rds-for-mysql-instance-when-the-read-or-write-splitting-feature-is-enabled
        if ($master || $this->shouldSticky()) {
            $sql = '/*FORCE_MASTER*/ '.$sql;
        }

        $statement = $this->prepare($sql, $bindParams, $master);
        $result = $this->fetchResult($statement);
        if ($cacheName) {
            $this->setDataToCache($cacheName, $result, $cacheExpire, $cache);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function procedure(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): array
    {
        if ($cacheName && false !== ($result = $this->getDataFromCache($cacheName, $cache))) {
            return (array) $result;
        }

        $statement = $this->prepare($sql, $bindParams, $master);
        $result = $this->fetchProcedureResult($statement);

        if ($cacheName) {
            $this->setDataToCache($cacheName, $result, $cacheExpire, $cache);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(string $sql, array $bindParams = [], bool $master = false, bool $insert = false): int|string
    {
        $statement = $this->prepare($sql, $bindParams, $master);

        if (!$insert) {
            return $statement->rowCount();
        }

        $lastInsertId = $this->getPdo($master)->lastInsertId() ?: 0;
        // @phpstan-ignore-next-line
        if (!\is_int($lastInsertId) && ctype_digit($lastInsertId)) {
            $lastInsertId = (int) $lastInsertId;
        }

        // 底层数据库不支持自增字段或者表没有设计自增字段，
        // insert 操作 lastInsertId 会返回 0
        return $lastInsertId;
    }

    /**
     * {@inheritDoc}
     */
    public function cursor(string $sql, array $bindParams = [], bool $master = false): \Generator
    {
        $statement = $this->prepare($sql, $bindParams, $master);
        while ($value = $statement->fetch(\PDO::FETCH_OBJ)) {
            yield $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prepare(string $sql, array $bindParams = [], bool $master = false): \PDOStatement
    {
        $bindParamsResult = $this->normalizeBindParams($bindParams);
        $rawSql = ' ('.static::getRawSql($sql, $bindParamsResult).')';

        try {
            $statement = $this->getPdo($master)->prepare($sql);
            $this->bindParams($statement, $bindParamsResult);
            $statement->execute();
            $this->setLastSql($this->normalizeLastSql($statement).$rawSql);
            $this->reconnectRetry = 0;
        } catch (\PDOException $e) {
            if ($this->needReconnect($e)) {
                ++$this->reconnectRetry;
                $this->close();

                return $this->prepare($sql, $bindParams, $master);
            }

            // @phpstan-ignore-next-line
            if (isset($statement)) {
                $sql = $this->normalizeLastSql($statement);
            } else {
                $sql = $this->normalizeErrorLastSql($sql, $bindParamsResult);
            }
            $this->setLastSql($sql.$rawSql, true);
            $this->pdoException($e);
        }

        // @phpstan-ignore-next-line
        return $statement;
    }

    /**
     * {@inheritDoc}
     */
    public function transaction(\Closure $action): mixed
    {
        $this->beginTransaction();

        try {
            $result = $action($this);
            $this->commit();

            return $result;
        } catch (\Throwable $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction(): void
    {
        ++$this->transactionLevel;

        if (1 === $this->transactionLevel) {
            try { // @codeCoverageIgnore
                $this->getPdo(true)->beginTransaction(); // @phpstan-ignore-line

                if ($this->poolTransaction) {
                    $this->poolTransaction->set($this);
                }

                // @codeCoverageIgnoreStart
            } catch (\Throwable $e) {
                --$this->transactionLevel;

                throw $e;
            }
            // @codeCoverageIgnoreEnd
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->createSavepoint($this->getSavepointName()); // @codeCoverageIgnore
        }
    }

    /**
     * {@inheritDoc}
     */
    public function inTransaction(): bool
    {
        return $this->getPdo(true)->inTransaction();
    }

    /**
     * {@inheritDoc}
     */
    public function commit(): void
    {
        if (0 === $this->transactionLevel) {
            ConnectionException::noActiveTransaction('Commit');
        }

        if ($this->isRollbackOnly) {
            ConnectionException::rollbackOnly();
        }

        if (1 === $this->transactionLevel) {
            $this->getPdo(true)->commit();

            if ($this->poolTransaction) {
                $this->poolTransaction->remove();
            }
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->releaseSavepoint($this->getSavepointName()); // @codeCoverageIgnore
        }

        $this->transactionLevel = max(0, $this->transactionLevel - 1);
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack(): void
    {
        if (0 === $this->transactionLevel) {
            ConnectionException::noActiveTransaction('RollBack');
        }

        if (1 === $this->transactionLevel) {
            $this->transactionLevel = 0;
            $this->getPdo(true)->rollBack();
            $this->isRollbackOnly = false;

            if ($this->poolTransaction) {
                $this->poolTransaction->remove();
            }
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            // @codeCoverageIgnoreStart
            $this->rollbackSavepoint($this->getSavepointName());
            --$this->transactionLevel;
        // @codeCoverageIgnoreEnd
        } else {
            $this->isRollbackOnly = true;
            $this->transactionLevel = max(0, $this->transactionLevel - 1);
        }
    }

    /**
     * {@inheritDoc}
     *
     * - GitHub Actions 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    public function setSavepoints(bool $savepoints): void
    {
        $this->transactionWithSavepoints = $savepoints;
    }

    /**
     * {@inheritDoc}
     */
    public function hasSavepoints(): bool
    {
        return $this->transactionWithSavepoints;
    }

    /**
     * {@inheritDoc}
     */
    public function getLastSql(): string
    {
        return $this->sql;
    }

    /**
     * {@inheritDoc}
     */
    public function setRealLastSql(array $realLastSql): void
    {
        $this->realLastSql = $realLastSql;
    }

    /**
     * {@inheritDoc}
     */
    public function getRealLastSql(): array
    {
        return $this->realLastSql;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->masterPdo = null;
        $this->slavePdo = null;
    }

    /**
     * {@inheritDoc}
     */
    public static function getRawSql(string $sql, array $bindParams): string
    {
        $keys = $values = [];

        // Get longest keys first, sot the regex replacement doesn't
        // cut markers (ex : replace ":username" with "'joe'name"
        // if we have a param name :user
        $isNamedMarkers = false;
        if (\count($bindParams) && \is_string(key($bindParams))) {
            uksort($bindParams, function (string $k1, string $k2): int {
                return \strlen($k2) - \strlen($k1);
            });
            $isNamedMarkers = true;
        }

        foreach ($bindParams as $key => $value) {
            if (!\is_array($value) || 1 === \count($value)) {
                $dataType = null;
                $value = \is_array($value) ? reset($value) : $value;
            } else {
                [$value, $dataType] = $value;
            }

            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (\is_string($key)) {
                $keys[] = '/:'.ltrim($key, ':').'/';
            } else {
                $keys[] = '/[?]/';
            }

            switch (true) {
                case \PDO::PARAM_INT === $dataType:
                    $values[] = (string) $value;

                    break;

                case \PDO::PARAM_BOOL === $dataType:
                    $values[] = (string) $value;

                    break;

                case \PDO::PARAM_NULL === $dataType:
                    $values[] = 'NULL';

                    break;

                case \PDO::PARAM_STR === $dataType:
                    $values[] = "'".addslashes((string) $value)."'";

                    break;

                default:
                    if (\is_string($value)) {
                        $values[] = "'".addslashes($value)."'";
                    } elseif (\is_int($value)) {
                        $values[] = (string) $value;
                    } elseif (\is_float($value)) {
                        $values[] = (string) $value;
                    } elseif (\is_array($value)) {
                        $values[] = implode(',', $value);
                    } elseif (null === $value) {
                        $values[] = 'NULL';
                    }
            }
        }

        if ($isNamedMarkers) {
            return (string) preg_replace($keys, $values, $sql);
        }

        return (string) preg_replace($keys, $values, $sql, 1, $count);
    }

    /**
     * 初始化连接.
     */
    public function initConnect(array $config): void
    {
        $this->masterPdo = $this->createMasterConnection($config);

        if (!empty($config['separate']) && !empty($config['slave'])) {
            $this->slavePdo = $this->createSlaveConnection($config);
        }
    }

    protected function getMasterPdo(): \PDO
    {
        if (!$this->masterPdo) {
            $this->initConnect($this->config);
        }

        return $this->masterPdo;
    }

    protected function getSlavePdo(): \PDO
    {
        if ($this->shouldSticky()) {
            return $this->getMasterPdo();
        }

        return $this->slavePdo ?? $this->getMasterPdo();
    }

    protected function shouldSticky(): bool
    {
        return $this->transactionLevel > 0 && !empty($this->config['sticky']);
    }

    /**
     * 从缓存中获取查询数据.
     */
    protected function getDataFromCache(string $cacheName, ?ICache $cache = null): mixed
    {
        $cache = $this->determineCache($cache);
        if (false !== ($result = $cache->get($cacheName))) {
            return json_decode(json_encode($result, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
        }

        return false;
    }

    /**
     * 将查询数据写入缓存.
     */
    protected function setDataToCache(string $cacheName, array $data, ?int $cacheExpire = null, ?ICache $cache = null): void
    {
        $cache = $this->determineCache($cache);
        $cache->set($cacheName, $data, $cacheExpire);
    }

    /**
     * 确定使用的缓存.
     *
     * @throws \RuntimeException
     */
    protected function determineCache(?ICache $cache = null): ICache
    {
        if ($cache) {
            return $cache;
        }

        if (!$this->cache) {
            throw new \RuntimeException('Cache was not set.');
        }

        return $this->cache;
    }

    /**
     * 整理当前执行 SQL.
     */
    protected function normalizeLastSql(\PDOStatement $statement): string
    {
        ob_start();
        $statement->debugDumpParams();
        $sql = trim(ob_get_contents() ?: '', PHP_EOL.' ');
        $sql = str_replace(PHP_EOL, ' | ', $sql);
        ob_end_clean();

        return $sql;
    }

    /**
     * 整理当前错误执行 SQL.
     */
    protected function normalizeErrorLastSql(string $sql, array $bindParams): string
    {
        return $sql.' | '.json_encode($bindParams, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 整理 SQL 日志分类.
     */
    protected function normalizeSqlLogCategory(string $sql, bool $failed = false): string
    {
        return '[SQL'.($failed ? ':FAILED' : '').'] '.$sql;
    }

    /**
     * 获取主库配置.
     */
    protected function getMasterConfig(array $config): array
    {
        return $this->getMasterSlaveConfig($config, true);
    }

    /**
     * 获取从库配置.
     */
    protected function getSlaveConfig(array $config): array
    {
        return $this->getMasterSlaveConfig($config, false);
    }

    /**
     * 获取主从配置.
     */
    protected function getMasterSlaveConfig(array $config, bool $master): array
    {
        $type = $master ? 'master' : 'slave';

        return isset($config[$type][0])
            ? $config[$type][array_rand($config[$type])]
            : $config[$type];
    }

    /**
     * 连接主服务器.
     */
    protected function createMasterConnection(array $config): \PDO
    {
        return $this->createPdoConnection($this->getMasterConfig($config));
    }

    /**
     * 连接从服务器.
     */
    protected function createSlaveConnection(array $config): \PDO
    {
        return $this->createPdoConnection($this->getSlaveConfig($config));
    }

    /**
     * 连接pdo数据库.
     *
     * @throws \PDOException
     */
    protected function createPdoConnection(array $config): \PDO
    {
        if (\is_array($config['configs']) && isset($config['configs'][\PDO::ATTR_ERRMODE])) {
            ConnectionException::errModeExceptionOnly();
        }

        $e = null;
        foreach ($this->parseHost($config) as $host) {
            $config['host'] = $host;

            try {
                $connection = new \PDO(
                    $this->parseDsn($config),
                    $config['user'],
                    $config['password'],
                    $config['configs'] ?? null,
                );
                $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

                return $connection;
            } catch (\PDOException $e) {
                continue;
            }
        }

        // @phpstan-ignore-next-line
        throw $e;
    }

    /**
     * 解析数据库地址.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseHost(array $config): array
    {
        $host = Normalize::handle($config['host']);

        if (\is_array($host) && $host) {
            shuffle($host);

            return $host;
        }

        throw new \InvalidArgumentException('Database host is invalid.');
    }

    /**
     * 整理 PDO 参数绑定.
     */
    protected function normalizeBindParams(array $bindParams): array
    {
        $result = [];
        foreach ($bindParams as $key => $val) {
            $key = \is_int($key) || ctype_digit($key) ? (int) $key + 1 : ':'.$key;
            $val = \is_array($val) ? array_values($val) : [$val];
            if (!\array_key_exists(1, $val)
                && null !== ($bindParamType = $this->normalizeBindParamType($val[0]))) {
                $val[1] = $bindParamType;
            }
            $result[$key] = $val;
        }

        return $result;
    }

    /**
     * PDO 参数绑定.
     */
    protected function bindParams(\PDOStatement $statement, array $bindParams): void
    {
        foreach ($bindParams as $key => &$val) {
            $statement->{\count($val) >= 3 ? 'bindParam' : 'bindValue'}($key, ...$val);
        }
    }

    /**
     * 分析绑定参数类型数据.
     *
     * @todo 是否需要处理 \PDO::PARAM_LOB
     *
     * @see http://php.net/manual/en/pdo.constants.php
     */
    protected function normalizeBindParamType(mixed $value): ?int
    {
        switch (true) {
            case \is_int($value):
                return \PDO::PARAM_INT;

            case \is_bool($value):
                return \PDO::PARAM_BOOL;

            case null === $value:
                return \PDO::PARAM_NULL;

            case \is_string($value):
                return \PDO::PARAM_STR;

            default:
                return null;
        }
    }

    /**
     * 获得数据集.
     */
    protected function fetchResult(\PDOStatement $statement): array
    {
        // PHP8.0 开始 fetchAll 方法现在始终返回一个数组，而以前 false 可能在失败时返回。
        return $statement->fetchAll(\PDO::FETCH_OBJ);
    }

    /**
     * 获得存储过程数据集.
     */
    protected function fetchProcedureResult(\PDOStatement $statement): array
    {
        $result = [];
        while ($statement->columnCount()) {
            $result[] = $this->fetchResult($statement);
            $statement->nextRowset();
        }

        return $result;
    }

    /**
     * 设置最后执行 SQL.
     */
    protected function setLastSql(string $sql, bool $failed = false): void
    {
        $this->sql = ($failed ? '[FAILED] ' : '').$sql;
        $this->dispatch?->handle(
            IDatabase::SQL_EVENT,
            $this->normalizeSqlLogCategory($this->sql, $failed),
        );
    }

    /**
     * 获取部分事务回滚点名字.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function getSavepointName(): string
    {
        return 'trans'.$this->transactionLevel;
    }

    /**
     * 保存部分事务保存点.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function createSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'SAVEPOINT '.$savepointName);
        $this->getPdo(true)->exec($sql);
    }

    /**
     * 回滚部分事务到保存点.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function rollbackSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'ROLLBACK TO SAVEPOINT '.$savepointName);
        $this->getPdo(true)->exec($sql);
    }

    /**
     * 清除前面定义的部分事务保存点.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function releaseSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'RELEASE SAVEPOINT '.$savepointName);
        $this->getPdo(true)->exec($sql);
    }

    /**
     * 是否需要重连.
     */
    protected function needReconnect(\PDOException $e): bool
    {
        if (!$e->errorInfo || !isset($e->errorInfo[1])) {
            return false;
        }

        // errorInfo[1] 表示某个驱动错误码，后期扩展需要优化
        // 可以在驱动重写这个方法
        return \in_array((int) $e->errorInfo[1], [2006, 2013], true)
            && $this->reconnectRetry <= self::RECONNECT_MAX;
    }

    /**
     * PDO 异常处理.
     *
     * @throws \Leevel\Database\DuplicateKeyException
     */
    protected function pdoException(\PDOException $e): void
    {
        $message = $e->getMessage();

        // 模拟数据库 replace
        if (23000 === (int) $e->getCode()
              && str_contains($message, 'Duplicate entry')) {
            $e = new DuplicateKeyException($message);
            $e->setUniqueIndex($this->parseDuplicateUniqueIndex($message));

            throw $e;
        }

        throw $e;
    }

    protected function parseDuplicateUniqueIndex(string $message): string
    {
        $pattern = "/for key '(\\w+)'/";
        $matches = [];
        if (preg_match($pattern, $message, $matches)) {
            return $matches[1];
        }

        return '';
    }
}
