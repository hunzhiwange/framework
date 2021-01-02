<?php

declare(strict_types=1);

namespace Leevel\Database;

use Closure;
use InvalidArgumentException;
use Leevel\Flow\FlowControl;
use function Leevel\Support\Arr\normalize;
use Leevel\Support\Arr\normalize;
use RuntimeException;

/**
 * 条件构造器.
 */
class Condition
{
    use FlowControl;

    /**
     * And 逻辑运算符.
    */
    public const LOGIC_AND = 'and';

    /**
     * Or 逻辑运算符.
    */
    public const LOGIC_OR = 'or';

    /**
     * 原生查询左标识符.
    */
    public const RAW_LEFT = '{';

    /**
     * 原生查询右标识符.
    */
    public const RAW_RIGHT = '}';

    /**
     * 子表达式默认别名.
    */
    public const DEFAULT_SUBEXPRESSION_ALIAS = 'a';

    /**
     * 查询条件参数.
     */
    public array $options = [];

    /**
     * 条件逻辑连接符.
    */
    protected string $conditionLogic = 'and';

    /**
     * 数据库连接.
     */
    protected IDatabase $connect;

    /**
     * 绑定参数.
     */
    protected array $bindParams = [];

    /**
     * 支持的聚合类型.
     */
    protected static array $aggregateTypes = [
        'COUNT' => 'COUNT',
        'MAX'   => 'MAX',
        'MIN'   => 'MIN',
        'AVG'   => 'AVG',
        'SUM'   => 'SUM',
    ];

    /**
     * 支持的 union 类型.
     */
    protected static array $unionTypes = [
        'UNION'     => 'UNION',
        'UNION ALL' => 'UNION ALL',
    ];

    /**
     * 支持的 index 类型.
     */
    protected static array $indexTypes = [
        'FORCE'  => 'FORCE',
        'IGNORE' => 'IGNORE',
    ];

    /**
     * 连接参数.
     */
    protected static array $optionsDefault = [
        'comment'     => null,
        'prefix'      => [],
        'distinct'    => false,
        'columns'     => [],
        'aggregate'   => [],
        'union'       => [],
        'from'        => [],
        'index'       => [],
        'where'       => null,
        'group'       => [],
        'having'      => null,
        'order'       => [],
        'limitCount'  => null,
        'limitOffset' => null,
        'limitQuery'  => true,
        'forUpdate'   => false,
        'lockShare'   => false,
    ];

    /**
     * 条件逻辑类型.
    */
    protected string $conditionType = 'where';

    /**
     * 当前表信息.
    */
    protected string $table = '';

    /**
     * 是否为表操作.
    */
    protected bool $isTable = false;

    /**
     * 主表别名.
    */
    protected string $alias = '';

    /**
     * 是否处于时间功能状态.
    */
    protected ?string $inTimeCondition = null;

    /**
     * 绑定参数缓存.
     */
    protected array $bindParamsCache = [];

    /**
     * 参数绑定前缀.
    */
    protected string $bindParamsPrefix = '';

    /**
     * 构造函数.
     */
    public function __construct(IDatabase $connect)
    {
        $this->connect = $connect;
        $this->initOption();
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \Leevel\Database\ConditionErrorException
     */
    public function __call(string $method, array $args): mixed
    {
        $e = sprintf('Condition method %s not found.', $method);

        throw new ConditionErrorException($e);
    }

    /**
     * 插入数据 insert (支持原生 SQL).
     */
    public function insert(array|string $data, array $bind = [], bool $replace = false): array
    {
        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据插入
        if (is_array($data)) {
            $pdoPositionalParameterIndex = 0;
            list($fields, $values, $bind) = $this->normalizeBindData($data, $bind, $pdoPositionalParameterIndex);
            $tableName = $this->getTable();

            foreach ($fields as $key => $field) {
                $fields[$key] = $this->normalizeColumn($field, $tableName);
            }

            // 构造 insert 语句
            $sql = [];
            $sql[] = ($replace ? 'REPLACE' : 'INSERT').' INTO';
            $sql[] = $this->parseTable();
            $sql[] = '('.implode(',', $fields).')';
            $sql[] = 'VALUES';
            $sql[] = '('.implode(',', $values).')';
            $data = implode(' ', $sql);
        }

        $bind = array_merge($this->getBindParams(), $bind);

        return ['execute', $data, $bind];
    }

    /**
     * 批量插入数据 insertAll.
     *
     * @throws \InvalidArgumentException
     */
    public function insertAll(array $data, array $bind = [], bool $replace = false): array
    {
        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据批量插入
        $dataResult = $fields = [];
        $tableName = $this->getTable();
        $pdoPositionalParameterIndex = 0;
        foreach ($data as $key => $tmp) {
            if (!is_array($tmp) || count($tmp) !== count($tmp, 1)) {
                $e = 'Data for insertAll is not invalid.';

                throw new InvalidArgumentException($e);
            }

            list($tmpFields, $values, $bind) = $this->normalizeBindData($tmp, $bind, $pdoPositionalParameterIndex, $key);

            if (0 === $key) {
                $fields = $tmpFields;
                foreach ($fields as $fieldKey => $field) {
                    $fields[$fieldKey] = $this->normalizeColumn($field, $tableName);
                }
            }

            $dataResult[] = '('.implode(',', $values).')';
        }

        // 构造 insertAll 语句
        $sql = [];
        $sql[] = ($replace ? 'REPLACE' : 'INSERT').' INTO';
        $sql[] = $this->parseTable();
        $sql[] = '('.implode(',', $fields).')';
        $sql[] = 'VALUES';
        $sql[] = implode(',', $dataResult);
        $data = implode(' ', $sql);
        $bind = array_merge($this->getBindParams(), $bind);

        return ['execute', $data, $bind];
    }

    /**
     * 更新数据 update (支持原生 SQL).
     *
     * @throws \InvalidArgumentException
     */
    public function update(array|string $data, array $bind = []): array
    {
        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据更新
        if (is_array($data)) {
            $pdoPositionalParameterIndex = 0;
            list($fields, $values, $bind) = $this->normalizeBindData($data, $bind, $pdoPositionalParameterIndex);
            $tableName = $this->getTable();

            // SET 语句
            $setData = [];
            foreach ($fields as $key => $field) {
                $field = $this->normalizeColumn($field, $tableName);
                $setData[] = $field.' = '.$values[$key];
            }

            // 构造 update 语句
            if (!$values) {
                $e = 'Data for update can not be empty.';

                throw new InvalidArgumentException($e);
            }

            $sql = [];
            $sql[] = 'UPDATE';
            $sql[] = ltrim($this->parseFrom(), 'FROM ');
            $sql[] = 'SET '.implode(',', $setData);
            $sql[] = $this->parseWhere();
            $sql[] = $this->parseOrder();
            $sql[] = $this->parseLimitCount(true);
            $data = implode(' ', array_filter($sql));
        }

        $bind = array_merge($this->getBindParams(), $bind);

        return ['execute', $data, $bind];
    }

    /**
     * 删除数据 delete (支持原生 SQL).
     */
    public function delete(?string $data = null, array $bind = []): array
    {
        // 构造数据删除
        if (null === $data) {
            // 构造 delete 语句
            $sql = [];
            $sql[] = 'DELETE';
            if (1 === count($this->options['from'])) {
                $sql[] = 'FROM';
                $sql[] = $this->parseTable();
                $sql[] = $this->parseWhere();
                $sql[] = $this->parseOrder();
                $sql[] = $this->parseLimitCount(true);
            } else {
                $sql[] = $this->parseTable();
                $sql[] = $this->parseFrom();
                $sql[] = $this->parseWhere();
            }
            $data = implode(' ', array_filter($sql));
        }

        $bind = array_merge($this->getBindParams(), $bind);

        return ['execute', $data, $bind];
    }

    /**
     * 清空表重置自增 ID.
     */
    public function truncate(): array
    {
        $sql = [];
        $sql[] = 'TRUNCATE TABLE';
        $sql[] = $this->parseTable();
        $sql = implode(' ', $sql);

        return ['execute', $sql];
    }

    /**
     * 根据分页设置条件.
     */
    public function forPage(int $page, int $perPage = 10): self
    {
        return $this->limit(($page - 1) * $perPage, $perPage);
    }

    /**
     * 时间控制语句开始.
     *
     * @throws \InvalidArgumentException
     */
    public function time(string $type = 'date'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (!in_array($type, ['date', 'month', 'day', 'year'], true)) {
            $e = sprintf('Time type `%s` is invalid.', $type);

            throw new InvalidArgumentException($e);
        }

        $this->setInTimeCondition($type);

        return $this;
    }

    /**
     * 时间控制语句结束.
     */
    public function endTime(): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * 重置查询条件.
     */
    public function reset(?string $option = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $option) {
            $this->initOption();
        } elseif (array_key_exists($option, static::$optionsDefault)) {
            $this->options[$option] = static::$optionsDefault[$option];
        }

        return $this;
    }

