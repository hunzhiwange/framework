<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Server\Pool\Connection;
use Leevel\Support\Arr\Normalize;
use Leevel\Support\Collection;

/**
 * 数据库抽象层.
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

    public function forPage(int $page, int $perPage = 10): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $page, $perPage);
    }

    public function time(string $type = 'date'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $type);
    }

    public function endTime(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function reset(?string $config = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $config);
    }

    public function comment(string $comment): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $comment);
    }

    public function prefix(string $prefix): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $prefix);
    }

    public function table(array|\Closure|ICondition|ISelect|string $table, array|string $cols = '*'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols);
    }

    public function columns(array|string $cols = '*', ?string $table = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function setColumns(array|string $cols = '*', ?string $table = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function field(array|string $cols = '*', ?string $table = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function middlewares(string ...$middlewares): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$middlewares);
    }

    public function where(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orWhere(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orWhereRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function whereExists(mixed $exists): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereNotExists(mixed $exists): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDate(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDay(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereMonth(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereYear(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $names, $value, $dataType);
    }

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes, $type);
    }

    public function ignoreIndex(array|string $indexes): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes);
    }

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects, $type);
    }

    public function unionAll(array|callable|ICondition|self|string $selects): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects);
    }

    public function groupBy(array|string $expression): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression);
    }

    public function having(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orHaving(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orHavingRaw(string $raw): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function havingBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotBetween(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotNull(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotIn(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotLike(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDate(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDay(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingMonth(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingYear(mixed ...$cond): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression, $orderDefault);
    }

    public function latest(string $field = 'create_at'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function oldest(string $field = 'create_at'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function distinct(bool $flag = true): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function count(string $field = '*', string $alias = 'row_count'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function avg(string $field, string $alias = 'avg_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function max(string $field, string $alias = 'max_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function min(string $field, string $alias = 'min_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function sum(string $field, string $alias = 'sum_value'): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function one(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function all(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function top(int $count = 30): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $count);
    }

    public function limit(int $offset = 0, int $count = 0): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $offset, $count);
    }

    public function forUpdate(bool $flag = true): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function lockShare(bool $flag = true): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function resetBindParams(array $bindParams = []): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParams);
    }

    public function setBindParamsPrefix(string $bindParamsPrefix): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParamsPrefix);
    }

    public function if(mixed $condition): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function elif(mixed $condition): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function else(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function fi(): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function getAlias(): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public static function raw(string $raw): string
    {
        return Condition::raw($raw);
    }

    public function makeSql(bool $withLogicGroup = false): string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $withLogicGroup);
    }

    public function getBindParams(): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function databaseCondition(): Condition
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function databaseConnect(): IDatabase
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function master(bool $master = false): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $master);
    }

    public function asSome(?\Closure $asSome = null, array $args = []): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asSome, $args);
    }

    public function asArray(?\Closure $asArray = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asArray);
    }

    public function asCollection(bool $asCollection = true, array $valueTypes = []): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asCollection, $valueTypes);
    }

    public function select(null|callable|ISelect|string $data = null, array $bind = []): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $data, $bind);
    }

    public function insert(array|string $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind, $replace);
    }

    public function insertAll(array $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind, $replace);
    }

    public function update(array|string $data, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind);
    }

    public function updateColumn(string $column, mixed $value, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $column, $value, $bind);
    }

    public function updateIncrease(string $column, int $step = 1, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $column, $step, $bind);
    }

    public function updateDecrease(string $column, int $step = 1, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $column, $step, $bind);
    }

    public function delete(?string $data = null, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $data, $bind);
    }

    public function truncate(): array|int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findOne(): mixed
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findArray(): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findAsArray(): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findCollection(): Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__);
    }

    public function findAll(?\Closure $condition = null): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $condition);
    }

    public function find(?int $num = null): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $num);
    }

    public function value(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findList(mixed $fieldValue, ?string $fieldKey = null, ?\Closure $condition = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $fieldValue, $fieldKey, $condition);
    }

    public function chunk(int $count, \Closure $chunk): void
    {
        $this->callSelectMethod(__FUNCTION__, $count, $chunk);
    }

    public function each(int $count, \Closure $each): void
    {
        $this->callSelectMethod(__FUNCTION__, $count, $each);
    }

    public function findCount(string $field = '*', ?\Closure $condition = null): int
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findAvg(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findMax(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findMin(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findSum(string $field, ?\Closure $condition = null): mixed
    {
        return $this->callSelectMethod(__FUNCTION__, $field, $condition);
    }

    public function findPageList(int $currentPage, int $perPage = 10, ?int $count = null, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $count, $config, $condition);
    }

    public function findPageMacro(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $config, $condition);
    }

    public function findPagePrevNext(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        // @phpstan-ignore-next-line
        return $this->callSelectMethod(__FUNCTION__, $currentPage, $perPage, $config, $condition);
    }

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): Select
    {
        return $this->callSelectMethodChain(__FUNCTION__, $name, $expire, $cache);
    }

    public function getContainer(): IContainer
    {
        return $this->container;
    }

    public function setCache(?ICache $cache): void
    {
        $this->cache = $cache;
    }

    public function getCache(): ?ICache
    {
        return $this->cache;
    }

    public function databaseSelect(): Select
    {
        return new Select($this);
    }

    public function getPdo(bool $master = false): \PDO
    {
        return $master ? $this->getMasterPdo() : $this->getSlavePdo();
    }

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

    public function cursor(string $sql, array $bindParams = [], bool $master = false): \Generator
    {
        $statement = $this->prepare($sql, $bindParams, $master);
        while ($value = $statement->fetch(\PDO::FETCH_OBJ)) {
            yield $value;
        }
    }

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

    public function inTransaction(): bool
    {
        return $this->getPdo(true)->inTransaction();
    }

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
     * @codeCoverageIgnore
     */
    public function setSavepoints(bool $savepoints): void
    {
        // GitHub Actions 无法通过测试忽略
        $this->transactionWithSavepoints = $savepoints;
    }

    public function hasSavepoints(): bool
    {
        return $this->transactionWithSavepoints;
    }

    public function getLastSql(): string
    {
        return $this->sql;
    }

    public function setRealLastSql(array $realLastSql): void
    {
        $this->realLastSql = $realLastSql;
    }

    public function getRealLastSql(): array
    {
        return $this->realLastSql;
    }

    public function close(): void
    {
        $this->masterPdo = null;
        $this->slavePdo = null;
    }

    public static function getRawSql(string $sql, array $bindParams): string
    {
        $keys = $values = [];

        // Get longest keys first, sot the regex replacement doesn't
        // cut markers (ex : replace ":username" with "'joe'name"
        // if we have a param name :user
        $isNamedMarkers = false;
        if (\count($bindParams) && \is_string(key($bindParams))) {
            uksort($bindParams, static function (string $k1, string $k2): int {
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

    protected function callSelectMethodChain(string $method, mixed ...$args): Select
    {
        return $this->databaseSelect()->{$method}(...$args);
    }

    protected function callSelectMethod(string $method, mixed ...$args): mixed
    {
        return $this->databaseSelect()->{$method}(...$args);
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
     * @throws DuplicateKeyException
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
