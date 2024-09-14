<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Di\IContainer;
use Leevel\Server\Pool\Pool as BasePool;
use Leevel\Support\Collection;

/**
 * 数据库查询器.
 */
class Select implements ISelect
{
    /**
     * 数据库连接.
     */
    protected IDatabase $connect;

    /**
     * 查询条件.
     */
    protected Condition $condition;

    /**
     * 查询类型.
     *
     * - master: false=读服务器,true=写服务器
     * - as_some: 每一项记录以某种包装返回，null 表示默认返回
     * - as_args: 包装附加参数
     * - as_collection: 以对象集合方法返回
     * - cache: 查询缓存参数, 分别对应 $name 缓存名字,expire 缓存过期时间和 cache 缓存驱动
     */
    protected static array $queryParamsDefault = [
        'master' => false,
        'as_some' => null,
        'as_args' => [],
        'as_collection' => false,
        'as_collection_value_types' => [],
        'cache' => [null, null, null],
    ];

    /**
     * 查询类型.
     */
    protected array $queryParams = [];

    /**
     * 构造函数.
     *
     * - 查询器主体方法参考自早年 QeePHP 数据库查询 Api.
     * - 在这个思想下大量进行了重构，在查询 API 用法上我们将一些与 Laravel 的用法习惯靠拢，实现了大量语法糖.
     * - 也支持 ThinkPHP 这种的数组方式传入查询，查询构造器非常复杂，为保证结果符合预期这里编写了大量的单元测试.
     */
    public function __construct(IDatabase $connect)
    {
        $this->connect = $connect;
        $this->condition = new Condition($connect);
        $this->initConfig();
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \InvalidArgumentException
     */
    public function __call(string $method, array $args): mixed
    {
        throw new \InvalidArgumentException(sprintf('Select do not implement magic method `%s`.', $method));
    }

    public function getContainer(): IContainer
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__);
    }

    public function setCache(?ICache $cache): void
    {
        $this->callDatabaseMethod(__FUNCTION__, $cache);
    }

    public function getCache(): ?ICache
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__);
    }

    public function databaseSelect(): self
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__);
    }

    public function getPdo(bool $master = false): \PDO
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $master);
    }

    public function query(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): mixed
    {
        return $this->callDatabaseMethod(__FUNCTION__, $sql, $bindParams, $master, $cacheName, $cacheExpire, $cache);
    }

    public function procedure(string $sql, array $bindParams = [], bool $master = false, ?string $cacheName = null, ?int $cacheExpire = null, ?ICache $cache = null): array
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $sql, $bindParams, $master, $cacheName, $cacheExpire, $cache);
    }

    public function execute(string $sql, array $bindParams = [], bool $master = false, bool $insert = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $sql, $bindParams, $master, $insert);
    }

    public function cursor(string $sql, array $bindParams = [], bool $master = false): \Generator
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $sql, $bindParams, $master);
    }

    public function prepare(string $sql, array $bindParams = [], bool $master = false): \PDOStatement
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $sql, $bindParams, $master);
    }

    public function transaction(\Closure $action): mixed
    {
        return $this->callDatabaseMethod(__FUNCTION__, $action);
    }

    public function beginTransaction(): void
    {
        $this->callDatabaseMethod(__FUNCTION__);
    }

    public function inTransaction(): bool
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__);
    }

    public function commit(): void
    {
        $this->callDatabaseMethod(__FUNCTION__);
    }

    public function rollBack(): void
    {
        $this->callDatabaseMethod(__FUNCTION__);
    }

    public function setSavepoints(bool $savepoints): void
    {
        $this->callDatabaseMethod(__FUNCTION__, $savepoints);
    }

    public function hasSavepoints(): bool
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__);
    }

    public function getLastSql(): string
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__);
    }

    public function setRealLastSql(array $realLastSql): void
    {
        $this->callDatabaseMethod(__FUNCTION__, $realLastSql);
    }

    public function getRealLastSql(): array
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__);
    }

    public function close(): void
    {
        $this->callDatabaseMethod(__FUNCTION__);
    }

    public static function getRawSql(string $sql, array $bindParams): string
    {
        return Database::getRawSql($sql, $bindParams);
    }

    public function parseDsn(array $config): string
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $config);
    }

    public function getTableNames(string $dbName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $dbName, $master);
    }

    public function getTableColumns(string $tableName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $tableName, $master);
    }

    public function getUniqueIndex(string $tableName, bool $master = false): array
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $tableName, $master);
    }

    public function identifierColumn(string $name): string
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $name);
    }

    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        // @phpstan-ignore-next-line
        return $this->callDatabaseMethod(__FUNCTION__, $limitCount, $limitOffset);
    }

    public function release(): void
    {
        $this->callDatabaseMethod(__FUNCTION__);
    }

    public function setPool(BasePool $pool): void
    {
        $this->callDatabaseMethod(__FUNCTION__, $pool);
    }

    public function forPage(int $page, int $perPage = 10): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $page, $perPage);
    }

    public function time(string $type = 'date'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $type);
    }

    public function endTime(): self
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function reset(?string $config = null): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $config);
    }

    public function comment(string $comment): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $comment);
    }

    public function prefix(string $prefix): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $prefix);
    }

    public function table(array|\Closure|ICondition|ISelect|string $table, array|string $cols = '*'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols);
    }

    public function columns(array|string $cols = '*', ?string $table = null): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $cols, $table);
    }

    public function setColumns(array|string $cols = '*', ?string $table = null): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $cols, $table);
    }

    public function field(array|string $cols = '*', ?string $table = null): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $cols, $table);
    }

    public function middlewares(string ...$middlewares): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$middlewares);
    }

    public function where(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function orWhere(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereRaw(string $raw): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function orWhereRaw(string $raw): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function whereExists(mixed $exists): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $exists);
    }

    public function whereNotExists(mixed $exists): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $exists);
    }

    public function whereBetween(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotBetween(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNull(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotNull(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereIn(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotIn(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereLike(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotLike(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDate(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDay(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereMonth(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereYear(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $names, $value, $dataType);
    }

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $indexes, $type);
    }

    public function ignoreIndex(array|string $indexes): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $indexes);
    }

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $selects, $type);
    }

    public function unionAll(array|callable|ICondition|self|string $selects): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $selects);
    }

    public function groupBy(array|string $expression): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $expression);
    }

    public function having(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function orHaving(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingRaw(string $raw): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function orHavingRaw(string $raw): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function havingBetween(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotBetween(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNull(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotNull(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingIn(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotIn(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingLike(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotLike(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDate(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDay(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingMonth(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingYear(mixed ...$cond): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $expression, $orderDefault);
    }

    public function latest(string $field = 'create_at'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field);
    }

    public function oldest(string $field = 'create_at'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field);
    }

    public function distinct(bool $flag = true): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $flag);
    }

    public function count(string $field = '*', string $alias = 'row_count'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function avg(string $field, string $alias = 'avg_value'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function max(string $field, string $alias = 'max_value'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function min(string $field, string $alias = 'min_value'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function sum(string $field, string $alias = 'sum_value'): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function one(): self
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function all(): self
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function top(int $count = 30): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $count);
    }

    public function limit(int $offset = 0, int $count = 0): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $offset, $count);
    }

    public function forUpdate(bool $flag = true): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $flag);
    }

    public function lockShare(bool $flag = true): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $flag);
    }

    public function resetBindParams(array $bindParams = []): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $bindParams);
    }

    public function setBindParamsPrefix(string $bindParamsPrefix): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $bindParamsPrefix);
    }

    public function getAlias(): string
    {
        // @phpstan-ignore-next-line
        return $this->callConditionMethod(__FUNCTION__);
    }

    public static function raw(string $raw): string
    {
        return Condition::raw($raw);
    }

    public function makeSql(bool $withLogicGroup = false): string
    {
        // @phpstan-ignore-next-line
        return $this->callConditionMethod(__FUNCTION__, $withLogicGroup);
    }

    public function getBindParams(): array
    {
        // @phpstan-ignore-next-line
        return $this->callConditionMethod(__FUNCTION__);
    }

    public function if(mixed $condition): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $condition);
    }

    public function elif(mixed $condition): self
    {
        return $this->callConditionMethodChain(__FUNCTION__, $condition);
    }

    public function else(): self
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function fi(): self
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function databaseCondition(): Condition
    {
        return $this->condition;
    }

    public function databaseConnect(): IDatabase
    {
        return $this->connect;
    }

    public function master(bool $master = false): self
    {
        $this->queryParams['master'] = $master;

        return $this;
    }

    public function asSome(?\Closure $asSome = null, array $args = []): self
    {
        $this->queryParams['as_some'] = $asSome;
        $this->queryParams['as_args'] = $args;

        return $this;
    }

    public function asArray(?\Closure $asArray = null): self
    {
        $this->queryParams['as_some'] = static fn (array $value): array => $asArray ? $asArray($value) : $value;
        $this->queryParams['as_args'] = [];
        $this->queryParams['as_collection_value_types'] = ['array'];

        return $this;
    }

    public function asCollection(bool $asCollection = true, array $valueTypes = []): self
    {
        $this->queryParams['as_collection'] = $asCollection;
        $this->queryParams['as_collection_value_types'] = $asCollection ? $valueTypes : [];

        return $this;
    }

    public function select(null|callable|ISelect|string $data = null, array $bind = []): mixed
    {
        // 查询对象直接查询
        if ($data instanceof self) {
            return $data->find();
        }

        // 回调
        if ($data instanceof \Closure) {
            $data($this);
            $data = null;
        }

        // 调用查询
        if (null === $data) {
            return $this->find();
        }

        return $this->runNativeSql(...['query', $data, $bind]);
    }

    public function insert(array|string $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this
            ->runNativeSql(
                ...$this
                    ->condition
                    ->insert($data, $bind, $replace)
            )
        ;
    }

    public function insertAll(array $data, array $bind = [], array|bool $replace = false): int|string
    {
        // @phpstan-ignore-next-line
        return $this
            ->runNativeSql(
                ...$this
                    ->condition
                    ->insertAll($data, $bind, $replace)
            )
        ;
    }

    public function update(array|string $data, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this
            ->runNativeSql(
                ...$this
                    ->condition
                    ->update($data, $bind)
            )
        ;
    }

    public function updateColumn(string $column, mixed $value, array $bind = []): int
    {
        return $this->update([$column => $value], $bind);
    }

    public function updateIncrease(string $column, int $step = 1, array $bind = []): int
    {
        return $this->updateColumn($column, Condition::raw('['.$column.']+'.$step), $bind);
    }

    public function updateDecrease(string $column, int $step = 1, array $bind = []): int
    {
        return $this->updateColumn($column, Condition::raw('['.$column.']-'.$step), $bind);
    }

    public function delete(?string $data = null, array $bind = []): int
    {
        // @phpstan-ignore-next-line
        return $this
            ->runNativeSql(
                ...$this
                    ->condition
                    ->delete($data, $bind)
            )
        ;
    }

    public function truncate(): array|int
    {
        // @phpstan-ignore-next-line
        return $this
            ->runNativeSql(
                ...$this
                    ->condition
                    ->truncate()
            )
        ;
    }

    public function findOne(): mixed
    {
        $this->condition->one();

        return $this->queryOneResult();
    }

    public function findArray(): array
    {
        $this->condition->all();

        // @phpstan-ignore-next-line
        return $this
            ->asCollection(false)
            ->queryAllResult()
        ;
    }

    public function findAsArray(): array
    {
        $this->condition->all();

        // @phpstan-ignore-next-line
        return $this
            ->asCollection(false)
            ->asArray()
            ->queryAllResult()
        ;
    }

    public function findCollection(): Collection|EntityCollection
    {
        $this->condition->all();

        // @phpstan-ignore-next-line
        return $this->asCollection()->queryAllResult();
    }

    public function findAll(?\Closure $condition = null): array|Collection|EntityCollection
    {
        $this->condition->all();

        if ($condition) {
            $condition($this);
        }

        return $this->queryAllResult();
    }

    public function find(?int $num = null): array|Collection|EntityCollection
    {
        if (null !== $num) {
            $this->condition->top($num);
        }

        return $this->queryAllResult();
    }

    public function value(string $field, ?\Closure $condition = null): mixed
    {
        $this
            ->condition
            ->setColumns($field)
            ->one()
        ;

        if ($condition) {
            $condition($this);
        }

        $result = (array) $this
            ->asSome()
            ->queryOneResult()
        ;

        return $result[$field] ?? null;
    }

    public function findList(mixed $fieldValue, ?string $fieldKey = null, ?\Closure $condition = null): array
    {
        // 第一个字段为值，第二个字段为键值，多余的字段丢弃
        $fields = [];
        if (\is_array($fieldValue)) {
            $fields = $fieldValue;
        } else {
            $fields[] = $fieldValue;
        }

        if ($fieldKey) {
            $fields[] = $fieldKey;
        }

        $this->condition->setColumns($fields);

        if ($condition) {
            $condition($this);
        }

        $list = $this
            ->asSome()
            ->asCollection(false)
            ->findAll()
        ;

        // 解析结果
        $result = [];
        // @phpstan-ignore-next-line
        foreach ($list as $v) {
            $v = (array) $v;
            if (1 === \count($v)) {
                $result[] = reset($v);
            } else {
                $value = array_shift($v);
                $key = array_shift($v);
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function chunk(int $count, \Closure $chunk): void
    {
        $result = (array) $this
            ->forPage($page = 1, $count)
            ->findAll()
        ;

        // @phpstan-ignore-next-line
        while (\count($result) > 0) {
            if (false === $chunk($result, $page)) {
                break;
            }

            ++$page;
            $result = $this
                ->forPage($page, $count)
                ->findAll()
            ;
        }
    }

    public function each(int $count, \Closure $each): void
    {
        $this->chunk($count, static function ($result, $page) use ($each) {
            foreach ($result as $key => $value) {
                if (false === $each($value, $key, $page)) {
                    return false;
                }
            }
        });
    }

    public function findCount(string $field = '*', ?\Closure $condition = null): int
    {
        return (int) $this->findAggregateResult('count', $field, 'row_count', $condition);
    }

    public function findAvg(string $field, ?\Closure $condition = null): mixed
    {
        return $this->findAggregateResult('avg', $field, 'avg_value', $condition);
    }

    public function findMax(string $field, ?\Closure $condition = null): mixed
    {
        return $this->findAggregateResult('max', $field, 'max_value', $condition);
    }

    public function findMin(string $field, ?\Closure $condition = null): mixed
    {
        return $this->findAggregateResult('min', $field, 'min_value', $condition);
    }

    public function findSum(string $field, ?\Closure $condition = null): mixed
    {
        return $this->findAggregateResult('sum', $field, 'sum_value', $condition);
    }

    public function findPageList(int $currentPage, int $perPage = 10, ?int $count = null, array $config = [], ?\Closure $condition = null): Page
    {
        $page = new Page($currentPage, $perPage, $count, $config);

        if ($condition) {
            $condition($this);
        }

        $data = $this
            ->limit($page->getFromRecord(), $perPage)
            ->findAll()
        ;
        $page->setData($data);

        return $page;
    }

    public function findPageMacro(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        return $this->findPageList($currentPage, $perPage, Page::MACRO, $config, $condition);
    }

    public function findPagePrevNext(int $currentPage, int $perPage = 10, array $config = [], ?\Closure $condition = null): Page
    {
        return $this->findPageList($currentPage, $perPage, null, $config, $condition);
    }

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): self
    {
        $this->queryParams['cache'] = [$name, $expire, $cache];

        return $this;
    }

    /**
     * 查询单条记录获得结果.
     */
    protected function queryOneResult(): mixed
    {
        $data = $this->queryBaseData();
        if (null === $this->queryParams['as_some']) {
            $data = $this->queryDefaultOne($data);
        } else {
            $data = $this->querySomeOne($data);
        }

        $this->condition->resetBindParams();

        return $data;
    }

    /**
     * 查询单条记录获得结果.
     */
    protected function queryAllResult(): array|Collection|EntityCollection
    {
        $data = $this->queryBaseData();
        if (null === $this->queryParams['as_some']) {
            $data = $this->queryDefaultAll($data);
        } else {
            $data = $this->querySomeAll($data);
        }

        $this->condition->resetBindParams();

        return $data;
    }

    protected function callDatabaseMethod(string $method, mixed ...$args): mixed
    {
        return $this->connect->{$method}(...$args);
    }

    protected function callConditionMethodChain(string $method, mixed ...$args): self
    {
        $this->condition->{$method}(...$args);

        return $this;
    }

    protected function callConditionMethod(string $method, mixed ...$args): mixed
    {
        return $this->condition->{$method}(...$args);
    }

    /**
     * 初始化查询条件.
     */
    protected function initConfig(): void
    {
        $this->queryParams = static::$queryParamsDefault;
    }

    protected function bindParamsTypeForHuman(array &$bindParams): void
    {
        foreach ($bindParams as &$v) {
            if (!\is_array($v) || !\array_key_exists(1, $v)) {
                continue;
            }

            $v[1] = match ($v[1]) {
                \PDO::PARAM_INT => 'PDO::PARAM_INT',
                \PDO::PARAM_BOOL => 'PDO::PARAM_BOOL',
                \PDO::PARAM_NULL => 'PDO::PARAM_NULL',
                \PDO::PARAM_STR => 'PDO::PARAM_STR',
                \PDO::PARAM_LOB => 'PDO::PARAM_LOB',
                \PDO::PARAM_STR | \PDO::PARAM_STR_NATL => 'PDO::PARAM_STR | PDO::PARAM_STR_NATL',
                \PDO::PARAM_STR | \PDO::PARAM_STR_CHAR => 'PDO::PARAM_STR | PDO::PARAM_STR_CHAR',
                default => 'PDO::PARAM_UNKNOWN',
            };
        }
    }

    /**
     * 单条记录以默认返回结果.
     */
    protected function queryDefaultOne(array $data): mixed
    {
        return reset($data) ?: [];
    }

    /**
     * 多条记录以默认返回结果.
     */
    protected function queryDefaultAll(array $data): array|Collection|EntityCollection
    {
        return $this->queryParams['as_collection'] ? $this->queryResultAsCollection($data) : $data;
    }

    /**
     * 单条记录以某种包装返回结果.
     */
    protected function querySomeOne(array $data): mixed
    {
        /** @var \Closure $asSome */
        $asSome = $this->queryParams['as_some'];
        $data = reset($data) ?: [];

        return $asSome((array) $data, ...$this->queryParams['as_args']);
    }

    /**
     * 多条记录以某种包装返回结果.
     */
    protected function querySomeAll(array $data): array|Collection|EntityCollection
    {
        /** @var \Closure $asSome */
        $asSome = $this->queryParams['as_some'];
        foreach ($data as &$value) {
            $value = $asSome((array) $value, ...$this->queryParams['as_args']);
        }

        if ($this->queryParams['as_collection']) {
            $data = $this->queryResultAsCollection($data);
        }

        return $data;
    }

    /**
     * 获取聚合结果.
     */
    protected function findAggregateResult(string $method, string $field, string $alias, ?\Closure $condition = null): mixed
    {
        $this->condition->{$method}($field, $alias);

        if ($condition) {
            $condition($this);
        }

        $result = $this
            ->asSome()
            ->queryOneResult()
        ;

        return \is_object($result) ? ($result->{$alias} ?? 0) : ($result[$alias] ?? 0);
    }

    /**
     * 原生 SQL 执行方法.
     */
    protected function runNativeSql(string $type, string $data, array $bindParams = [], bool $insert = false): mixed
    {
        $args = [$data, $bindParams, $this->queryParams['master']];
        $this->setDatabaseRealLastSql($args);
        if (\in_array($type, ['query', 'procedure'], true)) {
            $args = array_merge($args, $this->queryParams['cache']);
        } elseif ('execute' === $type) {
            $args[] = $insert;
        }

        return $this->connect->{$type}(...$args);
    }

    /**
     *  设置最近一次真实查询的 SQL 语句.
     */
    protected function setDatabaseRealLastSql(array $args): void
    {
        $newArgs = $args;
        $this->bindParamsTypeForHuman($newArgs[1]);
        $this->connect->setRealLastSql($newArgs);
    }

    /**
     * 查询对象转集合.
     *
     * @throws \InvalidArgumentException
     */
    protected function queryResultAsCollection(array $data): Collection|EntityCollection
    {
        // 处理一下特殊实体集合问题
        if (isset($this->queryParams['as_collection_value_types'][0])) {
            $collectionValueTypes = $this->queryParams['as_collection_value_types'][0];
            if (!$collectionValueTypes || !\is_string($collectionValueTypes) || !class_exists($collectionValueTypes)) {
                throw new \InvalidArgumentException('Invalid collection value types.');
            }

            if (is_subclass_of($collectionValueTypes, Entity::class)) {
                return new EntityCollection($data, $this->queryParams['as_collection_value_types'][0]);
            }

            return new Collection($data, $this->queryParams['as_collection_value_types']);
        }

        // 根据值处理
        $firstValue = $data[0] ?? false;
        if ($firstValue && \is_object($firstValue)) {
            if ($firstValue instanceof Entity) {
                $data = new EntityCollection($data, $firstValue::class);
            } else {
                $data = new Collection($data, [$firstValue::class]);
            }
        } else {
            $data = new Collection($data);
        }

        return $data;
    }

    protected function queryBaseData(): array
    {
        $args = [
            $this->makeSql(),
            $this->condition->getBindParams(),
            $this->queryParams['master'],
        ];

        $this->setDatabaseRealLastSql($args);

        return (array) $this->connect->query(...$args, ...$this->queryParams['cache']);
    }
}
