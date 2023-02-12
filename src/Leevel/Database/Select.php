<?php

declare(strict_types=1);

namespace Leevel\Database;

use Leevel\Cache\ICache;
use Leevel\Support\Collection;
use Leevel\Support\Str\UnCamelize;

/**
 * 数据库查询器.
 *
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 10)                                                                                      根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                                                                时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                                                  时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                                                              重置查询条件.
 * @method static \Leevel\Database\Select comment(string $comment)                                                                                                   查询注释.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                                                     prefix 查询.
 * @method static \Leevel\Database\Select table(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols = '*')           添加一个要查询的表及其要查询的字段.
 * @method static string                  getAlias()                                                                                                                 获取表别名.
 * @method static \Leevel\Database\Select columns(array|string $cols = '*', ?string $table = null)                                                                   添加字段.
 * @method static \Leevel\Database\Select setColumns(array|string $cols = '*', ?string $table = null)                                                                设置字段.
 * @method static string                  raw(string $raw)                                                                                                           原生查询.
 * @method static \Leevel\Database\Select where(...$cond)                                                                                                            where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                                                          orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                                                      Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                                                    Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                                                       exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                                                    not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                                                     whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                                                  whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                                                        whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                                                     whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                                                          whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                                                       whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                                                        whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                                                     whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                                                        whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                                                         whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                                                       whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                                                        whereYear 查询条件.
 * @method static \Leevel\Database\Select bind(mixed $names, mixed $value = null, ?int $dataType = null)                                                             参数绑定支持.
 * @method static \Leevel\Database\Select forceIndex(array|string $indexs, string $type = 'FORCE')                                                                   index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex(array|string $indexs)                                                                                          index 忽略索引.
 * @method static \Leevel\Database\Select join(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)        join 查询.
 * @method static \Leevel\Database\Select innerJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)   innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)    leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)   rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)    fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond)   crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin(array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $table, array|string $cols, ...$cond) naturalJoin 查询.
 * @method static \Leevel\Database\Select union(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects, string $type = 'UNION')           添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll(array|callable|\Leevel\Database\Condition|\Leevel\Database\Select|string $selects)                                添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy(array|string $expression)                                                                                          指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                                                           添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                                                         orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                                                     having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                                                   having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                                                    havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                                                 havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                                                       havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                                                    havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                                                         havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                                                      havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                                                       havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                                                    havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                                                       havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                                                        havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                                                      havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                                                       havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy(array|string $expression, string $orderDefault = 'ASC')                                                            添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                                                        最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                                                        最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                                                                创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                                                    总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                                                            平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                                                            最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                                                            最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                                                            合计
 * @method static \Leevel\Database\Select one()                                                                                                                      指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                                                      指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                                                       查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                                                     limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                                                               排它锁 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select lockShare(bool $flag = true)                                                                                               共享锁 LOCK SHARE 查询.
 * @method static array                   getBindParams()                                                                                                            返回参数绑定.
 * @method static void                    resetBindParams(array $bindParams = [])                                                                                    重置参数绑定.
 * @method static void                    setBindParamsPrefix(string $bindParamsPrefix)                                                                              设置参数绑定前缀.
 * @method static \Leevel\Database\Select if(mixed $value = false)                                                                                                   条件语句 if.
 * @method static \Leevel\Database\Select elif(mixed $value = false)                                                                                                 条件语句 elif.
 * @method static \Leevel\Database\Select else()                                                                                                                     条件语句 else.
 * @method static \Leevel\Database\Select fi()                                                                                                                       条件语句 fi.
 * @method static \Leevel\Database\Select setFlowControl(bool $inFlowControl, bool $isFlowControlTrue)                                                               设置当前条件表达式状态.
 * @method static bool                    checkFlowControl()                                                                                                         验证一下条件表达式是否通过.
 */
class Select
{
    /**
     * 分页统计数量缓存后缀.
     *
     * - 分页统计数量缓存 KEY 需要加一个后缀与分页数据区分.
     */
    public const PAGE_COUNT_CACHE_SUFFIX = ':pagecount';

    /**
     * 数据库连接.
     */
    protected IDatabase $connect;

    /**
     * 查询条件.
     */
    protected Condition $condition;

    /**
     * 分页查询条件备份.
     */
    protected array $backupPage = [];

    /**
     * 不查询直接返回 SQL.
     */
    protected bool $onlyMakeSql = false;

    /**
     * 查询类型.
     *
     * - master: bool,false (读服务器),true (写服务器)
     * - master: int,其它去对应服务器连接 ID，\Leevel\Database\IDatabase::MASTER 表示主服务器
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
        'cache' => [null, null, null],
    ];

    /**
     * 查询类型.
     */
    protected array $queryParams = [];

