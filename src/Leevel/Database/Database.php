<?php

declare(strict_types=1);

namespace Leevel\Database;

use Closure;
use Exception;
use Generator;
use InvalidArgumentException;
use Leevel\Cache\Manager as CacheManager;
use Leevel\Event\IDispatch;
use Leevel\Protocol\Pool\Connection;
use Leevel\Protocol\Pool\IConnection;
use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

/**
 * 数据库抽象层.
 *
 * @method static \Leevel\Database\Condition databaseCondition()                                                                                查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                  返回数据库连接对象.
 * @method static \Leevel\Database\Select sql(bool $flag = true)                                                                                指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Select master($master = false)                                                                               设置是否查询主服务器.
 * @method static \Leevel\Database\Select asSome(?\Closure $asSome = null, array $args = [])                                                    设置以某种包装返会结果.
 * @method static \Leevel\Database\Select asArray(?\Closure $asArray = null)                                                                    设置返会结果为数组.
 * @method static \Leevel\Database\Select asCollection(bool $asCollection = true)                                                               设置是否以集合返回.
 * @method static mixed select($data = null, array $bind = [], bool $flag = false)                                                              原生 SQL 查询数据.
 * @method static null|array|int insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                              插入数据 insert (支持原生 SQL).
 * @method static null|array|int insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                     批量插入数据 insertAll.
 * @method static array|int update($data, array $bind = [], bool $flag = false)                                                                     更新数据 update (支持原生 SQL).
 * @method static array|int updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                              更新某个字段的值
 * @method static array|int updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                     字段递增.
 * @method static array|int updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                     字段减少.
 * @method static array|int delete(?string $data = null, array $bind = [], bool $flag = false)                                                      删除数据 delete (支持原生 SQL).
 * @method static array|int truncate(bool $flag = false)                                                                                            清空表重置自增 ID.
 * @method static mixed findOne(bool $flag = false)                                                                                             返回一条记录.
 * @method static mixed findAll(bool $flag = false)                                                                                             返回所有记录.
 * @method static mixed find(?int $num = null, bool $flag = false)                                                                              返回最后几条记录.
 * @method static mixed value(string $field, bool $flag = false)                                                                                返回一个字段的值
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                                         返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                       数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                         数据分块处理依次回调.
 * @method static array|int findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                         总记录数.
 * @method static mixed findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                 平均数.
 * @method static mixed findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                 最大值.
 * @method static mixed findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                 最小值.
 * @method static mixed findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                 合计.
 * @method static \Leevel\Database\Page page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []) 分页查询.
 * @method static \Leevel\Database\Page pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                  创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])               创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                             取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                  获得查询字符串.
 * @method static \Leevel\Database\Select cache(string $name, ?int $expire = null, ?string $connect = null)                                     设置查询缓存.
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 10)                                                                 根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                                           时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                             时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                                         重置查询条件.
 * @method static \Leevel\Database\Select comment(string $comment)                                                                              查询注释.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                                prefix 查询.
 * @method static \Leevel\Database\Select table($table, $cols = '*')                                                                            添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                             获取表别名.
 * @method static \Leevel\Database\Select columns($cols = '*', ?string $table = null)                                                           添加字段.
 * @method static \Leevel\Database\Select setColumns($cols = '*', ?string $table = null)                                                        设置字段.
 * @method static string raw(string $raw)                                                                                                       原生查询.
 * @method static \Leevel\Database\Select where(...$cond)                                                                                       where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                                     orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                                 Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                               Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                                  exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                               not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                                whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                             whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                                   whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                                whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                                     whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                                  whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                                   whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                                whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                                   whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                                    whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                                  whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                                   whereYear 查询条件.
 * @method static \Leevel\Database\Select bind($names, $value = null, ?int $dataType = null)                                                    参数绑定支持.
 * @method static \Leevel\Database\Select forceIndex($indexs, $type = 'FORCE')                                                                  index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex($indexs)                                                                                  index 忽略索引.
 * @method static \Leevel\Database\Select join($table, $cols, ...$cond)                                                                         join 查询.
 * @method static \Leevel\Database\Select innerJoin($table, $cols, ...$cond)                                                                    innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin($table, $cols, ...$cond)                                                                     leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin($table, $cols, ...$cond)                                                                    rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin($table, $cols, ...$cond)                                                                     fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin($table, $cols, ...$cond)                                                                    crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin($table, $cols, ...$cond)                                                                  naturalJoin 查询.
 * @method static \Leevel\Database\Select union($selects, string $type = 'UNION')                                                               添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll($selects)                                                                                    添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy($expression)                                                                                  指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                                      添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                                    orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                                having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                              having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                               havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                            havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                                  havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                               havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                                    havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                                 havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                                  havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                               havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                                  havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                                   havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                                 havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                                  havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy($expression, string $orderDefault = 'ASC')                                                    添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                                   最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                                   最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                                           创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                               总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                                       平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                                       最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                                       最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                                       合计
 * @method static \Leevel\Database\Select one()                                                                                                 指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                                 指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                                  查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                                limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                                          排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select lockShare(bool $flag = true)                                                                          共享锁 LOCK SHARE 查询.
 * @method static array getBindParams()                                                                                                         返回参数绑定.                                                                                                        返回参数绑定.
 * @method static void resetBindParams(array $bindParams = [])                                                                                  重置参数绑定.
 * @method static void setBindParamsPrefix(string $bindParamsPrefix)                                                                            设置参数绑定前缀.
 */
