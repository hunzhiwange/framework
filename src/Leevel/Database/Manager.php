<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Server\Pool\Pool as BasePool;
use Leevel\Support\Collection;
use Leevel\Support\Manager as Managers;
use Swoole\Coroutine;

/**
 * 数据库管理器.
 */
class Manager extends Managers implements IDatabase
{
    /**
     * 数据库连接池.
     */
    protected array $pools = [];

    /**
     * 数据库连接池事务管理器.
     */
    protected array $poolTransactions = [];

    public function getContainer(): IContainer
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function setCache(?ICache $cache): void
    {
        $this->callConnectMethod(__FUNCTION__, $cache);
    }

    public function getCache(): ?ICache
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function databaseSelect(): ISelect
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function getPdo(bool $master = false): \PDO
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $master);
    }

    public function query(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $sql, $bindParams, $master, $cacheName, $cacheExpire, $cache);
    }

    public function procedure(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $sql, $bindParams, $master, $cacheName, $cacheExpire, $cache);
    }

    public function execute(string $sql, array $bindParams = [], bool $master = false, bool $insert = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $sql, $bindParams, $master, $insert);
    }

    public function cursor(string $sql, array $bindParams = [], bool $master = false): \Generator
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $sql, $bindParams, $master);
    }

    public function prepare(string $sql, array $bindParams = [], bool $master = false): \PDOStatement
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $sql, $bindParams, $master);
    }

    public function transaction(\Closure $action): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $action);
    }

    public function beginTransaction(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public function inTransaction(): bool
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function commit(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public function rollBack(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public function setSavepoints(bool $savepoints): void
    {
        $this->callConnectMethod(__FUNCTION__, $savepoints);
    }

    public function hasSavepoints(): bool
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function getLastSql(): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function setRealLastSql(array $realLastSql): void
    {
        $this->callConnectMethod(__FUNCTION__, $realLastSql);
    }

    public function getRealLastSql(): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function close(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public static function getRawSql(string $sql, array $bindParams): string
    {
        return Database::getRawSql($sql, $bindParams);
    }

    public function parseDsn(array $config): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $config);
    }

    public function getTableNames(string $dbName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $dbName, $master);
    }

    public function getTableColumns(string $tableName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $tableName, $master);
    }

    public function getUniqueIndex(string $tableName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $tableName, $master);
    }

    public function identifierColumn(string $name): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $name);
    }

    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $limitCount, $limitOffset);
    }

    public function release(): void
    {
        $this->callConnectMethod(__FUNCTION__);
    }

    public function setPool(BasePool $pool): void
    {
        $this->callConnectMethod(__FUNCTION__, $pool);
    }

    public function forPage(int $page, int $perPage = 10): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $page, $perPage);
    }

    public function time(string $type = 'date'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $type);
    }

    public function endTime(): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function reset(?string $config = null): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $config);
    }

    public function comment(string $comment): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $comment);
    }

    public function prefix(string $prefix): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $prefix);
    }

    public function table(array|\Closure|ICondition|ISelect|string $table, array|string $cols = '*'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols);
    }

    public function columns(array|string $cols = '*', ?string $table = null): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function setColumns(array|string $cols = '*', ?string $table = null): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function field(array|string $cols = '*', ?string $table = null): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $cols, $table);
    }

    public function middlewares(string ...$middlewares): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$middlewares);
    }

    public function where(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orWhere(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereRaw(string $raw): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orWhereRaw(string $raw): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function whereExists(mixed $exists): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereNotExists(mixed $exists): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $exists);
    }

    public function whereBetween(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotBetween(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNull(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotNull(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereIn(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotIn(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereLike(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotLike(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDate(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDay(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereMonth(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereYear(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $names, $value, $dataType);
    }

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes, $type);
    }

    public function ignoreIndex(array|string $indexes): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $indexes);
    }

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects, $type);
    }

    public function unionAll(array|callable|ICondition|self|string $selects): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $selects);
    }

    public function groupBy(array|string $expression): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression);
    }

    public function having(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orHaving(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingRaw(string $raw): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function orHavingRaw(string $raw): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $raw);
    }

    public function havingBetween(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotBetween(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNull(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotNull(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingIn(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotIn(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingLike(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotLike(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDate(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDay(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingMonth(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingYear(mixed ...$cond): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, ...$cond);
    }

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $expression, $orderDefault);
    }

    public function latest(string $field = 'create_at'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function oldest(string $field = 'create_at'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field);
    }

    public function distinct(bool $flag = true): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function count(string $field = '*', string $alias = 'row_count'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function avg(string $field, string $alias = 'avg_value'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function max(string $field, string $alias = 'max_value'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function min(string $field, string $alias = 'min_value'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function sum(string $field, string $alias = 'sum_value'): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $field, $alias);
    }

    public function one(): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function all(): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function top(int $count = 30): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $count);
    }

    public function limit(int $offset = 0, int $count = 0): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $offset, $count);
    }

    public function forUpdate(bool $flag = true): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function lockShare(bool $flag = true): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $flag);
    }

    public function resetBindParams(array $bindParams = []): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParams);
    }

    public function setBindParamsPrefix(string $bindParamsPrefix): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $bindParamsPrefix);
    }

    public function if(mixed $condition): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function elif(mixed $condition): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $condition);
    }

    public function else(): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function fi(): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__);
    }

    public function getAlias(): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public static function raw(string $raw): string
    {
        return Condition::raw($raw);
    }

    public function makeSql(bool $withLogicGroup = false): string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $withLogicGroup);
    }

    public function getBindParams(): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function databaseCondition(): Condition
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function databaseConnect(): IDatabase
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function master(bool $master = false): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $master);
    }

    public function asSome(?\Closure $asSome = null, array $args = []): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asSome, $args);
    }

    public function asArray(?\Closure $asArray = null): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asArray);
    }

    public function asCollection(bool $asCollection = true, array $valueTypes = []): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $asCollection, $valueTypes);
    }

    public function select(null|callable|ISelect|string $data = null, array $bind = []): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $data, $bind);
    }

    public function insert(array|string $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $data, $bind, $replace);
    }

    public function insertAll(array $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $data, $bind, $replace);
    }

    public function update(array|string $data, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $data, $bind);
    }

    public function updateColumn(string $column, mixed $value, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $column, $value, $bind);
    }

    public function updateIncrease(string $column, int $step = 1, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $column, $step, $bind);
    }

    public function updateDecrease(string $column, int $step = 1, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $column, $step, $bind);
    }

    public function delete(?string $data = null, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $data, $bind);
    }

    public function truncate(): array|int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function findOne(): mixed
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function findArray(): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function findAsArray(): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function findCollection(): Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function findAll(): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function find(?int $num = null): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $num);
    }

    public function value(string $field): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $field);
    }

    public function list(mixed $fieldValue, ?string $fieldKey = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $fieldValue, $fieldKey);
    }

    public function chunk(int $count, \Closure $chunk): void
    {
        $this->callConnectMethod(__FUNCTION__, $count, $chunk);
    }

    public function each(int $count, \Closure $each): void
    {
        $this->callConnectMethod(__FUNCTION__, $count, $each);
    }

    public function findCount(string $field = '*', string $alias = 'row_count'): int
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $field, $alias);
    }

    public function findAvg(string $field, string $alias = 'avg_value'): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $field, $alias);
    }

    public function findMax(string $field, string $alias = 'max_value'): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $field, $alias);
    }

    public function findMin(string $field, string $alias = 'min_value'): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $field, $alias);
    }

    public function findSum(string $field, string $alias = 'sum_value'): mixed
    {
        return $this->callConnectMethod(__FUNCTION__, $field, $alias);
    }

    public function page(int $currentPage, int $perPage = 10, ?int $count = null, array $config = []): Page
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $currentPage, $perPage, $count, $config);
    }

    public function pageMacro(int $currentPage, int $perPage = 10, array $config = []): Page
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $currentPage, $perPage, $config);
    }

    public function pagePrevNext(int $currentPage, int $perPage = 10, array $config = []): Page
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__, $currentPage, $perPage, $config);
    }

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): ISelect
    {
        return $this->callSelectMethodChain(__FUNCTION__, $name, $expire, $cache);
    }

    public function queryOne(): mixed
    {
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function queryAll(): array|Collection|EntityCollection
    {
        // @phpstan-ignore-next-line
        return $this->callConnectMethod(__FUNCTION__);
    }

    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): IDatabase
    {
        // 协程环境每次从创建驱动中获取连接
        if ($this->container->enabledCoroutine()) {
            $newConnect = true;
        }

        return parent::connect($connect, $newConnect, ...$arguments);
    }

    public function reconnect(?string $connect = null, ...$arguments): IDatabase
    {
        return parent::reconnect($connect, ...$arguments);
    }

    protected function callSelectMethodChain(string $method, mixed ...$args): ISelect
    {
        return $this->callConnectMethod($method, ...$args);
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
            $this->container,
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
        Coroutine::defer(static fn () => $connection->release());

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
        $type = ['separate', 'driver', 'master', 'slave'];

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

        if (empty($config['separate'])) {
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