    /**
     * 构造函数.
     *
     * - This class borrows heavily from the QeePHP Framework and is part of the QeePHP package.
     * - 查询器主体方法来自于早年 QeePHP 数据库查询 Api,这个 10 年前的作品设计理念非常先进.
     * - 在这个思想下大量进行了重构，在查询 API 用法上我们将一些与 Laravel 的用法习惯靠拢，实现了大量语法糖.
     * - 也支持 ThinkPHP 这种的数组方式传入查询，查询构造器非常复杂，为保证结果符合预期这里编写了大量的单元测试.
     *
     * @see http://qeephp.com
     * @see http://qeephp.cn/docs/qeephp-manual/
     */
    public function __construct(IDatabase $connect)
    {
        $this->connect = $connect;
        $this->condition = new Condition($connect);
        $this->initOption();
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \InvalidArgumentException
     */
    public function __call(string $method, array $args): mixed
    {
        try {
            $this->condition->{$method}(...$args);

            return $this;
        } catch (ConditionErrorException) {
            if (method_exists($this->connect, $method)
                && \is_callable([$this->connect, $method])) {
                return $this->connect->{$method}(...$args);
            }
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
                    $e = 'Params of findBy or findAllBy was not matched.';

                    throw new \InvalidArgumentException($e);
                }

                if (!$isKeep) {
                    $keys = array_map(function ($item) {
                        return UnCamelize::handle($item);
                    }, $keys);
                }

                $method = 'find'.($isOne ? 'One' : 'All');

                return $this
                    ->where(array_combine($keys, $args))
                    ->{$method}();
            }

            if (!ctype_digit($method)) {
                $e = sprintf('Select do not implement magic method `%s`.', $sourceMethod);

                throw new \InvalidArgumentException($e);
            }

            return $this
                ->top((int) $method)
                ->find()
            ;
        }

        $e = sprintf('Select do not implement magic method `%s`.', $method);

