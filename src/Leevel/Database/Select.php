<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Support\Collection;
use Leevel\Support\Str\UnCamelize;

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
        if (\is_callable([$this->connect, $method])) {
            return $this->connect->{$method}(...$args);
        }

        // 动态查询支持
        if (0 === strncasecmp($method, 'find', 4)) {
            $sourceMethod = $method;
            $method = substr($method, 4);

            // support find10start3 etc.
            if (str_contains(strtolower($method), 'start')) {
                $values = explode('start', strtolower($method));
                $num = (int) array_shift($values);
                $offset = (int) array_shift($values);

                return $this->limit($offset, $num)->find();
            }

            // support findByName findByNameAndSex etc.
            // support findAllByNameAndSex etc.
            if (0 === strncasecmp($method, 'By', 2)
                || 0 === strncasecmp($method, 'AllBy', 5)) {
                $method = substr($method, ($isOne = 0 === strncasecmp($method, 'By', 2)) ? 2 : 5);
                $isKeep = false;
                if ('_' === substr($method, -1)) {
                    $isKeep = true;
                    $method = substr($method, 0, -1);
                }

                $keys = explode('And', $method);
                if (\count($keys) !== \count($args)) {
                    throw new \InvalidArgumentException('Params of findBy or findAllBy was not matched.');
                }

                if (!$isKeep) {
                    $keys = array_map(static function ($item) {
                        return UnCamelize::handle($item);
                    }, $keys);
                }

                $method = 'find'.($isOne ? 'One' : 'All');

                return $this
                    ->where(array_combine($keys, $args))
                    ->{$method}()
                ;
            }

            if (!ctype_digit($method)) {
                throw new \InvalidArgumentException(sprintf('Select do not implement magic method `%s`.', $sourceMethod));
            }

            return $this
                ->top((int) $method)
                ->find()
            ;
        }

        throw new \InvalidArgumentException(sprintf('Select do not implement magic method `%s`.', $method));
    }

    public function forPage(int $page, int $perPage = 10): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $page, $perPage);
    }

    public function time(string $type = 'date'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $type);
    }

    public function endTime(): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function reset(?string $config = null): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $config);
    }

    public function comment(string $comment): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $comment);
    }

    public function prefix(string $prefix): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $prefix);
    }

    public function table(array|\Closure|ICondition|ISelect|string $table, array|string $cols = '*'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols);
    }

    public function columns(array|string $cols = '*', ?string $table = null): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $cols, $table);
    }

    public function setColumns(array|string $cols = '*', ?string $table = null): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $cols, $table);
    }

    public function field(array|string $cols = '*', ?string $table = null): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $cols, $table);
    }

    public function middlewares(string ...$middlewares): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$middlewares);
    }

    public function where(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function orWhere(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereRaw(string $raw): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function orWhereRaw(string $raw): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function whereExists(mixed $exists): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $exists);
    }

    public function whereNotExists(mixed $exists): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $exists);
    }

    public function whereBetween(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotBetween(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNull(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotNull(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereIn(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotIn(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereLike(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereNotLike(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDate(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereDay(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereMonth(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function whereYear(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $names, $value, $dataType);
    }

    public function forceIndex(array|string $indexes, string $type = 'FORCE'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $indexes, $type);
    }

    public function ignoreIndex(array|string $indexes): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $indexes);
    }

    public function join(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function innerJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function leftJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function rightJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function fullJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function crossJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function naturalJoin(array|\Closure|ICondition|self|string $table, array|string $cols, mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $table, $cols, ...$cond);
    }

    public function union(array|callable|ICondition|self|string $selects, string $type = 'UNION'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $selects, $type);
    }

    public function unionAll(array|callable|ICondition|self|string $selects): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $selects);
    }

    public function groupBy(array|string $expression): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $expression);
    }

    public function having(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function orHaving(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingRaw(string $raw): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function orHavingRaw(string $raw): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $raw);
    }

    public function havingBetween(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotBetween(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNull(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotNull(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingIn(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotIn(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingLike(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingNotLike(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDate(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingDay(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingMonth(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function havingYear(mixed ...$cond): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, ...$cond);
    }

    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $expression, $orderDefault);
    }

    public function latest(string $field = 'create_at'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field);
    }

    public function oldest(string $field = 'create_at'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field);
    }

    public function distinct(bool $flag = true): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $flag);
    }

    public function count(string $field = '*', string $alias = 'row_count'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function avg(string $field, string $alias = 'avg_value'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function max(string $field, string $alias = 'max_value'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function min(string $field, string $alias = 'min_value'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function sum(string $field, string $alias = 'sum_value'): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $field, $alias);
    }

    public function one(): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function all(): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function top(int $count = 30): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $count);
    }

    public function limit(int $offset = 0, int $count = 0): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $offset, $count);
    }

    public function forUpdate(bool $flag = true): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $flag);
    }

    public function lockShare(bool $flag = true): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $flag);
    }

    public function resetBindParams(array $bindParams = []): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $bindParams);
    }

    public function setBindParamsPrefix(string $bindParamsPrefix): ISelect
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

    public function if(mixed $condition): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $condition);
    }

    public function elif(mixed $condition): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__, $condition);
    }

    public function else(): ISelect
    {
        return $this->callConditionMethodChain(__FUNCTION__);
    }

    public function fi(): ISelect
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

    public function master(bool $master = false): ISelect
    {
        $this->queryParams['master'] = $master;

        return $this;
    }

    public function asSome(?\Closure $asSome = null, array $args = []): ISelect
    {
        $this->queryParams['as_some'] = $asSome;
        $this->queryParams['as_args'] = $args;

        return $this;
    }

    public function asArray(?\Closure $asArray = null): ISelect
    {
        $this->queryParams['as_some'] = static fn (array $value): array => $asArray ? $asArray($value) : $value;
        $this->queryParams['as_args'] = [];
        $this->queryParams['as_collection_value_types'] = ['array'];

        return $this;
    }

    public function asCollection(bool $asCollection = true, array $valueTypes = []): ISelect
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

        return $this->queryOne();
    }

    public function findArray(): array
    {
        $this->condition->all();

        // @phpstan-ignore-next-line
        return $this->asCollection(false)->queryAll();
    }

    public function findAsArray(): array
    {
        $this->condition->all();

        // @phpstan-ignore-next-line
        return $this
            ->asCollection(false)
            ->asArray()
            ->queryAll()
        ;
    }

    public function findCollection(): Collection|EntityCollection
    {
        $this->condition->all();

        // @phpstan-ignore-next-line
        return $this->asCollection()->queryAll();
    }

    public function findAll(): array|Collection|EntityCollection
    {
        $this->condition->all();

        return $this->queryAll();
    }

    public function find(?int $num = null): array|Collection|EntityCollection
    {
        if (null !== $num) {
            $this->condition->top($num);
        }

        return $this->queryAll();
    }

    public function value(string $field): mixed
    {
        $this
            ->condition
            ->setColumns($field)
            ->one()
        ;

        $result = (array) $this
            ->asSome()
            ->queryOne()
        ;

        return $result[$field] ?? null;
    }

    public function list(mixed $fieldValue, ?string $fieldKey = null): array
    {
        // 纵然有弱水三千，我也只取一瓢
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

    public function findCount(string $field = '*', string $alias = 'row_count'): int
    {
        return (int) $this->findAggregateResult('count', $field, $alias);
    }

    public function findAvg(string $field, string $alias = 'avg_value'): mixed
    {
        return $this->findAggregateResult('avg', $field, $alias);
    }

    public function findMax(string $field, string $alias = 'max_value'): mixed
    {
        return $this->findAggregateResult('max', $field, $alias);
    }

    public function findMin(string $field, string $alias = 'min_value'): mixed
    {
        return $this->findAggregateResult('min', $field, $alias);
    }

    public function findSum(string $field, string $alias = 'sum_value'): mixed
    {
        return $this->findAggregateResult('sum', $field, $alias);
    }

    public function page(int $currentPage, int $perPage = 10, ?int $count = null, array $config = []): Page
    {
        $page = new Page($currentPage, $perPage, $count, $config);
        $data = $this
            ->limit($page->getFromRecord(), $perPage)
            ->findAll()
        ;
        $page->setData($data);

        return $page;
    }

    public function pageMacro(int $currentPage, int $perPage = 10, array $config = []): Page
    {
        return $this->page($currentPage, $perPage, Page::MACRO, $config);
    }

    public function pagePrevNext(int $currentPage, int $perPage = 10, array $config = []): Page
    {
        return $this->page($currentPage, $perPage, null, $config);
    }

    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): ISelect
    {
        $this->queryParams['cache'] = [$name, $expire, $cache];

        return $this;
    }

    public function queryOne(): mixed
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

    public function queryAll(): array|Collection|EntityCollection
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

    protected function callConditionMethodChain(string $method, mixed ...$args): ISelect
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
    protected function findAggregateResult(string $method, string $field, string $alias): mixed
    {
        $this->condition->{$method}($field, $alias);

        $result = $this
            ->asSome()
            ->queryOne()
        ;

        return \is_object($result) ? ($result->{$alias} ?? 0) : ($result[$alias] ?? 0);
    }

    /**
     * 原生 SQL 执行方法.
     */
    protected function runNativeSql(string $type, string $data, array $bindParams = [], bool $insert = false): mixed
    {
        $args = [$data, $bindParams, $this->queryParams['master']];
        $this->setRealLastSql($args);
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
    protected function setRealLastSql(array $args): void
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

        $this->setRealLastSql($args);

        return (array) $this->connect->query(...$args, ...$this->queryParams['cache']);
    }
}