abstract class Database implements IDatabase, IConnection
{
    use Connection {
        release as baseRelease;
    }

    /**
     * 所有数据库连接.
     */
    protected array $connects = [];

    /**
     * 当前数据库连接.
     */
    protected ?PDO $connect = null;

    /**
     * PDO 预处理语句对象
     */
    protected ?PDOStatement $pdoStatement = null;

    /**
     * 数据查询组件.
     */
    protected ?Select $select = null;

    /**
     * 数据库连接参数.
     *
     * - separate:数据库读写是否分离
     * - distributed:是否采用分布式
     * - master:分布式服务部署主服务器
     * - master.host:数据库 host，默认为 localhost
     * - master.port:端口
     * - master.name:数据库名字
     * - master.user:数据库用户名
     * - master.password:数据库密码
     * - master.charset:数据库编码
     * - master.options:连接参数
     * - slave:分布式服务部署模式中，附属服务器列表
     */
    protected array $option = [
        'separate'    => false,
        'distributed' => false,
        'master'      => [
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'name'     => '',
            'user'     => '',
            'password' => '',
            'charset'  => 'utf8',
            'options'  => [
                PDO::ATTR_PERSISTENT        => false,
                PDO::ATTR_CASE              => PDO::CASE_NATURAL,
                PDO::ATTR_ORACLE_NULLS      => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_EMULATE_PREPARES  => false,
                PDO::ATTR_TIMEOUT           => 30,
            ],
        ],
        'slave' => [],
    ];

    /**
     * SQL 最后查询语句.
    */
    protected ?string $sql = null;

    /**
     * SQL 影响记录数量.
     */
    protected int $numRows = 0;

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
     * 是否仅仅是事务回滚.
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
     * 连接管理.
     */
    protected ?Manager $manager = null;

    /**
     * 缓存管理.
     */
    protected ?CacheManager $cache = null;