    /**
     * 查询注释.
     */
    public function comment(string $comment): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->options['comment'] = $comment;

        return $this;
    }

    /**
     * prefix 查询.
     */
    public function prefix(string $prefix): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->options['prefix'][] = $prefix;

        return $this;
    }

    /**
     * 添加一个要查询的表及其要查询的字段.
     */
    public function table(array|Closure|Condition|Select|string $table, array|string $cols = '*'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setIsTable(true);
        $this->addJoin('inner join', $table, $cols);
        $this->setIsTable(false);

        return $this;
    }

    /**
     * 获取表别名.
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * 添加字段.
     */
    public function columns(array|string $cols = '*', ?string $table = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $table) {
            $table = $this->getTable();
        }

        $this->addCols($table, $cols);

        return $this;
    }

    /**
     * 设置字段.
     */
    public function setColumns(array|string $cols = '*', ?string $table = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $table) {
            $table = $this->getTable();
        }

        $this->options['columns'] = [];
        $this->addCols($table, $cols);

        return $this;
    }

    /**
     * 原生查询.
     */
    public static function raw(string $raw): string
    {
        return static::RAW_LEFT.$raw.static::RAW_RIGHT;
    }

    /**
     * where 查询条件.
     */
    public function where(...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($cond, static::LOGIC_AND);
        array_unshift($cond, 'where');

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * orWhere 查询条件.
     */
    public function orWhere(...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($cond, static::LOGIC_OR);
        array_unshift($cond, 'where');

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * Where 原生查询.
     */
    public function whereRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $cond = [];
        array_unshift($cond, static::LOGIC_AND);
        array_unshift($cond, 'where');
        $cond[] = [':stringSimple' => static::raw($raw)];

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * Where 原生 OR 查询.
     */
    public function orWhereRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $cond = [];
        array_unshift($cond, static::LOGIC_OR);
        array_unshift($cond, 'where');
        $cond[] = [':stringSimple' => static::raw($raw)];

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * exists 方法支持
     */
    public function whereExists(mixed $exists): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addConditions([':exists' => $exists]);
    }

    /**
     * not exists 方法支持
     */
    public function whereNotExists(mixed $exists): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addConditions([':notexists' => $exists]);
    }

    /**
     * whereBetween 查询条件.
     */
    public function whereBetween(...$cond): self
    {
        return $this->callWhereSugar('whereBetween', $cond);
    }

    /**
     * whereNotBetween 查询条件.
     */
    public function whereNotBetween(...$cond): self
    {
        return $this->callWhereSugar('whereNotBetween', $cond);
    }

    /**
     * whereNull 查询条件.
     */
    public function whereNull(...$cond): self
    {
        return $this->callWhereSugar('whereNull', $cond);
    }

    /**
     * whereNotNull 查询条件.
     */
    public function whereNotNull(...$cond): self
    {
        return $this->callWhereSugar('whereNotNull', $cond);
    }

    /**
     * whereIn 查询条件.
     */
    public function whereIn(...$cond): self
    {
        return $this->callWhereSugar('whereIn', $cond);
    }

    /**
     * whereNotIn 查询条件.
     */
    public function whereNotIn(...$cond): self
    {
        return $this->callWhereSugar('whereNotIn', $cond);
    }

    /**
     * whereLike 查询条件.
     */
    public function whereLike(...$cond): self
    {
        return $this->callWhereSugar('whereLike', $cond);
    }

    /**
     * whereNotLike 查询条件.
     */
    public function whereNotLike(...$cond): self
    {
        return $this->callWhereSugar('whereNotLike', $cond);
    }

    /**
     * whereDate 查询条件.
     */
    public function whereDate(...$cond): self
    {
        return $this->callWhereTimeSugar('whereDate', $cond);
    }

    /**
     * whereDay 查询条件.
     */
    public function whereDay(...$cond): self
    {
        return $this->callWhereTimeSugar('whereDay', $cond);
    }

    /**
     * whereMonth 查询条件.
     */
    public function whereMonth(...$cond): self
    {
        return $this->callWhereTimeSugar('whereMonth', $cond);
    }

    /**
     * whereYear 查询条件.
     */
    public function whereYear(...$cond): self
    {
        return $this->callWhereTimeSugar('whereYear', $cond);
    }

    /**
     * 参数绑定支持.
     */
    public function bind(mixed $names, mixed $value = null, ?int $dataType = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (is_array($names)) {
            foreach ($names as $key => $item) {
                if (!is_array($item)) {
                    $item = $dataType ? [$item, $dataType] : [$item];
                }
                $this->bindParams[$key] = $item;
            }
        } else {
            if (!is_array($value)) {
                $value = $dataType ? [$value, $dataType] : [$value];
            }
            $this->bindParams[$names] = $value;
        }

        return $this;
    }

    /**
     * index 强制索引（或者忽略索引）.
     *
     * @throws \InvalidArgumentException
     */
    public function forceIndex(array|string $indexs, $type = 'FORCE'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (!isset(static::$indexTypes[$type])) {
            $e = sprintf('Invalid Index type `%s`.', $type);

            throw new InvalidArgumentException($e);
        }

        $type = strtoupper($type);
        $indexs = normalize($indexs);
        foreach ($indexs as $value) {
            $value = normalize($value);
            foreach ($value as $tmp) {
                $this->options['index'][$type][] = $tmp;
            }
        }

        return $this;
    }

    /**
     * index 忽略索引.
     */
    public function ignoreIndex(array|string $indexs): self
    {
        return $this->forceIndex($indexs, 'IGNORE');
    }

    /**
     * join 查询.
     */
    public function join(array|Closure|Condition|Select|string $table, array|string $cols, ...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addJoin('inner join', $table, $cols, ...$cond);
    }

    /**
     * innerJoin 查询.
     */
    public function innerJoin(array|Closure|Condition|Select|string $table, array|string $cols, ...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addJoin('inner join', $table, $cols, ...$cond);
    }

    /**
     * leftJoin 查询.
     */
    public function leftJoin(array|Closure|Condition|Select|string $table, array|string $cols, ...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addJoin('left join', $table, $cols, ...$cond);
    }

    /**
     * rightJoin 查询.
     */
    public function rightJoin(array|Closure|Condition|Select|string $table, array|string $cols, ...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addJoin('right join', $table, $cols, ...$cond);
    }

    /**
     * fullJoin 查询.
     */
    public function fullJoin(array|Closure|Condition|Select|string $table, array|string $cols, ...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addJoin('full join', $table, $cols, ...$cond);
    }

    /**
     * crossJoin 查询.
     */
    public function crossJoin(array|Closure|Condition|Select|string $table, array|string $cols, ...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addJoin('cross join', $table, $cols, ...$cond);
    }

    /**
     * naturalJoin 查询.
     */
    public function naturalJoin(array|Closure|Condition|Select|string $table, array|string $cols, ...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addJoin('natural join', $table, $cols, ...$cond);
    }

    /**
     * 添加一个 UNION 查询.
     *
     * @throws \InvalidArgumentException
     */
    public function union(Select|Condition|array|callable|string $selects, string $type = 'UNION'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (!isset(static::$unionTypes[$type])) {
            $e = sprintf('Invalid UNION type `%s`.', $type);

            throw new InvalidArgumentException($e);
        }

        if (!is_array($selects)) {
            $selects = [$selects];
        }

        foreach ($selects as $tmp) {
            $this->options['union'][] = [$tmp, $type];
        }

        return $this;
    }

    /**
     * 添加一个 UNION ALL 查询.
     */
    public function unionAll(Select|Condition|array|callable|string $selects): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->union($selects, 'UNION ALL');
    }

    /**
     * 指定 GROUP BY 子句.
     */
    public function groupBy(array|string $expression): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $currentTableName = $this->getTable();
        $expression = $this->convertExpressionToArray($expression);
        foreach ($expression as $value) {
            $value = $this->convertExpressionToArray($value);
            foreach ($value as $tmp) {
                if (preg_match('/(.+)\.(.+)/', $tmp, $matches)) {
                    $currentTableName = $matches[1];
                    $tmp = $matches[2];
                }
                $tmp = $this->normalizeColumn($tmp, $currentTableName);
                $this->options['group'][] = $tmp;
            }
        }

        return $this;
    }

    /**
     * 添加一个 HAVING 条件.
     *
     * - 参数规范参考 where()方法.
     */
    public function having(...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($cond, static::LOGIC_AND);
        array_unshift($cond, 'having');

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * orHaving 查询条件.
     */
    public function orHaving(...$cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($cond, static::LOGIC_OR);
        array_unshift($cond, 'having');

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * having 原生查询.
     */
    public function havingRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $cond = [];
        array_unshift($cond, static::LOGIC_AND);
        array_unshift($cond, 'having');
        $cond[] = [':stringSimple' => static::raw($raw)];

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * having 原生 OR 查询.
     */
    public function orHavingRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $cond = [];
        array_unshift($cond, static::LOGIC_OR);
        array_unshift($cond, 'having');
        $cond[] = [':stringSimple' => static::raw($raw)];

        return $this->aliatypeAndLogic(...$cond);
    }

    /**
     * havingBetween 查询条件.
     */
    public function havingBetween(...$cond): self
    {
        return $this->callHavingSugar('havingBetween', $cond);
    }

    /**
     * havingNotBetween 查询条件.
     */
    public function havingNotBetween(...$cond): self
    {
        return $this->callHavingSugar('havingNotBetween', $cond);
    }

    /**
     * havingNull 查询条件.
     */
    public function havingNull(...$cond): self
    {
        return $this->callHavingSugar('havingNull', $cond);
    }

    /**
     * havingNotNull 查询条件.
     */
    public function havingNotNull(...$cond): self
    {
        return $this->callHavingSugar('havingNotNull', $cond);
    }

    /**
     * havingIn 查询条件.
     */
    public function havingIn(...$cond): self
    {
        return $this->callHavingSugar('havingIn', $cond);
    }

    /**
     * havingNotIn 查询条件.
     */
    public function havingNotIn(...$cond): self
    {
        return $this->callHavingSugar('havingNotIn', $cond);
    }

    /**
     * havingLike 查询条件.
     */
    public function havingLike(...$cond): self
    {
        return $this->callHavingSugar('havingLike', $cond);
    }

    /**
     * havingNotLike 查询条件.
     */
    public function havingNotLike(...$cond): self
    {
        return $this->callHavingSugar('havingNotLike', $cond);
    }

    /**
     * havingDate 查询条件.
     */
    public function havingDate(...$cond): self
    {
        return $this->callHavingTimeSugar('havingDate', $cond);
    }

    /**
     * havingDay 查询条件.
     */
    public function havingDay(...$cond): self
    {
        return $this->callHavingTimeSugar('havingDay', $cond);
    }

    /**
     * havingMonth 查询条件.
     */
    public function havingMonth(...$cond): self
    {
        return $this->callHavingTimeSugar('havingMonth', $cond);
    }

    /**
     * havingYear 查询条件.
     */
    public function havingYear(...$cond): self
    {
        return $this->callHavingTimeSugar('havingYear', $cond);
    }

    /**
     * 添加排序.
     */
    public function orderBy(array|string $expression, string $orderDefault = 'ASC'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $orderDefault = strtoupper($orderDefault);
        $tableName = $this->getTable();
        $expression = $this->convertExpressionToArray($expression);
        foreach ($expression as $value) {
            $value = $this->convertExpressionToArray($value);
            foreach ($value as $tmp) {
                // 表达式支持
                if (preg_match('/^'.static::raw('(.+?)').'$/', $tmp, $threeMatches)) {
                    $tmp = $this->normalizeExpression($threeMatches[1], $tableName);
                    if (preg_match('/(.*\W)('.'ASC'.'|'.'DESC'.')\b/si', $tmp, $matches)) {
                        $tmp = trim($matches[1]);
                        $sort = strtoupper($matches[2]);
                    } else {
                        $sort = $orderDefault;
                    }

                    $this->options['order'][] = $tmp.' '.$sort;
                } else {
                    $currentTableName = $tableName;
                    $sort = $orderDefault;

                    if (preg_match('/(.*\W)('.'ASC'.'|'.'DESC'.')\b/si', $tmp, $matches)) {
                        $tmp = trim($matches[1]);
                        $sort = strtoupper($matches[2]);
                    }

                    if (!preg_match('/\(.*\)/', $tmp)) {
                        if (preg_match('/(.+)\.(.+)/', $tmp, $matches)) {
                            $currentTableName = $matches[1];
                            $tmp = $matches[2];
                        }
                        $tmp = $this->normalizeTableOrColumn("{$currentTableName}.{$tmp}");
                    }

                    $this->options['order'][] = $tmp.' '.$sort;
                }
            }
        }

        return $this;
    }

    /**
     * 最近排序数据.
     */
    public function latest(string $field = 'create_at'): self
    {
        return $this->orderBy($field, 'DESC');
    }

    /**
     * 最早排序数据.
     */
    public function oldest(string $field = 'create_at'): self
    {
        return $this->orderBy($field, 'ASC');
    }

    /**
     * 创建一个 SELECT DISTINCT 查询.
     */
    public function distinct(bool $flag = true): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->options['distinct'] = $flag;

        return $this;
    }

    /**
     * 总记录数.
     */
    public function count(string $field = '*', string $alias = 'row_count'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addAggregate('COUNT', $field, $alias);
    }

    /**
     * 平均数.
     */
    public function avg(string $field, string $alias = 'avg_value'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addAggregate('AVG', $field, $alias);
    }

    /**
     * 最大值.
     */
    public function max(string $field, string $alias = 'max_value'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addAggregate('MAX', $field, $alias);
    }

    /**
     * 最小值.
     */
    public function min(string $field, string $alias = 'min_value'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addAggregate('MIN', $field, $alias);
    }

    /**
     * 合计
     */
    public function sum(string $field, string $alias = 'sum_value'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addAggregate('SUM', $field, $alias);
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     */
    public function one(): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->options['limitCount'] = 1;
        $this->options['limitOffset'] = null;
        $this->options['limitQuery'] = false;

        return $this;
    }

    /**
     * 指示查询所有符合条件的记录.
     */
    public function all(): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if ($this->options['limitQuery']) {
            return $this;
        }

        $this->options['limitCount'] = null;
        $this->options['limitOffset'] = null;
        $this->options['limitQuery'] = true;

        return $this;
    }

    /**
     * 查询几条记录.
     */
    public function top(int $count = 30): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->limit(0, $count);
    }

    /**
     * limit 限制条数.
     */
    public function limit(int $offset = 0, int $count = 0): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (0 === $count) {
            return $this->top($offset);
        }

        $this->options['limitCount'] = $count;
        $this->options['limitOffset'] = $offset;
        $this->options['limitQuery'] = true;

        return $this;
    }

    /**
     * 排它锁 FOR UPDATE 查询.
     *
     * @throws \RuntimeException
     */
    public function forUpdate(bool $flag = true): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if ($flag && $this->options['lockShare']) {
            throw new RuntimeException('Lock share and for update cannot exist at the same time.');
        }

        $this->options['forUpdate'] = $flag;

        return $this;
    }

    /**
     * 共享锁 LOCK SHARE 查询.
     *
     * @throws \RuntimeException
     */
    public function lockShare(bool $flag = true): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if ($flag && $this->options['forUpdate']) {
            throw new RuntimeException('Lock share and for update cannot exist at the same time.');
        }

        $this->options['lockShare'] = $flag;

        return $this;
    }

    /**
     * 获得查询字符串.
     */
    public function makeSql(bool $withLogicGroup = false): string
    {
        $sql = [$this->parseComment(), 'SELECT'];

        foreach (array_keys($this->options) as $option) {
            $option = (string) $option;
            if ('from' === $option) {
                $sql['from'] = '';
            } elseif (in_array($option, ['comment', 'union'], true)) {
                continue;
            } else {
                if (method_exists($this, $method = 'parse'.ucfirst($option))) {
                    $sql[$option] = $this->{$method}();
                }
            }
        }

        $sql['from'] = $this->parseFrom();

        // 删除空元素
        foreach ($sql as $offset => $option) {
            if ('' === trim($option)) {
                unset($sql[$offset]);
            }
        }

        $sql[] = $this->parseUnion();
        $result = trim(implode(' ', $sql));

        if (true === $withLogicGroup) {
            return '('.$result.')';
        }

        return $result;
    }

    /**
     * 返回参数绑定.
     */
    public function getBindParams(): array
    {
        return $this->bindParams;
    }

    /**
     * 重置参数绑定.
     */
    public function resetBindParams(array $bindParams = []): void
    {
        $this->bindParams = $bindParams;
    }

    /**
     * 设置参数绑定前缀.
     */
    public function setBindParamsPrefix(string $bindParamsPrefix): void
    {
        $this->bindParamsPrefix = $bindParamsPrefix ? $bindParamsPrefix.'_' : '';
    }

    /**
     * 表达式转换为数组.
     */
    protected function convertExpressionToArray(array|string $expression): array
    {
        // 处理条件表达式
        if (is_string($expression) &&
            false !== strpos($expression, ',') &&
            preg_match_all('/'.static::raw('(.+?)').'/', $expression, $matches)) {
            $expression = str_replace(
                $matches[1][0],
                base64_encode($matches[1][0]),
                $expression
            );
        }

        $expression = normalize($expression);

        // 还原
        if (!empty($matches)) {
            foreach ($matches[1] as $tmp) {
                $key = array_search(static::raw(base64_encode($tmp)), $expression, true);
                $expression[$key] = static::raw($tmp);
            }
        }

        return $expression;
    }

    /**
     * 调用 where 语法糖.
     */
    protected function callWhereSugar(string $method, array $args): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);

        if (0 === strpos($method, 'whereNot')) {
            $type = 'not '.strtolower(substr($method, 8));
        } else {
            $type = strtolower(substr($method, 5));
        }

        array_unshift($args, $type);

        return $this->aliasCondition(...$args);
    }

    /**
     * 调用 where 时间语法糖.
     */
    protected function callWhereTimeSugar(string $method, array $args): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setInTimeCondition(strtolower(substr($method, 5)));
        $this->where(...$args);
        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * 调用 having 语法糖.
     *
     * 
     */
    protected function callHavingSugar(string $method, array $args): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        if (0 === strpos($method, 'havingNot')) {
            $type = 'not '.strtolower(substr($method, 9));
        } else {
            $type = strtolower(substr($method, 6));
        }

        array_unshift($args, $type);

        return $this->aliasCondition(...$args);
    }

    /**
     * 调用 having 时间语法糖.
     */
    protected function callHavingTimeSugar(string $method, array $args): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setInTimeCondition(strtolower(substr($method, 6)));
        $this->having(...$args);
        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * 解析查询注释分析结果.
     */
    protected function parseComment(): string
    {
        if (empty($this->options['comment'])) {
            return '';
        }

        return '/*'.$this->options['comment'].'*/';
    }

    /**
     * 解析 prefix 分析结果.
     */
    protected function parsePrefix(): string
    {
        if (empty($this->options['prefix'])) {
            return '';
        }

        return implode(' ', $this->options['prefix']);
    }

    /**
     * 解析 distinct 分析结果.
     */
    protected function parseDistinct(): string
    {
        if (!$this->options['distinct']) {
            return '';
        }

        return 'DISTINCT';
    }

    /**
     * 分析语句中的字段.
     */
    protected function parseColumns(): string
    {
        if (empty($this->options['columns'])) {
            return '';
        }

        $columns = [];
        foreach ($this->options['columns'] as $item) {
            list($tableName, $col, $alias) = $item;

            // 表达式支持
            if (preg_match('/^'.static::raw('(.+?)').'$/', $col, $matches)) {
                $columns[] = $this->normalizeExpression($matches[1], $tableName);
            } else {
                if ('*' !== $col && $alias) {
                    $columns[] = $this->normalizeTableOrColumn("{$tableName}.{$col}", $alias, 'AS');
                } else {
                    $columns[] = $this->normalizeTableOrColumn("{$tableName}.{$col}");
                }
            }
        }

        return implode(',', $columns);
    }

    /**
     * 解析 aggregate 分析结果.
     */
    protected function parseAggregate(): string
    {
        if (empty($this->options['aggregate'])) {
            return '';
        }

        $columns = [];

        foreach ($this->options['aggregate'] as $item) {
            list(, $field, $alias) = $item;
            $columns[] = $field.' AS '.$alias;
        }

        return $columns ? implode(',', $columns) : '';
    }

    /**
     * 解析 from 分析结果.
     */
    protected function parseFrom(): string
    {
        if (empty($this->options['from'])) {
            return '';
        }

        $from = [];

        foreach ($this->options['from'] as $alias => $value) {
            $tmp = '';

            // 如果不是第一个 FROM，则添加 JOIN
            if (!empty($from)) {
                $tmp .= strtoupper($value['join_type']).' ';
            }

            // 表名子表达式支持
            if (false !== strpos($value['table_name'], '(')) {
                $tmp .= $value['table_name'].' '.$alias;
            } elseif ($alias === $value['table_name']) {
                $tmp .= $this->normalizeTableOrColumn("{$value['schema']}.{$value['table_name']}");
            } else {
                $tmp .= $this->normalizeTableOrColumn("{$value['schema']}.{$value['table_name']}", $alias);
            }

            // 添加 JOIN 查询条件
            if (!empty($from) && !empty($value['join_cond'])) {
                $tmp .= ' ON '.$value['join_cond'];
            }

            $from[] = $tmp;
        }

        return 'FROM '.implode(' ', $from);
    }

    /**
     * 解析 table 分析结果.
     */
    protected function parseTable(): string
    {
        $alias = null;
        foreach ($this->options['from'] as $alias => $value) {
            if ($alias === $value['table_name']) {
                $alias = $this->normalizeTableOrColumn("{$value['schema']}.{$value['table_name']}");
            }

            break;
        }

        return $alias;
    }

    /**
     * 解析 index 分析结果.
     */
    protected function parseIndex(): string
    {
        $index = '';
        foreach (['FORCE', 'IGNORE'] as $type) {
            if (empty($this->options['index'][$type])) {
                continue;
            }

            $index .= ($index ? ' ' : '').$type.' INDEX('.
                implode(',', $this->options['index'][$type]).')';
        }

        return $index;
    }

    /**
     * 解析 where 分析结果.
     */
    protected function parseWhere(bool $child = false): string
    {
        if (empty($this->options['where'])) {
            return '';
        }

        return $this->analyseCondition('where', $child);
    }

    /**
     * 解析 union 分析结果.
     */
    protected function parseUnion(): string
    {
        if (empty($this->options['union'])) {
            return '';
        }

        $sql = '';
        if ($this->options['union']) {
            $optionsCount = count($this->options['union']);
            foreach ($this->options['union'] as $index => $value) {
                list($union, $type) = $value;
                if ($union instanceof self || $union instanceof Select) {
                    if ($union instanceof self) {
                        $tmp = $union->makeSql();
                        $this->bindParams = array_merge($this->bindParams, $union->getBindParams());
                        $union->resetBindParams();
                        $union = $tmp;
                    } else {
                        $tmp = $union->makeSql();
                        $this->bindParams = array_merge($this->bindParams, $union->databaseCondition()->getBindParams());
                        $union->databaseCondition()->resetBindParams();
                        $union = $tmp;
                    }
                }

                if ($index <= $optionsCount - 1) {
                    $sql .= PHP_EOL.$type.' '.$union;
                }
            }
        }

        return $sql;
    }

    /**
     * 解析 order 分析结果.
     */
    protected function parseOrder(): string
    {
        if (empty($this->options['order'])) {
            return '';
        }

        return 'ORDER BY '.implode(',', array_unique($this->options['order']));
    }

    /**
     * 解析 group 分析结果.
     */
    protected function parseGroup(): string
    {
        if (empty($this->options['group'])) {
            return '';
        }

        return 'GROUP BY '.implode(',', $this->options['group']);
    }

    /**
     * 解析 having 分析结果.
     */
    protected function parseHaving(bool $child = false): string
    {
        if (empty($this->options['having'])) {
            return '';
        }

        return $this->analyseCondition('having', $child);
    }

    /**
     * 解析 limit 分析结果.
     */
    protected function parseLimitCount(bool $withoutOffset = false): string
    {
        if (null === $this->options['limitOffset'] &&
            null === $this->options['limitCount']) {
            return '';
        }

        return $this->connect->limitCount(
            $this->options['limitCount'],
            $withoutOffset ? null : $this->options['limitOffset']
        );
    }

    /**
     * 解析 FOR UPDATE 分析结果.
     */
    protected function parseForUpdate(): string
    {
        if (!$this->options['forUpdate']) {
            return '';
        }

        return 'FOR UPDATE';
    }

    /**
     * 解析 LOCK SHARE 分析结果.
     */
    protected function parseLockShare(): string
    {
        if (!$this->options['lockShare']) {
            return '';
        }

        return 'LOCK IN SHARE MODE';
    }

    /**
     * 解析条件.
     * 
     * - 包括 where 和 having
     */
    protected function analyseCondition(string $condType, bool $child = false): string
    {
        $sqlCond = [];
        $table = $this->getTable();
        foreach ($this->options[$condType] as $key => $cond) {
            // 逻辑连接符
            if (in_array($cond, [static::LOGIC_AND, static::LOGIC_OR], true)) {
                $sqlCond[] = strtoupper($cond);
                continue;
            }

            // 特殊处理
            if (is_string($key)) {
                // 嵌套 string
                if (':string' === $key) {
                    $sqlCond[] = implode(' AND ', $cond);
                } elseif (':stringSimple' === $key) {
                    foreach ($cond as $c) {
                        // 逻辑连接符
                        if (in_array($c, [static::LOGIC_AND, static::LOGIC_OR], true)) {
                            $sqlCond[] = strtoupper($c);
                        } else {
                            $sqlCond[] = $c;
                        }
                    }
                }
            } elseif (is_array($cond)) {
                // 表达式支持
                if (preg_match('/^'.static::raw('(.+?)').'$/', $cond[0], $matches)) {
                    $cond[0] = $this->normalizeExpression($matches[1], $table);
                } else {
                    // 字段处理
                    if (false !== strpos($cond[0], '.')) {
                        $tmp = explode('.', $cond[0]);
                        $currentTable = $tmp[0];
                        $cond[0] = $tmp[1];
                    } else {
                        $currentTable = $table;
                    }

                    $cond[0] = $this->normalizeTableColumn(
                        $cond[0],
                        $currentTable
                    );
                }

                // 分析是否存在自动格式化时间标识
                $findTime = null;
                if (0 === strpos($cond[1], '@')) {
                    foreach (['date', 'month', 'day', 'year'] as $timeType) {
                        if (0 === stripos($cond[1], '@'.$timeType)) {
                            $findTime = $timeType;
                            $cond[1] = ltrim(substr($cond[1], strlen($timeType) + 1));
                            break;
                        }
                    }
                }

                // 格式化字段值，支持数组
                if (array_key_exists(2, $cond)) {
                    $isArray = true;
                    if (!is_array($cond[2])) {
                        $cond[2] = [$cond[2]];
                        $isArray = false;
                    }

                    $rawCondKey = $condGenerateBindParams = [];
                    foreach ($cond[2] as $condKey => $tmp) {
                        // 对象子表达式支持
                        if (is_object($tmp) && ($tmp instanceof self || $tmp instanceof Select)) {
                            $tmp = $this->analyseConditionFieldValueObjectExpression($cond[0], $tmp, $condKey, $rawCondKey, $condGenerateBindParams);
                        }

                        // 回调方法子表达式支持
                        elseif (is_object($tmp) && $tmp instanceof Closure) {
                            $this->conditionSubExpression(function(Condition $condition) use(
                                &$tmp, $cond, $condKey,
                                &$rawCondKey, &$condGenerateBindParams,
                            ) {
                                $tmp($condition);
                                $condition->setBindParamsPrefix($bindParams = $this->generateBindParams($cond[0]));
                                $tmp = $condition->makeSql(true);
                                $rawCondKey[] = $condKey;
                                $condGenerateBindParams[$condKey] = $bindParams;
                            });
                        }

                        // 表达式支持
                        elseif (is_string($tmp) && preg_match('/^'.static::raw('(.+?)').'$/', $tmp, $matches)) {
                            $tmp = $this->normalizeExpression($matches[1], $table);
                            $rawCondKey[] = $condKey;
                        }

                        // 自动格式化时间
                        elseif (null !== $findTime) {
                            $tmp = $this->parseTime($cond[0], $tmp, $findTime);
                        }
                        $cond[2][$condKey] = $tmp;
                    }

                    if (false === $isArray) {
                        $cond[2] = reset($cond[2]);
                    }
                }

                // 拼接结果
                if (in_array($cond[1], ['null', 'not null'], true)) {
                    $sqlCond[] = $this->analyseConditionGenerateNull($cond);
                } elseif (in_array($cond[1], ['in', 'not in'], true)) {
                    $sqlCond[] = $this->analyseConditionGenerateIn($cond, $rawCondKey, $condGenerateBindParams);
                } elseif (in_array($cond[1], ['between', 'not between'], true)) {
                    $sqlCond[] = $this->analyseConditionGenerateBetween($cond, $rawCondKey, $condGenerateBindParams);
                } elseif (is_scalar($cond[2])) {
                    $sqlCond[] = $this->analyseConditionGenerateNormal($cond, $rawCondKey, $condGenerateBindParams);
                } elseif ('=' === $cond[1] && null === $cond[2]) {
                    $sqlCond[] = $this->analyseConditionGenerateSpecialNull($cond);
                }
            }
        }

        // 剔除第一个逻辑符
        array_shift($sqlCond);

        return (false === $child ? strtoupper($condType).' ' : '').
            implode(' ', $sqlCond);
    }

    protected function conditionSubExpression(Closure $call, ?string $table = null): void
    {
        $condition = new static($this->connect);
        $condition->setTable($table ?? $this->getTable());
        $call($condition);
        $this->bindParams = array_merge($condition->getBindParams(), $this->bindParams);
        $condition->resetBindParams();
    }

    protected function analyseConditionFieldValueObjectExpression(string $fieldName, mixed $fieldValueItem, int $condKey, array &$rawCondKey, array &$condGenerateBindParams): string
    {
        if ($fieldValueItem instanceof Select) {
            $fieldValueItem->databaseCondition()->setBindParamsPrefix($bindParams = $this->generateBindParams($fieldName));
            $data = $fieldValueItem->databaseCondition()->makeSql(true);
            $this->bindParams = array_merge($fieldValueItem->databaseCondition()->getBindParams(), $this->bindParams);
            $fieldValueItem->databaseCondition()->resetBindParams();
            $fieldValueItem = $data;
        } else {
            $fieldValueItem->setBindParamsPrefix($bindParams = $this->generateBindParams($fieldName));
            $data = $fieldValueItem->makeSql(true);
            $this->bindParams = array_merge($fieldValueItem->getBindParams(), $this->bindParams);
            $fieldValueItem->resetBindParams();
            $fieldValueItem = $data;
        }

        $rawCondKey[] = $condKey;
        $condGenerateBindParams[$condKey] = $bindParams;

        return $fieldValueItem;
    }

    protected function analyseConditionGenerateNormal(array $cond, array $rawCondKey, array $condGenerateBindParams): string
    {
        if (in_array(0, $rawCondKey, true)) {
            return $cond[0].' '.strtoupper($cond[1]).' '.$cond[2];
        }

        $sql = $cond[0].' '.strtoupper($cond[1]).' '.':'.
            ($bindParams = $condGenerateBindParams[0] ?? $this->generateBindParams($cond[0]));
        $this->bind($bindParams, $cond[2]);

        return $sql;
    }

    protected function analyseConditionGenerateNull(array $cond): string
    {   
        return $cond[0].' IS '.strtoupper($cond[1]);
    }

    protected function analyseConditionGenerateSpecialNull(array $cond): string
    {   
        return  $cond[0].' IS NULL';
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function analyseConditionGenerateIn(array $cond, array $rawCondKey, array $condGenerateBindParams): string
    {   
        if (!$rawCondKey && (!is_array($cond[2]) || empty($cond[2]))) {
            $e = 'The [not] in param value must not be an empty array.';

            throw new InvalidArgumentException($e);
        }

        $bindParams = $condGenerateBindParams[0] ?? $this->generateBindParams($cond[0]);
        $inData = is_array($cond[2]) ? $cond[2] : [$cond[2]];
        foreach ($inData as $k => &$v) {
            if (!in_array($k, $rawCondKey, true)) {
                $this->bind(($tmpBindParams = $bindParams.'_in').$k, $v);
                $v = ':'.$tmpBindParams.$k;
            }
        }

        return $cond[0].' '.
            strtoupper($cond[1]).' '.
            (
                1 === count($inData) && isset($inData[0]) && 0 === strpos($inData[0], '(') ? $inData[0] : '('.implode(',', $inData).')'
            );
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function analyseConditionGenerateBetween(array $cond, array $rawCondKey, array $condGenerateBindParams): string
    {   
        if (!is_array($cond[2]) || count($cond[2]) < 2) {
            $e = 'The [not] between param value must be an array which not less than two elements.';

            throw new InvalidArgumentException($e);
        }

        $betweenValue = $bindParams = [];
        foreach ($cond[2] as $k => $v) {
            if (in_array($k, $rawCondKey, true)) {
                $betweenValue[$k] = $cond[2][$k];
            } else {
                if (!$bindParams) {
                    $bindParams = [
                        $condGenerateBindParams[0] ?? $this->generateBindParams($cond[0]),
                        'between' === $cond[1] ? 'between' : 'notbetween',
                    ];
                }
                $tmpBindParams = $bindParams[0].'_'.$bindParams[1].$k;
                $betweenValue[$k] = ':'.$tmpBindParams;
                $this->bind($tmpBindParams, $cond[2][$k]);
            }
        }

        return $cond[0].' '.strtoupper($cond[1]).' '.$betweenValue[0].' AND '.$betweenValue[1];
    } 

    /**
     * 生成绑定参数.
     *
     * - 支持防止重复的参数生成
     */
    protected function generateBindParams(string $bindParams): string
    {
        if (!preg_match('/^[A-Za-z0-9\_]+$/', $bindParams)) {
            $bindParams = trim(preg_replace('/[^A-Za-z0-9\_]/', '_', $bindParams), '_');
            $bindParams = preg_replace('/[\_]{2,}/', '_', $bindParams);
        }
        $bindParams = $this->bindParamsPrefix.$bindParams;
        if (isset($this->bindParamsCache[$bindParams])) {
            $tmp = $bindParams.'_'.$this->bindParamsCache[$bindParams];
            if (isset($this->bindParamsCache[$tmp])) {
                return $this->generateBindParams($tmp);
            }
            $this->bindParamsCache[$bindParams]++;

            return $tmp;
        }

        $this->bindParamsCache[$bindParams] = 1;

        return $bindParams;
    }

    /**
     * 别名条件.
     */
    protected function aliasCondition(string $conditionType, mixed $cond): self
    {
        if (!is_array($cond)) {
            $args = func_get_args();
            $this->addConditions($args[1], $conditionType, $args[2] ?? null);
        } else {
            foreach ($cond as $tmp) {
                $this->addConditions($tmp[0], $conditionType, $tmp[1]);
            }
        }

        return $this;
    }

    /**
     * 别名类型和逻辑.
     *
     * @todo 代码复杂度过高，需要重构
     */
    protected function aliatypeAndLogic(string $type, string $logic, mixed $cond): self
    {
        $this->setTypeAndLogic($type, $logic);

        if ($cond instanceof Closure) {
            $this->conditionSubExpression(function(Condition $condition) use($type, $cond) {
                $cond($condition);
                $tmp = $condition->{'parse'.ucwords($type)}(true);
                $this->setConditionItem('('.$tmp.')', ':string');
            });

            return $this;
        }

        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        return $this->addConditions(...$args);
    }

    /**
     * 组装条件.
     *
     * @throws \InvalidArgumentException
     */
    protected function addConditions(string|array $fieldOrCond, mixed $operator = null, mixed $value = null): self
    {
        // 整理多个参数到二维数组
        if (!is_array($fieldOrCond)) {
            $data = [$fieldOrCond, $operator];
            if (3 === func_num_args()) {
                $data[] = $value;
            }
            $this->addConditionsEach($data);
            return $this;
        }

        if (count($fieldOrCond) === count($fieldOrCond, 1)) {
            $conditions = $this->isAssociativeArray($fieldOrCond) ? $fieldOrCond : [$fieldOrCond];
        } else {
            $conditions = $fieldOrCond;
        }
        foreach ($conditions as $key => $cond) {
            $this->addConditionsEach($cond, is_string($key) ? $key : null); 
        } 

        return $this;
    }

    /**
     * 是否为关联数组.
     */
    protected function isAssociativeArray(array $data): bool
    {
        $keys = array_keys($data);
       
        return $keys !== array_keys($keys);
    }

    protected function addConditionsEach(array|string|Select|Condition|Closure|int|float $cond, ?string $key = null): void
    {
        match($key) {
            ':string', ':stringSimple' => $this->addConditionsString($key, $cond),
            ':subor', ':suband' => $this->addConditionsSub($key, $cond),
            ':exists', ':notexists' => $this->addConditionsExists($key, $cond),
            default => $this->addConditionsNormal($cond, $key),
        };
    }

    protected function addConditionsString(string $key, string $cond): void
    {
        // 表达式支持
        if (preg_match('/^'.static::raw('(.+?)').'$/', $cond, $matches)) {
            $cond = $this->normalizeExpression($matches[1], $this->getTable());
        }

        $this->setConditionItem($cond, $key);
    }

    protected function addConditionsSub(string $key, array $cond): void
    {
        $this->conditionSubExpression(function(Condition $condition) use($key, $cond) {
            $typeAndLogic = $this->getTypeAndLogic();
            $condition->setTypeAndLogic($typeAndLogic[0]);
    
            // 逻辑表达式
            if (isset($cond[':logic'])) {
                if (strtolower($cond[':logic']) === static::LOGIC_OR) {
                    $condition->setTypeAndLogic(null, static::LOGIC_OR);
                }
                unset($cond[':logic']);
            }
    
            $condition = $condition->addConditions($cond);
            $parseType = 'parse'.ucwords($typeAndLogic[0]);
            $oldLogic = $typeAndLogic[1];
            $condition->setBindParamsPrefix($this->generateBindParams($this->getTable().'.'.substr($key, 1)));
            $this->setTypeAndLogic(null, ':subor' === $key ? static::LOGIC_OR : static::LOGIC_AND);
            $this->setConditionItem('('.$condition->{$parseType}(true).')', ':string');
            $this->setTypeAndLogic(null, $oldLogic);
        });
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function addConditionsExists(string $key, string|Select|Condition|Closure $cond): void
    {
        // having 不支持 [not] exists
        if ('having' === $this->getTypeAndLogic()[0]) {
            $e = 'Having do not support [not] exists.';

            throw new InvalidArgumentException($e);
        }

        if (is_object($cond) && ($cond instanceof self || $cond instanceof Select)) {
            $cond = $cond instanceof Select ? 
                $cond->databaseCondition()->makeSql() : 
                $cond->makeSql();
        } elseif (is_object($cond) && $cond instanceof Closure) {
            $this->conditionSubExpression(function(Condition $condition) use($key, &$cond) {
                $cond($condition);
                $condition->setBindParamsPrefix($this->generateBindParams($this->getTable().'.'.substr($key, 1)));
                $cond = $condition->makeSql();
            });
        }

        $cond = (':notexists' === $key ? 'NOT EXISTS ' : 'EXISTS ').'('.$cond.')';
        $this->setConditionItem($cond, ':string');
    }

    protected function addConditionsNormal(array|string|int $cond, ?string $key = null): void
    {
        // 处理字符串 "null"
        if (is_scalar($cond)) {
            $cond = (array) $cond;
        }

        // 合并字段到数组
        if ($key) {
            array_unshift($cond, $key);
        }

        // 处理默认 “=” 的类型
        if (2 === count($cond) && !in_array($cond[1], ['null', 'not null'], true)) {
            $cond[2] = $cond[1];
            $cond[1] = '=';
        }

        // 字段
        $cond[1] = trim($cond[1] ?? 'null');

        // 特殊类型
        if (in_array($cond[1], ['between', 'not between', 'in', 'not in', 'null', 'not null'], true)) {
            if (isset($cond[2]) && is_string($cond[2]) && $cond[2]) {
                $cond[2] = explode(',', $cond[2]);
            }
            $this->setConditionItem([$cond[0], $cond[1], $cond[2] ?? null]);
        }else {
            // 普通类型
            $this->setConditionItem($cond);
        }
    }

    /**
     * 设置条件的一项.
     */
    protected function setConditionItem(array|string $items, string $type = ''): void
    {
        $typeAndLogic = $this->getTypeAndLogic();

        // 字符串类型
        if ($type) {
            // 支持嵌套的 string
            if (':string' === $type) {
                if (empty($this->options[$typeAndLogic[0]][$type])) {
                    $this->options[$typeAndLogic[0]][] = $typeAndLogic[1];
                    $this->options[$typeAndLogic[0]][$type] = [];
                }
                $this->options[$typeAndLogic[0]][$type][] = $items;
            } elseif (':stringSimple' === $type) {
                $this->options[$typeAndLogic[0]][$type][] = $typeAndLogic[1];
                $this->options[$typeAndLogic[0]][$type][] = $items;
            }
        } else {
            // 格式化时间
            if (($inTimeCondition = $this->getInTimeCondition())) {
                $items[1] = '@'.$inTimeCondition.' '.$items[1];
            }
            $this->options[$typeAndLogic[0]][] = $typeAndLogic[1];
            $this->options[$typeAndLogic[0]][] = $items;
        }
    }

    /**
     * 设置条件的逻辑和类型.
     */
    protected function setTypeAndLogic(?string $type = null, ?string $logic = null): void
    {
        if (null !== $type) {
            $this->conditionType = $type;
        }

        if (null !== $logic) {
            $this->conditionLogic = $logic;
        }
    }

    /**
     * 获取条件的逻辑和类型.
     */
    protected function getTypeAndLogic(): array
    {
        return [
            $this->conditionType,
            $this->conditionLogic,
        ];
    }

    /**
     * 格式化字段.
     */
    protected function normalizeColumn(string $field, string $tableName): string
    {
        if (preg_match('/^'.static::raw('(.+?)').'$/', $field, $matches)) {
            $field = $this->normalizeExpression($matches[1], $tableName);
        } elseif (!preg_match('/\(.*\)/', $field)) {
            if (preg_match('/(.+)\.(.+)/', $field, $matches)) {
                $currentTableName = $matches[1];
                $field = $matches[2];
            } else {
                $currentTableName = $tableName;
            }

            $field = $this->normalizeTableOrColumn("{$currentTableName}.{$field}");
        }

        return $field;
    }

    /**
     * 连表 join 操作.
     *
     * @throws \InvalidArgumentException
     */
    protected function addJoin(string $joinType, array|Closure|Condition|Select|string $names, array|string $cols, mixed $cond = null): self
    {
        // 不能在使用 UNION 查询的同时使用 JOIN 查询
        if (count($this->options['union'])) {
            $e = 'JOIN queries cannot be used while using UNION queries.';

            throw new InvalidArgumentException($e);
        }

        // 是否分析 schema，子表达式不支持
        $parseSchema = true;
        $alias = '';
        if (is_array($names)) {
            $tmp = $names;
            foreach ($tmp as $alias => $names) {
                if (!is_string($alias)) {
                    $e = sprintf('Alias must be string,but %s given.', gettype($alias));

                    throw new InvalidArgumentException($e);
                }

                break;
            }
        }

        if (is_object($names) && ($names instanceof self || $names instanceof Select)) { 
            // 对象子表达式
            $table = $names->makeSql(true);
            if (!$alias) {
                $alias = $names instanceof Select ? $names->databaseCondition()->getAlias() : $names->getAlias();
            }
            $parseSchema = false;
        } elseif (is_object($names) && $names instanceof Closure) { 
            // 回调方法
            $condition = new static($this->connect);
            $condition->setTable($this->getTable());
            $names($condition);
            $table = $condition->makeSql(true);
            if (!$alias) {
                $alias = $condition->getAlias();
            }
            $parseSchema = false;
        } elseif (is_string($names) && 0 === strpos($names, '(')) { 
            // 字符串子表达式
            if (false !== ($position = strripos($names, 'as'))) {
                $table = trim(substr($names, 0, $position - 1));
                $alias = trim(substr($names, $position + 2));
            } else {
                $table = $names;
                if (!$alias) {
                    $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
                }
            }
            $parseSchema = false;
        } elseif (is_string($names)) {
            // 字符串指定别名
            if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $names, $matches)) {
                $table = $matches[1];
                $alias = $matches[2];
            } else {
                $table = $names;
            }
        }

        // 确定 table_name 和 schema
        if (true === $parseSchema) {
            $tmp = explode('.', $table);
            if (isset($tmp[1])) {
                $schema = $tmp[0];
                $tableName = $tmp[1];
            } else {
                $schema = null;
                $tableName = $table;
            }
        } else {
            $schema = null;
            $tableName = $table;
        }

        if (!$alias) {
            $alias = $tableName;
        }

        // 只有表操作才设置当前表
        if ($this->getIsTable()) {
            $this->setTable(
                ($schema ? $schema.'.' : '').$alias
            );
            $this->alias = $alias;
        }

        // 查询条件
        $args = func_get_args();
        if (count($args) > 3) {
            for ($i = 0; $i <= 2; $i++) {
                array_shift($args);
            }

            $this->conditionSubExpression(function(Condition $condition) use($args, &$cond) {
                $condition->where(...$args); 
                $cond = $condition->parseWhere(true);
            }, $alias);
        }

        // 添加一个要查询的数据表
        $this->options['from'][$alias] = [
            'join_type'  => $joinType,
            'table_name' => $tableName,
            'schema'     => $schema,
            'join_cond'  => $cond,
        ];

        // 添加查询字段
        $this->addCols($alias, $cols);

        return $this;
    }

    /**
     * 添加字段.
     */
    protected function addCols(string $tableName, array|string $cols): void
    {
        $cols = $this->convertExpressionToArray($cols);
        if (empty($cols)) {
            return;
        }

        foreach ($cols as $alias => $v) {
            $v = $this->convertExpressionToArray($v);
            foreach ($v as $col) {
                $currentTableName = $tableName;

                // 检查是不是 "字段名 AS 别名"这样的形式
                if (preg_match('/^(.+)\s+'.'AS'.'\s+(.+)$/i', $col, $matches)) {
                    $col = $matches[1];
                    $alias = $matches[2];
                }

                // 检查字段名是否包含表名称
                if (preg_match('/(.+)\.(.+)/', $col, $matches)) {
                    $currentTableName = $matches[1];
                    $col = $matches[2];
                }

                $this->options['columns'][] = [
                    $currentTableName,
                    $col,
                    is_string($alias) ? $alias : null,
                ];
            }
        }
    }

    /**
     * 添加一个集合查询.
     */
    protected function addAggregate(string $type, string $field, string $alias): self
    {
        $this->options['columns'] = [];
        $tableName = $this->getTable();

        // 表达式支持
        if (preg_match('/^'.static::raw('(.+?)').'$/', $field, $matches)) {
            $field = $this->normalizeExpression($matches[1], $tableName);
        } else {
            // 检查字段名是否包含表名称
            if (preg_match('/(.+)\.(.+)/', $field, $matches)) {
                $tableName = $matches[1];
                $field = $matches[2];
            }

            if ('*' === $field) {
                $tableName = '';
            }

            $field = $this->normalizeTableColumn($field, $tableName);
        }

        $field = "{$type}(${field})";

        $this->options['aggregate'][] = [
            $type,
            $field,
            $alias,
        ];

        $this->one();

        return $this;
    }

    /**
     * 删除参数绑定支持.
     */
    protected function deleteBindParams(int|string $names): void
    {
        if (isset($this->bindParams[$names])) {
            unset($this->bindParams[$names]);
        }
    }

    /**
     * 分析绑定参数数据.
     *
     * @throws \InvalidArgumentException
     */
    protected function normalizeBindData(array $data, array $bind, int &$pdoPositionalParameterIndex, int $index = 0): array
    {
        $fields = $values = [];
        $tableName = $this->getTable();
        $expressionRegex = '/^'.static::raw('(.+?)').'$/';
        foreach ($data as $key => $value) {
            $pdoNamedParameter = $pdoPositionalParameter = $isExpression = false;

            // 表达式支持
            if (is_string($value) && preg_match($expressionRegex, $value, $matches)) {
                $value = $this->normalizeExpression($matches[1], $tableName);
                if (0 === strpos($value, ':')) {
                    $pdoNamedParameter = true;
                } elseif ('?' === $value) {
                    $pdoPositionalParameter = true;
                } else {
                    $isExpression = true;
                }
            }

            // 字段
            if (0 === $index) {
                $fields[] = $key;
            }

            if (true === $pdoNamedParameter || (true === $isExpression && !empty($matches))) {
                $values[] = $value;
            } else {
                // 转换位置占位符至命名占位符
                if (true === $pdoPositionalParameter) {
                    if (isset($bind[$pdoPositionalParameterIndex])) {
                        $key = 'pdopositional2namedparameter_'.$pdoPositionalParameterIndex;
                        $value = $bind[$pdoPositionalParameterIndex];
                        unset($bind[$pdoPositionalParameterIndex]);
                        $this->deleteBindParams($pdoPositionalParameterIndex);
                        $pdoPositionalParameterIndex++;
                    } else {
                        $e = 'PDO positional parameters not match with bind data.';

                        throw new InvalidArgumentException($e);
                    }
                } else {
                    $key = 'pdonamedparameter_'.$key;
                }

                if ($index > 0) {
                    $key .= '_'.$index;
                }

                $values[] = ':'.($key = $this->generateBindParams($key));
                $this->bind($key, $value);
            }
        }

        return [$fields, $values, $bind];
    }

    /**
     * SQL 表达式格式化.
     */
    protected function normalizeExpression(string $sql, string $tableName): string
    {
        preg_match_all('/\[[a-z][a-z0-9_\.]*\]|\[\*\]/i', $sql, $matches, PREG_OFFSET_CAPTURE);
        $matches = reset($matches);
        $out = '';
        $offset = 0;
        foreach ($matches as $value) {
            $length = strlen($value[0]);
            $field = substr($value[0], 1, $length - 2);
            $tmp = explode('.', $field);
            switch (count($tmp)) {
                case 2:
                    $field = $tmp[1];
                    $table = $tmp[0];

                    break;
                default:
                    $field = $tmp[0];
                    $table = $tableName;
            }

            $field = $this->normalizeTableOrColumn("{$table}.{$field}");
            $out .= substr($sql, $offset, $value[1] - $offset).$field;
            $offset = $value[1] + $length;
        }

        $out .= substr($sql, $offset);

        return $out;
    }

    /**
     * 表或者字段格式化（支持别名）.
     */
    protected function normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null): string
    {
        $names = explode('.', str_replace('`', '', $name));
        foreach ($names as $offset => $v) {
            if (empty($v)) {
                unset($names[$offset]);
            } else {
                $names[$offset] = $this->connect->identifierColumn($v);
            }
        }
        $name = implode('.', $names);

        if ($alias) {
            return "{$name} ".($as ? $as.' ' : '').$this->connect->identifierColumn($alias);
        }

        return $name;
    }

    /**
     * 字段格式化.
     */
    protected function normalizeTableColumn(string $key, string $tableName): string
    {
        return $this->normalizeTableOrColumn("{$tableName}.{$key}");
    }

    /**
     * 设置当前表名字.
     */
    protected function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * 获取当前表名字.
     */
    protected function getTable(): string
    {
        return $this->table;
    }

    /**
     * 设置是否为表操作.
     */
    protected function setIsTable(bool $isTable = true): void
    {
        $this->isTable = $isTable;
    }

    /**
     * 返回是否为表操作.
     */
    protected function getIsTable(): bool
    {
        return $this->isTable;
    }

    /**
     * 解析时间信息.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseTime(string $field, mixed $value, string $type): mixed
    {
        $field = str_replace('`', '', $field);
        $table = $this->getTable();

        if (!preg_match('/\(.*\)/', $field)) {
            if (preg_match('/(.+)\.(.+)/', $field, $matches)) {
                $table = $matches[1];
                $field = $matches[2];
            }
        }

        // 支持类型
        switch ($type) {
            case 'day':
                $value = (int) $value;
                if ($value > 31) {
                    $e = sprintf('Days can only be less than 31,but %s given.', $value);

                    throw new InvalidArgumentException($e);
                }

                $date = getdate();
                $value = mktime(0, 0, 0, $date['mon'], $value, $date['year']);

                break;
            case 'month':
                $value = (int) $value;
                if ($value > 12) {
                    $e = sprintf('Months can only be less than 12,but %s given.', $value);

                    throw new InvalidArgumentException($e);
                }

                $date = getdate();
                $value = mktime(0, 0, 0, $value, 1, $date['year']);

                break;
            case 'year':
                $value = mktime(0, 0, 0, 1, 1, (int) $value);

                break;
            case 'date':
            default:
                $value = strtotime($value);
                if (false === $value) {
                    $e = 'Please enter a right time of strtotime.';

                    throw new InvalidArgumentException($e);
                }

                break;
        }

        return $value;
    }

    /**
     * 设置当前是否处于时间条件状态.
     */
    protected function setInTimeCondition(?string $inTimeCondition = null): void
    {
        $this->inTimeCondition = $inTimeCondition;
    }

    /**
     * 返回当前是否处于时间条件状态.
     */
    protected function getInTimeCondition(): ?string
    {
        return $this->inTimeCondition;
    }

    /**
     * 初始化查询条件.
     */
    protected function initOption(): void
    {
        $this->options = static::$optionsDefault;
    }
}

// import fn.
class_exists(normalize::class);