        throw new \InvalidArgumentException($e);
    }

    /**
     * 查询对象.
     */
    public function databaseCondition(): Condition
    {
        return $this->condition;
    }

    /**
     * 返回数据库连接对象.
     */
    public function databaseConnect(): IDatabase
    {
        return $this->connect;
    }

    /**
     * 设置是否查询主服务器.
     */
    public function master(bool|int $master = false): self
    {
        $this->queryParams['master'] = $master;

        return $this;
    }

    /**
     * 设置以某种包装返会结果.
     */
    public function asSome(?\Closure $asSome = null, array $args = []): self
    {
        $this->queryParams['as_some'] = $asSome;
        $this->queryParams['as_args'] = $args;

        return $this;
    }

    /**
     * 设置返会结果为数组.
     */
    public function asArray(?\Closure $asArray = null): self
    {
        $this->queryParams['as_some'] = fn (array $value): array => $asArray ? $asArray($value) : $value;
        $this->queryParams['as_args'] = [];

        return $this;
    }

    /**
     * 设置是否以集合返回.
     */
    public function asCollection(bool $asCollection = true): self
    {
        $this->queryParams['as_collection'] = $asCollection;

        return $this;
    }

    /**
     * 原生 SQL 查询数据.
     */
    public function select(null|callable|Select|string $data = null, array $bind = []): mixed
    {
        // 查询对象直接查询
        if ($data instanceof self) {
            return $data->find(null, $this->onlyMakeSql);
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

    /**
     * 插入数据 insert (支持原生 SQL).
     */
    public function insert(array|string $data, array $bind = [], bool|array $replace = false): null|array|int
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

    /**
     * 批量插入数据 insertAll.
     */
    public function insertAll(array $data, array $bind = [], bool|array $replace = false): null|array|int
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

    /**
     * 更新数据 update (支持原生 SQL).
     */
    public function update(array|string $data, array $bind = []): array|int
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

    /**
     * 更新某个字段的值.
     */
    public function updateColumn(string $column, mixed $value, array $bind = []): array|int
    {
        return $this->update([$column => $value], $bind);
    }

    /**
     * 字段递增.
     */
    public function updateIncrease(string $column, int $step = 1, array $bind = []): array|int
    {
        return $this->updateColumn($column, Condition::raw('['.$column.']+'.$step), $bind);
    }

    /**
     * 字段减少.
     */
    public function updateDecrease(string $column, int $step = 1, array $bind = []): array|int
    {
        return $this->updateColumn($column, Condition::raw('['.$column.']-'.$step), $bind);
    }

    /**
     * 删除数据 delete (支持原生 SQL).
     */
    public function delete(?string $data = null, array $bind = []): array|int
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

    /**
     * 清空表重置自增 ID.
     */
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

    /**
     * 返回一条记录.
     */
    public function findOne(): mixed
    {
        $this->condition->one();

        return $this
            ->query()
        ;
    }

    /**
     * 返回所有记录.
     */
    public function findAll(): mixed
    {
        $this->condition->all();

        return $this
            ->query()
        ;
    }

    /**
     * 返回最后几条记录.
     */
    public function find(?int $num = null): mixed
    {
        if (null !== $num) {
            $this->condition->top($num);
        }

        return $this
            ->query()
        ;
    }

    /**
     * 返回一个字段的值.
     */
    public function value(string $field): mixed
    {
        $this
            ->condition
            ->setColumns($field)
            ->one()
        ;

        $result = (array) $this

            ->asSome()
            ->query()
        ;

        if (true === $this->onlyMakeSql) {
            return $result;
        }

        return $result[$field] ?? null;
    }

    /**
     * 返回一列数据.
     */
    public function list(mixed $fieldValue, ?string $fieldKey = null): array
    {
        // 纵然有弱水三千，我也只取一瓢 (第一个字段为值，第二个字段为键值，多余的字段丢弃)
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
            ->findAll()
        ;

        if (true === $this->onlyMakeSql) {
            return $list;
        }

        // 解析结果
        $result = [];
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

    /**
     * 数据分块处理.
     */
    public function chunk(int $count, \Closure $chunk): void
    {
        $result = (array) $this
            ->forPage($page = 1, $count)
            ->findAll()
        ;

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

    /**
     * 数据分块处理依次回调.
     */
    public function each(int $count, \Closure $each): void
    {
        $this->chunk($count, function ($result, $page) use ($each) {
            foreach ($result as $key => $value) {
                if (false === $each($value, $key, $page)) {
                    return false;
                }
            }
        });
    }

    /**
     * 总记录数.
     */
    public function findCount(string $field = '*', string $alias = 'row_count'): array|int
    {
        $result = $this->findAggregateResult('count', $field, $alias);

        return \is_array($result) ? $result : (int) $result;
    }

    /**
     * 平均数.
     */
    public function findAvg(string $field, string $alias = 'avg_value'): mixed
    {
        return $this->findAggregateResult('avg', $field, $alias);
    }

    /**
     * 最大值.
     */
    public function findMax(string $field, string $alias = 'max_value'): mixed
    {
        return $this->findAggregateResult('max', $field, $alias);
    }

    /**
     * 最小值.
     */
    public function findMin(string $field, string $alias = 'min_value'): mixed
    {
        return $this->findAggregateResult('min', $field, $alias);
    }

    /**
     * 合计.
     */
    public function findSum(string $field, string $alias = 'sum_value'): mixed
    {
        return $this->findAggregateResult('sum', $field, $alias);
    }

    /**
     * 分页查询.
     *
     * - 可以渲染 HTML.
     */
    public function page(int $currentPage, int $perPage = 10, string $column = '*', array $option = []): Page
    {
        $page = new Page($currentPage, $perPage, $this->pageCount($column), $option);
        $data = $this
            ->limit($page->getFromRecord(), $perPage)
            ->findAll()
        ;
        $page->setData($data);

        return $page;
    }

    /**
     * 创建一个无限数据的分页查询.
     */
    public function pageMacro(int $currentPage, int $perPage = 10, array $option = []): Page
    {
        $page = new Page($currentPage, $perPage, Page::MACRO, $option);
        $data = $this
            ->limit($page->getFromRecord(), $perPage)
            ->findAll()
        ;
        $page->setData($data);

        return $page;
    }

    /**
     * 创建一个只有上下页的分页查询.
     */
    public function pagePrevNext(int $currentPage, int $perPage = 10, array $option = []): Page
    {
        $page = new Page($currentPage, $perPage, null, $option);
        $data = $this
            ->limit($page->getFromRecord(), $perPage)
            ->findAll()
        ;
        $page->setData($data);

        return $page;
    }

    /**
     * 取得分页查询记录数量.
     */
    public function pageCount(string $cols = '*'): int
    {
        $this->backupPageArgs();
        if (!empty($this->queryParams['cache'][0])) {
            $this->queryParams['cache'][0] .= self::PAGE_COUNT_CACHE_SUFFIX;
        }
        $count = $this->findCount($cols);
        $this->restorePageArgs();

        return $count;
    }

    /**
     * 获得查询字符串.
     */
    public function makeSql(bool $withLogicGroup = false): string
    {
        return $this->condition->makeSql($withLogicGroup);
    }

    /**
     * 设置查询缓存.
     */
    public function cache(string $name, ?int $expire = null, ?ICache $cache = null): self
    {
        $this->queryParams['cache'] = [$name, $expire, $cache];

        return $this;
    }

    /**
     * 安全格式指定返回 SQL 不做任何操作.
     */
    protected function safeSql(bool $flag = true): self
    {
        return $this;
        if (true === $this->onlyMakeSql) {
            return $this;
        }
        $this->onlyMakeSql = $flag;

        return $this;
    }

    /**
     * 初始化查询条件.
     */
    protected function initOption(): void
    {
        $this->queryParams = static::$queryParamsDefault;
    }

    /**
     * 查询获得结果.
     */
    protected function query(): mixed
    {
        $args = [
            $this->makeSql(),
            $this->condition->getBindParams(),
            $this->queryParams['master'],
        ];

        // 设置最近一次真实查询的 SQL 语句
        $newArgs = $args;
        $this->bindParamsTypeForHuman($newArgs[1]);
        $this->connect->setRealLastSql($newArgs);

        $data = $this->connect->query(...$args, ...$this->queryParams['cache']);
        if (null === $this->queryParams['as_some']) {
            $data = $this->queryDefault($data);
        } else {
            $data = $this->querySome($data);
        }

        $this->condition->resetBindParams();

        return $data;
    }

    protected function bindParamsTypeForHuman(array &$bindParams): void
    {
        foreach ($bindParams as &$v) {
            if (!\array_key_exists(1, $v)) {
                continue;
            }

            $v[1] = match ($v[1]) {
                \PDO::PARAM_INT => 'PDO::PARAM_INT',
                \PDO::PARAM_BOOL => 'PDO::PARAM_BOOL',
                \PDO::PARAM_NULL => 'PDO::PARAM_NULL',
                \PDO::PARAM_STR => 'PDO::PARAM_STR',
                default => 'PDO::PARAM_UNKNOWN',
            };
        }
    }

    /**
     * 以默认返回结果.
     */
    protected function queryDefault(array $data): mixed
    {
        if (!$this->condition->options['limitQuery']) {
            return reset($data) ?: [];
        }

        return $this->queryParams['as_collection'] ? new Collection($data, $this->parseSelectDataType($data)) : $data;
    }

    /**
     * 以某种包装返回结果.
     */
    protected function querySome(array $data): mixed
    {
        /** @var \Closure $asSome */
        $asSome = $this->queryParams['as_some'];
        foreach ($data as &$value) {
            $value = $asSome((array) $value, ...$this->queryParams['as_args']);
        }

        if (!$this->condition->options['limitQuery']) {
            $data = reset($data) ?: $asSome([], ...$this->queryParams['as_args']);
        } elseif ($this->queryParams['as_collection']) {
            $data = new Collection($data, $this->parseSelectDataType($data));
        }

        return $data;
    }

    /**
     * 分析查询数据类型.
     *
     * - 目前仅支持对象.
     */
    protected function parseSelectDataType(array $data): array
    {
        return ($value = array_pop($data)) && \is_object($value) ? [$value::class] : [];
    }

    /**
     * 获取聚合结果.
     */
    protected function findAggregateResult(string $method, string $field, string $alias): mixed
    {
        $this->condition->{$method}($field, $alias);

        $result = $this

            ->asSome()
            ->query()
        ;

        if (true === $this->onlyMakeSql) {
            return $result;
        }

        return \is_object($result) ? $result->{$alias} : $result[$alias];
    }

    /**
     * 原生 SQL 执行方法.
     */
    protected function runNativeSql(string $type, string $data, array $bindParams = []): mixed
    {
        $args = [$data, $bindParams, $this->queryParams['master']];

        // 只返回 SQL，不做任何实际操作
        if (true === $this->onlyMakeSql) {
            return $args;
        }

        if ('query' === $type) {
            $args = array_merge($args, $this->queryParams['cache']);
        }

        return $this->connect->{$type}(...$args);
    }

    /**
     * 备份分页查询条件.
     */
    protected function backupPageArgs(): void
    {
        $this->backupPage = [];
        $this->backupPage['query_params'] = $this->queryParams;
        $this->backupPage['aggregate'] = $this->condition->options['aggregate'];
        $this->backupPage['columns'] = $this->condition->options['columns'];
        $this->backupPage['bind_params'] = $this->condition->getBindParams();
    }

    /**
     * 恢复分页查询条件.
     */
    protected function restorePageArgs(): void
    {
        $this->queryParams = $this->backupPage['query_params'];
        $this->condition->options['aggregate'] = $this->backupPage['aggregate'];
        $this->condition->options['columns'] = $this->backupPage['columns'];
        $this->condition->resetBindParams($this->backupPage['bind_params']);
    }
}