    /**
     * 构造函数.
     */
    public function __construct(array $option, ?IDispatch $dispatch = null, ?Manager $manager = null)
    {
        $this->option = array_merge($this->option, $option);
        $this->dispatch = $dispatch;
        $this->manager = $manager;
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
        $this->initSelect();

        return $this->select->{$method}(...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function setCache(?CacheManager $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function getCache(): ?CacheManager
    {
        return $this->cache;
    }

    /**
     * {@inheritDoc}
     */
    public function databaseSelect(): Select
    {
        if (!$this->select) {
            $this->initSelect();
        }

        return $this->select;
    }

    /**
     * {@inheritDoc}
     */
    public function pdo(bool|int $master = false): ?PDO 
    {
        if (is_bool($master)) {
            return false === $master ? $this->readConnect() : $this->writeConnect();
        }

        return $this->connects[$master] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function query(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null): mixed
    {
        if ($cacheName && false !== ($result = $this->getDataFromCache($cacheName, $cacheConnect))) {
            return $result;
        }

        $this->initSelect();
        $this->prepare($sql, $bindParams, $master);
        $result = $this->fetchResult();
        $this->release();
        if ($cacheName) {
            $this->setDataToCache($cacheName, (array) $result, $cacheExpire, $cacheConnect);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function procedure(string $sql, array $bindParams = [], bool|int $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?string $cacheConnect = null): array
    {
        if ($cacheName && false !== ($result = $this->getDataFromCache($cacheName, $cacheConnect))) {
            return $result;
        }

        $this->initSelect();
        $this->prepare($sql, $bindParams, $master);
        $result = $this->fetchProcedureResult();
        $this->release();
        if ($cacheName) {
            $this->setDataToCache($cacheName, $result, $cacheExpire, $cacheConnect);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(string $sql, array $bindParams = []): int|string
    {
        $this->initSelect();
        $this->prepare($sql, $bindParams, true);
        if (ctype_digit($lastInsertId = $this->lastInsertId())) {
            $lastInsertId = (int) $lastInsertId;
        }
        $this->release();

        /*
         * 底层数据库不支持自增字段或者表没有设计自增字段，insert 操作 lastInsertId 会返回 0，此时将会返回受影响记录。
         * 这个场景开发者需要注意一下。
         */
        return $lastInsertId ?: $this->numRows;
    }

    /**
     * {@inheritDoc}
     */
    public function cursor(string $sql, array $bindParams = [], bool|int $master = false): Generator
    {
        $this->initSelect();
        $this->prepare($sql, $bindParams, $master);
        $result = (function (): Generator {
            while ($value = $this->pdoStatement->fetch(PDO::FETCH_OBJ)) {
                yield $value;
            }
            $this->release();
        })();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function prepare(string $sql, array $bindParams = [], bool|int $master = false): PDOStatement
    {
        try {
            $bindParamsResult = $this->normalizeBindParams($bindParams);
            $rawSql = ' ('.static::getRawSql($sql, $bindParamsResult).')';
            $this->pdoStatement = $this->pdo($master)->prepare($sql);
            $this->bindParams($bindParamsResult);
            $this->pdoStatement->execute();
            $this->setLastSql($this->normalizeLastSql($this->pdoStatement).$rawSql);
            $this->reconnectRetry = 0;
        } catch (PDOException $e) {
            if ($this->needReconnect($e)) {
                $this->reconnectRetry++;
                $this->close();

                return $this->prepare($sql, $bindParams, $master);
            }

            if ($this->pdoStatement) {
                $sql = $this->normalizeLastSql($this->pdoStatement);
            } else {
                $sql = $this->normalizeErrorLastSql($sql, $bindParamsResult);
            }
            $this->setLastSql($sql.$rawSql, true);
            $this->pdoException($e);
        }

        $this->numRows = $this->pdoStatement->rowCount();

        return $this->pdoStatement;
    }

    /**
     * {@inheritDoc}
     */
    public function transaction(Closure $action): mixed
    {
        $this->beginTransaction();

        try {
            $result = $action($this);
            $this->commit();

            return $result;
        } catch (Exception $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction(): void
    {
        $this->transactionLevel++;

        if (1 === $this->transactionLevel) {
            try { // @codeCoverageIgnore
                $this->pdo(true)->beginTransaction();
                if ($this->manager) {
                    $this->manager->setTransactionConnection($this);
                }
                // @codeCoverageIgnoreStart
            } catch (Exception $e) {
                $this->transactionLevel--;

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
        return $this->pdo(true)->inTransaction();
    }

    /**
     * {@inheritDoc}
     * 
     * @throws \InvalidArgumentException
     */
    public function commit(): void
    {
        if (0 === $this->transactionLevel) {
            $e = 'There was no active transaction.';

            throw new InvalidArgumentException($e);
        }

        if ($this->isRollbackOnly) {
            $e = 'Commit failed for rollback only.';

            throw new InvalidArgumentException($e);
        }

        if (1 === $this->transactionLevel) {
            $this->pdo(true)->commit();
            if ($this->manager) {
                $this->manager->removeTransactionConnection();
                $this->release();
            }
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->releaseSavepoint($this->getSavepointName()); // @codeCoverageIgnore
        }

        $this->transactionLevel = max(0, $this->transactionLevel - 1);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function rollBack(): void
    {
        if (0 === $this->transactionLevel) {
            $e = 'There was no active transaction.';

            throw new InvalidArgumentException($e);
        }

        if (1 === $this->transactionLevel) {
            $this->transactionLevel = 0;
            $this->pdo(true)->rollBack();
            $this->isRollbackOnly = false;
            if ($this->manager) {
                $this->manager->removeTransactionConnection();
                $this->release();
            }
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            // @codeCoverageIgnoreStart
            $this->rollbackSavepoint($this->getSavepointName());
            $this->transactionLevel--;
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
    public function lastInsertId(?string $name = null): string
    {
        return $this->connect->lastInsertId($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastSql(): ?string
    {
        return $this->sql;
    }

    /**
     * {@inheritDoc}
     */
    public function numRows(): int
    {
        return $this->numRows;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): void
    {
        $this->freePDOStatement();
        $this->closeConnects();
    }

    /**
     * {@inheritDoc}
     */
    public function freePDOStatement(): void
    {
        // Fix errors
        // Error while sending STMT_CLOSE packet. PID=32336
        // PHP Fatal error:  Uncaught Error while sending STMT_CLOSE packet. PID=32336
        try {
            $this->pdoStatement = null;
        } catch (Exception) {
        }
    }

    /**
     * {@inheritDoc}
     */
    public function closeConnects(): void
    {
        $this->connects = [];
        $this->connect = null;
    }

    /**
     * {@inheritDoc}
     */
    public function release(): void
    {
        if (!$this->manager) {
            return;
        }

        if (!$this->manager->inTransactionConnection()) {
            $this->baseRelease();
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getRawSql(string $sql, array $bindParams): string
    {
        $keys = $values = [];

        /*
         * Get longest keys first, sot the regex replacement doesn't
         * cut markers (ex : replace ":username" with "'joe'name"
         * if we have a param name :user )
         */
        $isNamedMarkers = false;
        if (count($bindParams) && is_string(key($bindParams))) {
            uksort($bindParams, function (string $k1, string $k2): int {
                return strlen($k2) - strlen($k1);
            });
            $isNamedMarkers = true;
        }

        foreach ($bindParams as $key => $value) {
            if (!is_array($value) || 1 === count($value)) {
                $dataType = null;
                $value = is_array($value) ? reset($value) : $value;
            } else {
                list($value, $dataType) = $value;
            }

            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (is_string($key)) {
                $keys[] = '/:'.ltrim($key, ':').'/';
            } else {
                $keys[] = '/[?]/';
            }

            switch (true) {
                case PDO::PARAM_INT === $dataType:
                    $values[] = (string) $value;

                    break;
                case PDO::PARAM_BOOL === $dataType:
                    $values[] = (string) $value;

                    break;
                case PDO::PARAM_NULL === $dataType:
                    $values[] = 'NULL';

                    break;
                case PDO::PARAM_STR === $dataType:
                    $values[] = "'".addslashes((string) $value)."'";

                    break;
                default:
                    if (is_string($value)) {
                        $values[] = "'".addslashes($value)."'";
                    } elseif (is_int($value)) {
                        $values[] = (string) $value;
                    } elseif (is_float($value)) {
                        $values[] = (string) $value;
                    } elseif (is_array($value)) {
                        $values[] = implode(',', $value);
                    } elseif (null === $value) {
                        $values[] = 'NULL';
                    }
            }
        }

        if ($isNamedMarkers) {
            return preg_replace($keys, $values, $sql);
        }

        return preg_replace($keys, $values, $sql, 1, $count);
    }

    /**
     * 从缓存中获取查询数据.
     */
    protected function getDataFromCache(string $cacheName, ?string $cacheConnect = null): mixed
    {
        $this->validateCache();

        if (false !== ($result = $this->cache->connect($cacheConnect)->get($cacheName))) {
            return json_decode(json_encode($result, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
        }

        return false;
    }

    /**
     * 将查询数据写入缓存.
     */
    protected function setDataToCache(string $cacheName, array $data, ?int $cacheExpire = null, ?string $cacheConnect = null): void
    {
        $this->validateCache();
        $this->cache->connect($cacheConnect)->set($cacheName, $data, $cacheExpire);
    }

    /**
     * 校验缓存管理.
     *
     * @throws \RuntimeException
     */
    protected function validateCache(): void
    {
        if (!$this->cache) {
            throw new RuntimeException('Cache manager was not set.');
        }
    }

    /**
     * 整理当前执行 SQL.
     */
    protected function normalizeLastSql(PDOStatement $pdoStatement): string
    {
        ob_start();
        $pdoStatement->debugDumpParams();
        $sql = trim(ob_get_contents(), PHP_EOL.' ');
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
        return '[SQL'.(true === $failed ? ':FAILED' : '').'] '.$sql;
    }

    /**
     * 连接主服务器.
     */
    protected function writeConnect(): PDO
    {
        return $this->connect = $this->commonConnect($this->option['master'], IDatabase::MASTER, true);
    }

    /**
     * 连接读服务器.
     */
    protected function readConnect(): PDO
    {
        if (false === $this->option['distributed'] || empty($this->option['slave'])) {
            return $this->writeConnect();
        }

        if (count($this->connects) <= 1) {
            foreach ($this->option['slave'] as $read) {
                $this->commonConnect($read, null);
            }

            if (0 === count($this->connects)) {
                return $this->writeConnect();
            }
        }

        $connects = $this->connects;
        if (true === $this->option['separate'] && isset($connects[IDatabase::MASTER])) {
            unset($connects[IDatabase::MASTER]);
        }

        if (!$connects) {
            return $this->writeConnect();
        }

        $connects = array_values($connects);
        if (1 === count($connects)) {
            return $connects[0];
        }

        return $this->connect = $connects[floor(mt_rand(0, count($connects) - 1))];
    }

    /**
     * 连接数据库.
     *
     * @throws \InvalidArgumentException
     */
    protected function commonConnect(array $option = [], ?int $linkid = null, bool $throwException = false): mixed
    {
        if (null === $linkid) {
            $linkid = count($this->connects);
        }

        if (!empty($this->connects[$linkid])) {
            return $this->connects[$linkid];
        }

        if (is_array($option['options']) && isset($option['options'][PDO::ATTR_ERRMODE])) {
            $e = 'PDO query property \PDO::ATTR_ERRMODE cannot be set,it is always \PDO::ERRMODE_EXCEPTION.';

            throw new InvalidArgumentException($e);
        }

        try {
            $connect = new PDO(
                $this->parseDsn($option),
                $option['user'],
                $option['password'],
                $option['options'] ?? null,
            );
            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connects[$linkid] = $connect;
        } catch (PDOException $e) {
            if (false === $throwException) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * 整理 PDO 参数绑定.
     */
    protected function normalizeBindParams(array $bindParams): array
    {
        $result = [];
        foreach ($bindParams as $key => $val) {
            $key = is_int($key) || ctype_digit($key) ? (int) $key + 1 : ':'.$key;
            $val = is_array($val) ? array_values($val) : [$val];
            if (!array_key_exists(1, $val) &&
                null !== ($bindParamType = $this->normalizeBindParamType($val[0]))) {
                $val[1] = $bindParamType;
            }
            $result[$key] = $val;
        }

        return $result;
    }

    /**
     * PDO 参数绑定.
     */
    protected function bindParams(array $bindParams): void
    {
        foreach ($bindParams as $key => &$val) {
            $this->pdoStatement->{count($val) >= 3 ? 'bindParam' : 'bindValue'}($key, ...$val);
        }
    }

    /**
     * 分析绑定参数类型数据.
     *
     * @see http://php.net/manual/en/pdo.constants.php
     */
    protected function normalizeBindParamType(mixed $value): ?int
    {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case null === $value:
                return PDO::PARAM_NULL;
            case is_string($value):
                return PDO::PARAM_STR;
            default:
                return null;
        }
    }

    /**
     * 获得数据集.
     */
    protected function fetchResult(): array
    {
        return $this->pdoStatement->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * 获得存储过程数据集.
     *
     * @see http://php.net/manual/vote-note.php?id=123030&page=pdostatement.nextrowset&vote=down
     */
    protected function fetchProcedureResult(): array
    {
        $result = [];
        do {
            try {
                $result[] = $this->fetchResult();
            } catch (PDOException) { // @codeCoverageIgnore
            }
        } while ($this->pdoStatement->nextRowset());

        return $result;
    }

    /**
     * 设置最后执行 SQL.
     */
    protected function setLastSql(string $sql, bool $failed = false): void
    {
        $this->sql = ($failed ? '[FAILED] ' : '').$sql;
        if ($this->dispatch) {
            $this->dispatch->handle(
                IDatabase::SQL_EVENT,
                $this->normalizeSqlLogCategory($this->sql, $failed),
            );
        }
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
        $this->pdo(true)->exec($sql);
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
        $this->pdo(true)->exec($sql);
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
        $this->pdo(true)->exec($sql);
    }

    /**
     * 是否需要重连.
     */
    protected function needReconnect(PDOException $e): bool
    {
        if (!$e->errorInfo || !isset($e->errorInfo[1])) {
            return false;
        }

        // errorInfo[1] 表示某个驱动错误码，后期扩展需要优化
        // 可以在驱动重写这个方法
        return in_array((int) $e->errorInfo[1], [2006, 2013], true) &&
            $this->reconnectRetry <= self::RECONNECT_MAX;
    }

    /**
     * PDO 异常处理.
     *
     * @throws \Leevel\Database\DuplicateKeyException
     */
    protected function pdoException(PDOException $e): void
    {
        $message = $e->getMessage();

        // 模拟数据库 replace
        if (23000 === (int) $e->getCode() &&
            false !== strpos($message, 'Duplicate entry')) {
            throw new DuplicateKeyException($message);
        }

        throw $e;
    }

    /**
     * 初始化查询组件.
     */
    protected function initSelect(): void
    {
        $this->select = new Select($this);
    }
}
