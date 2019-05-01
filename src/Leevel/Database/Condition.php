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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database;

use Closure;
use InvalidArgumentException;
use Leevel\Flow\FlowControl;
use function Leevel\Support\Arr\normalize;
use Leevel\Support\Arr\normalize;
use PDO;

/**
 * 条件构造器从 select 分离出来.
 *
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.26
 *
 * @version 1.0
 */
class Condition
{
    use FlowControl;

    /**
     * And 逻辑运算符.
     *
     * @var string
     */
    const LOGIC_AND = 'and';

    /**
     * Or 逻辑运算符.
     *
     * @var string
     */
    const LOGIC_OR = 'or';

    /**
     * 逻辑分组左符号.
     *
     * @var string
     */
    const LOGIC_GROUP_LEFT = '(';

    /**
     * 逻辑分组右符号.
     *
     * @var string
     */
    const LOGIC_GROUP_RIGHT = ')';

    /**
     * 子表达式默认别名.
     *
     * @var string
     */
    const DEFAULT_SUBEXPRESSION_ALIAS = 'a';

    /**
     * 条件逻辑连接符.
     *
     * @var string
     */
    public $conditionLogic = 'and';
    /**
     * 数据库连接.
     *
     * @var Leevel\Database\IConnect
     */
    protected $connect;

    /**
     * 绑定参数.
     *
     * @var array
     */
    protected $bindParams = [];

    /**
     * 连接参数.
     *
     * @var array
     */
    protected $options = [];

    /**
     * 支持的聚合类型.
     *
     * @var array
     */
    protected static $aggregateTypes = [
        'COUNT' => 'COUNT',
        'MAX'   => 'MAX',
        'MIN'   => 'MIN',
        'AVG'   => 'AVG',
        'SUM'   => 'SUM',
    ];

    /**
     * 支持的 union 类型.
     *
     * @var array
     */
    protected static $unionTypes = [
        'UNION'     => 'UNION',
        'UNION ALL' => 'UNION ALL',
    ];

    /**
     * 支持的 index 类型.
     *
     * @var array
     */
    protected static $indexTypes = [
        'FORCE'  => 'FORCE',
        'IGNORE' => 'IGNORE',
    ];

    /**
     * 连接参数.
     *
     * @var array
     */
    protected static $optionsDefault = [
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
    ];

    /**
     * 条件逻辑类型.
     *
     * @var string
     */
    protected $conditionType = 'where';

    /**
     * 当前表信息.
     *
     * @var string
     */
    protected $table = '';

    /**
     * 是否为表操作.
     *
     * @var bool
     */
    protected $isTable = false;

    /**
     * 主表别名.
     *
     * @var string
     */
    protected $alias = '';

    /**
     * 是否处于时间功能状态
     *
     * @var string
     */
    protected $inTimeCondition;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\IConnect $connect
     */
    public function __construct(IConnect $connect)
    {
        $this->connect = $connect;

        $this->initOption();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if ($this->placeholderFlowControl($method)) {
            return $this;
        }

        if (0 === strpos($method, 'where') &&
            false !== ($result = $this->callWhereSugar($method, $args))) {
            return $result;
        }

        if (0 === strpos($method, 'having') &&
            false !== ($result = $this->callHavingSugar($method, $args))) {
            return $result;
        }

        if (false === strpos($method, 'join') &&
            false !== ($result = $this->callJoinSugar($method, $args))) {
            return $result;
        }

        throw new ConditionNotFoundException(
            sprintf('Condition method %s not found.', $method)
        );
    }

    /**
     * 插入数据 insert (支持原生 sql).
     *
     * @param array|string $data
     * @param array        $bind
     * @param bool         $replace
     *
     * @return array
     */
    public function insert($data, array $bind = [], bool $replace = false): array
    {
        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据插入
        if (is_array($data)) {
            $questionMark = 0;
            list($fields, $values, $bind, $questionMark) = $this->normalizeBindData($data, $bind, $questionMark);
            $tableName = $this->getTable();

            foreach ($fields as $key => $field) {
                $fields[$key] = $this->normalizeColumn($field, $tableName);
            }

            // 构造 insert 语句
            if ($values) {
                $sql = [];
                $sql[] = ($replace ? 'REPLACE' : 'INSERT').' INTO';
                $sql[] = $this->parseTable();
                $sql[] = '('.implode(',', $fields).')';
                $sql[] = 'VALUES';
                $sql[] = '('.implode(',', $values).')';
                $data = implode(' ', $sql);

                unset($fields, $values, $sql);
            }
        }

        $bind = array_merge($this->getBindParams(), $bind);

        return [
            $replace ? 'replace' : 'insert',
            $data,
            $bind,
        ];
    }

    /**
     * 批量插入数据 insertAll.
     *
     * @param array $data
     * @param array $bind
     * @param bool  $replace
     *
     * @return array
     */
    public function insertAll(array $data, array $bind = [], bool $replace = false): array
    {
        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据批量插入
        if (is_array($data)) {
            $dataResult = [];
            $questionMark = 0;
            $tableName = $this->getTable();

            foreach ($data as $key => $tmp) {
                if (!is_array($tmp) || count($tmp) !== count($tmp, 1)) {
                    throw new InvalidArgumentException('Data for insertAll is not invalid.');
                }

                list($tmpFields, $values, $bind, $questionMark) = $this->normalizeBindData($tmp, $bind, $questionMark, $key);

                if (0 === $key) {
                    $fields = $tmpFields;

                    foreach ($fields as $fieldKey => $field) {
                        $fields[$fieldKey] = $this->normalizeColumn($field, $tableName);
                    }
                }

                if ($values) {
                    $dataResult[] = '('.implode(',', $values).')';
                }
            }

            // 构造 insertAll 语句
            if ($dataResult) {
                $sql = [];
                $sql[] = ($replace ? 'REPLACE' : 'INSERT').' INTO';
                $sql[] = $this->parseTable();
                $sql[] = '('.implode(',', $fields).')';
                $sql[] = 'VALUES';
                $sql[] = implode(',', $dataResult);
                $data = implode(' ', $sql);

                unset($fields, $values, $sql, $dataResult);
            }
        }

        $bind = array_merge($this->getBindParams(), $bind);

        return [
            $replace ? 'replace' : 'insert',
            $data,
            $bind,
        ];
    }

    /**
     * 更新数据 update (支持原生 sql).
     *
     * @param array|string $data
     * @param array        $bind
     *
     * @return array
     */
    public function update($data, array $bind = []): array
    {
        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据更新
        if (is_array($data)) {
            $questionMark = 0;
            list($fields, $values, $bind, $questionMark) = $this->normalizeBindData($data, $bind, $questionMark);
            $tableName = $this->getTable();

            // SET 语句
            $setData = [];

            foreach ($fields as $key => $field) {
                $field = $this->normalizeColumn($field, $tableName);
                $setData[] = $field.' = '.$values[$key];
            }

            // 构造 update 语句
            if ($values) {
                $sql = [];
                $sql[] = 'UPDATE';
                $sql[] = ltrim($this->parseFrom(), 'FROM ');
                $sql[] = 'SET '.implode(',', $setData);
                $sql[] = $this->parseWhere();
                $sql[] = $this->parseOrder();
                $sql[] = $this->parseLimitCount();
                $sql[] = $this->parseForUpdate();
                $sql = array_filter($sql);
                $data = implode(' ', $sql);

                unset($bindData, $fields, $values, $setData, $sql);
            }
        }

        $bind = array_merge($this->getBindParams(), $bind);

        return [
            'update',
            $data,
            $bind,
        ];
    }

    /**
     * 删除数据 delete (支持原生 sql).
     *
     * @param null|string $data
     * @param array       $bind
     *
     * @return array
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

            $sql = array_filter($sql);
            $data = implode(' ', $sql);

            unset($sql);
        }

        $bind = array_merge($this->getBindParams(), $bind);

        return [
            'delete',
            $data,
            $bind,
        ];
    }

    /**
     * 清空表重置自增 ID.
     *
     * @return array
     */
    public function truncate(): array
    {
        // 构造 truncate 语句
        $sql = [];
        $sql[] = 'TRUNCATE TABLE';
        $sql[] = $this->parseTable();
        $sql = implode(' ', $sql);

        return [
            'statement',
            $sql,
        ];
    }

    /**
     * 根据分页设置条件.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return $this
     */
    public function forPage(int $page, int $perPage = 15): self
    {
        return $this->limit(($page - 1) * $perPage, $perPage);
    }

    /**
     * 时间控制语句开始.
     *
     * @param string $type
     *
     * @return $this
     */
    public function time(string $type = 'date'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (!in_array($type, ['date', 'month', 'day', 'year'], true)) {
            throw new InvalidArgumentException(
                sprintf('Time type `%s` is invalid.', $type)
            );
        }

        $this->setInTimeCondition($type);

        return $this;
    }

    /**
     * 时间控制语句结束.
     *
     * @return $this
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
     *
     * @param null|string $option
     *
     * @return $this
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
     * prefix 查询.
     *
     * @param string $prefix
     *
     * @return $this
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
     *
     * @param mixed        $table
     * @param array|string $cols
     *
     * @return $this
     */
    public function table($table, $cols = '*'): self
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
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * 添加字段.
     *
     * @param mixed  $cols
     * @param string $table
     *
     * @return $this
     */
    public function columns($cols = '*', ?string $table = null): self
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
     *
     * @param mixed  $cols
     * @param string $table
     *
     * @return $this
     */
    public function setColumns($cols = '*', ?string $table = null): self
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
     * where 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function where(...$arr): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_AND);
        array_unshift($arr, 'where');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * orWhere 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function orWhere(...$arr): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_OR);
        array_unshift($arr, 'where');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * Where 原生查询.
     *
     * @param string $raw
     *
     * @return $this
     */
    public function whereRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $condition = [];
        array_unshift($condition, static::LOGIC_AND);
        array_unshift($condition, 'where');
        $condition[] = [':stringSimple' => '{'.$raw.'}'];

        return $this->aliatypeAndLogic(...$condition);
    }

    /**
     * Where 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return $this
     */
    public function orWhereRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $condition = [];
        array_unshift($condition, static::LOGIC_OR);
        array_unshift($condition, 'where');
        $condition[] = [':stringSimple' => '{'.$raw.'}'];

        return $this->aliatypeAndLogic(...$condition);
    }

    /**
     * exists 方法支持
     *
     * @param mixed $exists
     *
     * @return $this
     */
    public function whereExists($exists): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addConditions([
            ':exists' => $exists,
        ]);
    }

    /**
     * not exists 方法支持
     *
     * @param mixed $exists
     *
     * @return $this
     */
    public function whereNotExists($exists): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addConditions([
            ':notexists' => $exists,
        ]);
    }

    /**
     * 参数绑定支持
     *
     * @param mixed $names
     * @param mixed $value
     * @param int   $type
     *
     * @return $this
     */
    public function bind($names, $value = null, int $type = PDO::PARAM_STR): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (is_array($names)) {
            foreach ($names as $key => $item) {
                if (!is_array($item)) {
                    $item = [
                        $item,
                        $type,
                    ];
                }

                $this->bindParams[$key] = $item;
            }
        } else {
            if (!is_array($value)) {
                $value = [
                    $value,
                    $type,
                ];
            }

            $this->bindParams[$names] = $value;
        }

        return $this;
    }

    /**
     * index 强制索引（或者忽略索引）.
     *
     * @param array|string $indexs
     * @param string       $type
     *
     * @return $this
     */
    public function forceIndex($indexs, $type = 'FORCE'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (!isset(static::$indexTypes[$type])) {
            throw new InvalidArgumentException(
                sprintf('Invalid Index type `%s`.', $type)
            );
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
     *
     * @param array|string $indexs
     *
     * @return $this
     */
    public function ignoreIndex($indexs): self
    {
        return $this->forceIndex($indexs, 'IGNORE');
    }

    /**
     * join 查询.
     *
     * @param mixed        $table 同 table $table
     * @param array|string $cols  同 table $cols
     * @param mixed        $cond  同 where $cond
     *
     * @return $this
     */
    public function join($table, $cols, $cond): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'inner join');

        return $this->addJoin(...$args);
    }

    /**
     * 添加一个 UNION 查询.
     *
     * @param array|callable|string $selects
     * @param string                $type
     *
     * @return $this
     */
    public function union($selects, string $type = 'UNION'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (!isset(static::$unionTypes[$type])) {
            throw new InvalidArgumentException(
                sprintf('Invalid UNION type `%s`.', $type)
            );
        }

        if (!is_array($selects)) {
            $selects = [
                $selects,
            ];
        }

        foreach ($selects as $tmp) {
            $this->options['union'][] = [
                $tmp,
                $type,
            ];
        }

        return $this;
    }

    /**
     * 添加一个 UNION ALL 查询.
     *
     * @param array|callable|string $selects
     *
     * @return $this
     */
    public function unionAll($selects): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->union($selects, 'UNION ALL');
    }

    /**
     * 指定 GROUP BY 子句.
     *
     * @param array|string $expression
     *
     * @return $this
     */
    public function groupBy($expression): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (is_string($expression) &&
            false !== strpos($expression, ',') &&
            false !== strpos($expression, '{') &&
            preg_match_all('/{(.+?)}/', $expression, $matches)) {
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
                $expression[
                    array_search('{'.base64_encode($tmp).'}', $expression, true)
                ] = '{'.$tmp.'}';
            }
        }

        $currentTableName = $this->getTable();

        foreach ($expression as $value) {
            // 处理条件表达式
            if (is_string($value) &&
                false !== strpos($value, ',') &&
                false !== strpos($value, '{') &&
                preg_match_all('/{(.+?)}/', $value, $subMatches)) {
                $value = str_replace(
                    $subMatches[1][0],
                    base64_encode($subMatches[1][0]),
                    $value
                );
            }

            $value = normalize($value);

            // 还原
            if (!empty($subMatches)) {
                foreach ($subMatches[1] as $tmp) {
                    $value[
                        array_search('{'.base64_encode($tmp).'}', $value, true)
                    ] = '{'.$tmp.'}';
                }
            }

            foreach ($value as $tmp) {
                if (preg_match('/(.+)\.(.+)/', $tmp, $matches)) {
                    $currentTableName = $matches[1];
                    $tmp = $matches[2];
                }

                // 表达式支持
                $tmp = $this->normalizeColumn($tmp, $currentTableName);

                $this->options['group'][] = $tmp;
            }
        }

        return $this;
    }

    /**
     * 添加一个 HAVING 条件
     * < 参数规范参考 where()方法 >.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function having(...$arr): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_AND);
        array_unshift($arr, 'having');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * orHaving 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function orHaving(...$arr): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_OR);
        array_unshift($arr, 'having');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * Having 原生查询.
     *
     * @param string $raw
     *
     * @return $this
     */
    public function havingRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $condition = [];
        array_unshift($condition, static::LOGIC_AND);
        array_unshift($condition, 'having');
        $condition[] = [':stringSimple' => '{'.$raw.'}'];

        return $this->aliatypeAndLogic(...$condition);
    }

    /**
     * Having 原生 OR 查询.
     *
     * @param string $raw
     *
     * @return $this
     */
    public function orHavingRaw(string $raw): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $condition = [];
        array_unshift($condition, static::LOGIC_OR);
        array_unshift($condition, 'having');
        $condition[] = [':stringSimple' => '{'.$raw.'}'];

        return $this->aliatypeAndLogic(...$condition);
    }

    /**
     * 添加排序.
     *
     * @param array|string $expression
     * @param string       $orderDefault
     *
     * @return $this
     */
    public function orderBy($expression, string $orderDefault = 'ASC'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        // 格式化为大写
        $orderDefault = strtoupper($orderDefault);

        // 处理条件表达式
        if (is_string($expression) &&
            false !== strpos($expression, ',') &&
            false !== strpos($expression, '{') &&
            preg_match_all('/{(.+?)}/', $expression, $matches)) {
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
                $expression[
                    array_search('{'.base64_encode($tmp).'}', $expression, true)
                ] = '{'.$tmp.'}';
            }
        }

        $tableName = $this->getTable();

        foreach ($expression as $value) {
            // 处理条件表达式
            if (is_string($value) &&
                false !== strpos($value, ',') &&
                false !== strpos($value, '{') &&
                preg_match_all('/{(.+?)}/', $value, $subMatches)) {
                $value = str_replace(
                    $subMatches[1][0],
                    base64_encode($subMatches[1][0]),
                    $value
                );
            }

            $value = normalize($value);

            // 还原
            if (!empty($subMatches)) {
                foreach ($subMatches[1] as $tmp) {
                    $value[
                        array_search('{'.base64_encode($tmp).'}', $value, true)
                    ] = '{'.$tmp.'}';
                }
            }

            foreach ($value as $tmp) {
                // 表达式支持
                if (false !== strpos($tmp, '{') &&
                    preg_match('/^{(.+?)}$/', $tmp, $threeMatches)) {
                    $tmp = $this->connect->normalizeExpression(
                        $threeMatches[1], $tableName
                    );

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

                        $tmp = $this->connect->normalizeTableOrColumn(
                            "{$currentTableName}.{$tmp}"
                        );
                    }

                    $this->options['order'][] = $tmp.' '.$sort;
                }
            }
        }

        return $this;
    }

    /**
     * 最近排序数据.
     *
     * @param string $field
     *
     * @return $this
     */
    public function latest(string $field = 'create_at'): self
    {
        return $this->orderBy($field, 'DESC');
    }

    /**
     * 最早排序数据.
     *
     * @param string $field
     *
     * @return $this
     */
    public function oldest(string $field = 'create_at'): self
    {
        return $this->orderBy($field, 'ASC');
    }

    /**
     * 创建一个 SELECT DISTINCT 查询.
     *
     * @param bool $flag 指示是否是一个 SELECT DISTINCT 查询（默认 true）
     *
     * @return $this
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
     *
     * @param string $field
     * @param string $alias
     *
     * @return $this
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
     *
     * @param string $field
     * @param string $alias
     *
     * @return $this
     */
    public function avg(string $field, string $alias = 'avg_value'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addAggregate('AVG', $field, $alias);
    }

    /**
     * 最大值
     *
     * @param string $field
     * @param string $alias
     *
     * @return $this
     */
    public function max(string $field, string $alias = 'max_value'): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->addAggregate('MAX', $field, $alias);
    }

    /**
     * 最小值
     *
     * @param string $field
     * @param string $alias
     *
     * @return $this
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
     *
     * @param string $field
     * @param string $alias
     *
     * @return $this
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
     *
     * @return $this
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
     *
     * @return $this
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
     *
     * @param int $count
     *
     * @return $this
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
     *
     * @param int $offset
     * @param int $count
     *
     * @return $this
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
     * 是否构造一个 FOR UPDATE 查询.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function forUpdate(bool $flag = true): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->options['forUpdate'] = $flag;

        return $this;
    }

    /**
     * 获得查询字符串.
     *
     * @param bool $withLogicGroup
     *
     * @return string
     */
    public function makeSql(bool $withLogicGroup = false): string
    {
        $sql = [
            'SELECT',
        ];

        foreach (array_keys($this->options) as $option) {
            if ('from' === $option) {
                $sql['from'] = '';
            } elseif ('union' === $option) {
                continue;
            } else {
                $method = 'parse'.ucfirst($option);

                if (method_exists($this, $method)) {
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
            return static::LOGIC_GROUP_LEFT.$result.static::LOGIC_GROUP_RIGHT;
        }

        return $result;
    }

    /**
     * 设置查询参数.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value): self
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * 返回查询参数.
     *
     * @return array
     */
    public function getOption(): array
    {
        return $this->options;
    }

    /**
     * 返回参数绑定.
     *
     * @return array
     */
    public function getBindParams(): array
    {
        return $this->bindParams;
    }

    /**
     * 调用 where 语法糖.
     *
     * @param string $method
     * @param array  $args
     *
     * @return $this|false
     */
    protected function callWhereSugar(string $method, array $args)
    {
        if (!in_array($method, [
            'whereNotBetween', 'whereBetween',
            'whereNotNull', 'whereNull',
            'whereNotIn', 'whereIn',
            'whereNotLike', 'whereLike',
            'whereDate', 'whereDay',
            'whereMonth', 'whereYear',
        ], true)) {
            return false;
        }

        if ($this->checkFlowControl()) {
            return $this;
        }

        if (in_array($method, ['whereDate', 'whereDay', 'whereMonth', 'whereYear'], true)) {
            $this->setInTimeCondition(strtolower(substr($method, 5)));
            $this->where(...$args);
            $this->setInTimeCondition(null);

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
     * 调用 having 语法糖.
     *
     * @param string $method
     * @param array  $args
     *
     * @return $this|false
     */
    protected function callHavingSugar(string $method, array $args)
    {
        if (!in_array($method, [
            'havingNotBetween', 'havingBetween',
            'havingNotNull', 'havingNull',
            'havingNotIn', 'havingIn',
            'havingNotLike', 'havingLike',
            'havingDate', 'havingDay',
            'havingMonth', 'havingYear',
        ], true)) {
            return false;
        }

        if ($this->checkFlowControl()) {
            return $this;
        }

        if (in_array($method, ['havingDate', 'havingDay', 'havingMonth', 'havingYear'], true)) {
            $this->setInTimeCondition(strtolower(substr($method, 6)));
            $this->having(...$args);
            $this->setInTimeCondition(null);

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
     * 调用 join 语法糖.
     *
     * @param string $method
     * @param array  $args
     *
     * @return $this|false
     */
    protected function callJoinSugar(string $method, array $args)
    {
        if (!in_array($method, [
            'innerJoin', 'leftJoin',
            'rightJoin', 'fullJoin',
            'crossJoin', 'naturalJoin',
        ], true)) {
            return false;
        }

        if ($this->checkFlowControl()) {
            return $this;
        }

        $type = substr($method, 0, -4).' join';

        array_unshift($args, $type);

        return $this->addJoin(...$args);
    }

    /**
     * 解析 prefix 分析结果.
     *
     * @return string
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
     *
     * @return string
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
     *
     * @return string
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
            if (false !== strpos($col, '{') &&
                preg_match('/^{(.+?)}$/', $col, $matches)) {
                $columns[] = $this->connect->normalizeExpression(
                    $matches[1],
                    $tableName
                );
            } else {
                if ('*' !== $col && $alias) {
                    $columns[] = $this->connect->normalizeTableOrColumn(
                        "{$tableName}.{$col}", $alias, 'AS');
                } else {
                    $columns[] = $this->connect->normalizeTableOrColumn(
                        "{$tableName}.{$col}"
                    );
                }
            }
        }

        return implode(',', $columns);
    }

    /**
     * 解析 aggregate 分析结果.
     *
     * @return string
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
     *
     * @return string
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
                $tmp .= $this->connect->normalizeTableOrColumn(
                    "{$value['schema']}.{$value['table_name']}"
                );
            } else {
                $tmp .= $this->connect->normalizeTableOrColumn(
                    "{$value['schema']}.{$value['table_name']}",
                    $alias
                );
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
     *
     * @return string
     */
    protected function parseTable(): string
    {
        foreach ($this->options['from'] as $alias => $value) {
            if ($alias === $value['table_name']) {
                $alias = $this->connect->normalizeTableOrColumn(
                    "{$value['schema']}.{$value['table_name']}"
                );
            }

            break;
        }

        return $alias;
    }

    /**
     * 解析 index 分析结果.
     *
     * @return string
     */
    protected function parseIndex(): string
    {
        $index = '';

        foreach ([
            'FORCE',
            'IGNORE',
        ] as $type) {
            if (empty($this->options['index'][$type])) {
                continue;
            }

            $index .= ($index ? ' ' : '').
                $type.' INDEX('.
                implode(',', $this->options['index'][$type]).
                ')';
        }

        return $index;
    }

    /**
     * 解析 where 分析结果.
     *
     * @param bool $child
     *
     * @return string
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
     *
     * @return string
     */
    protected function parseUnion(): string
    {
        if (empty($this->options['union'])) {
            return '';
        }

        $sql = '';

        if ($this->options['union']) {
            $options = count($this->options['union']);

            foreach ($this->options['union'] as $index => $value) {
                list($union, $type) = $value;

                if ($union instanceof self || $union instanceof Select) {
                    $union = $union->makeSql();
                }

                if ($index <= $options - 1) {
                    $sql .= "\n".$type.' '.$union;
                }
            }
        }

        return $sql;
    }

    /**
     * 解析 order 分析结果.
     *
     * @return string
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
     *
     * @return string
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
     *
     * @param bool $child
     *
     * @return string
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
     *
     * @param bool $withoutOffset
     *
     * @return string
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
     * 解析 forUpdate 分析结果.
     *
     * @return string
     */
    protected function parseForUpdate(): string
    {
        if (!$this->options['forUpdate']) {
            return '';
        }

        return 'FOR UPDATE';
    }

    /**
     * 解析 condition　条件（包括 where,having）.
     *
     * @param string $condType
     * @param bool   $child
     *
     * @return string
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
                if (false !== strpos($cond[0], '{') &&
                    preg_match('/^{(.+?)}$/', $cond[0], $matches)) {
                    $cond[0] = $this->connect->normalizeExpression(
                        $matches[1],
                        $table
                    );
                } else {
                    // 字段处理
                    if (false !== strpos($cond[0], '.')) {
                        $tmp = explode('.', $cond[0]);
                        $currentTable = $tmp[0];
                        $cond[0] = $tmp[1];
                    } else {
                        $currentTable = $table;
                    }

                    $cond[0] = $this->connect->normalizeColumn(
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
                if (isset($cond[2])) {
                    $isArray = true;

                    if (!is_array($cond[2])) {
                        $cond[2] = [$cond[2]];
                        $isArray = false;
                    }

                    foreach ($cond[2] as $condKey => $tmp) {
                        // 对象子表达式支持
                        if (is_object($tmp) && ($tmp instanceof self || $tmp instanceof Select)) {
                            $tmp = $tmp instanceof Select ? $tmp->databaseCondition()->makeSql(true) : $tmp->makeSql(true);
                        }

                        // 回调方法子表达式支持
                        elseif (is_object($tmp) && $tmp instanceof Closure) {
                            $select = new static($this->connect);
                            $select->setTable($this->getTable());
                            call_user_func_array($tmp, [
                                $select,
                            ]);

                            $tmp = $select->makeSql(true);
                        }

                        // 字符串子表达式支持
                        elseif (is_string($tmp) && 0 === strpos($tmp, '(')) {
                        }

                        // 表达式支持
                        elseif (is_string($tmp) &&
                            false !== strpos($tmp, '{') &&
                            preg_match('/^{(.+?)}$/', $tmp, $matches)) {
                            $tmp = $this->connect->normalizeExpression(
                                $matches[1],
                                $table
                            );
                        } else {
                            // 自动格式化时间
                            if (null !== $findTime) {
                                $tmp = $this->parseTime($cond[0], $tmp, $findTime);
                            }

                            $tmp = $this->connect->normalizeColumnValue($tmp);
                        }

                        $cond[2][$condKey] = $tmp;
                    }

                    if (false === $isArray ||
                        (1 === count($cond[2]) && 0 === strpos((string) ($cond[2][0]), '('))) {
                        $cond[2] = reset($cond[2]);
                    }
                }

                // 拼接结果
                if (in_array($cond[1], ['null', 'not null'], true)) {
                    $sqlCond[] = $cond[0].' IS '.strtoupper($cond[1]);
                } elseif (in_array($cond[1], ['in', 'not in'], true)) {
                    $sqlCond[] = $cond[0].' '.
                        strtoupper($cond[1]).' '.
                        (
                            is_array($cond[2]) ?
                            '('.implode(',', $cond[2]).')' :
                            $cond[2]
                        );
                } elseif (in_array($cond[1], ['between', 'not between'], true)) {
                    if (!is_array($cond[2]) || count($cond[2]) < 2) {
                        $e =
                            'The [not] between parameter value must be '.
                            'an array which not less than two elements.';

                        throw new InvalidArgumentException($e);
                    }

                    $sqlCond[] = $cond[0].' '.strtoupper($cond[1]).' '.$cond[2][0].' AND '.$cond[2][1];
                } elseif (is_scalar($cond[2])) {
                    $sqlCond[] = $cond[0].' '.strtoupper($cond[1]).' '.$cond[2];
                }
            }
        }

        // 剔除第一个逻辑符
        array_shift($sqlCond);

        return (false === $child ? strtoupper($condType).' ' : '').
            implode(' ', $sqlCond);
    }

    /**
     * 别名条件.
     *
     * @param string $conditionType
     * @param mixed  $cond
     *
     * @return $this
     */
    protected function aliasCondition(string $conditionType, $cond): self
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
     * @param string $type
     * @param string $logic
     * @param mixed  $cond
     *
     * @return $this
     *
     * @todo 代码复杂度过高，需要重构
     */
    protected function aliatypeAndLogic(string $type, string $logic, $cond): self
    {
        $this->setTypeAndLogic($type, $logic);

        if ($cond instanceof Closure) {
            $select = new static($this->connect);
            $select->setTable($this->getTable());
            call_user_func_array($cond, [
                $select,
            ]);

            $tmp = $select->{'parse'.ucwords($type)}(true);
            $this->setConditionItem(static::LOGIC_GROUP_LEFT.$tmp.static::LOGIC_GROUP_RIGHT, ':string');

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
     * @return $this
     */
    protected function addConditions(): self
    {
        $args = func_get_args();
        $table = $this->getTable();

        // 整理多个参数到二维数组
        if (!is_array($args[0])) {
            $conditions = [$args];
        } else {
            // 一维数组统一成二维数组格式
            $oneImension = false;

            foreach ($args[0] as $key => $value) {
                if (is_int($key) && !is_array($value)) {
                    $oneImension = true;
                }

                break;
            }

            if (true === $oneImension) {
                $conditions = [$args[0]];
            } else {
                $conditions = $args[0];
            }
        }

        // 遍历数组拼接结果
        foreach ($conditions as $key => $tmp) {
            if (!is_int($key)) {
                $key = trim($key);
            }

            // 字符串表达式
            if (is_string($key) && in_array($key, [':string', ':stringSimple'], true)) {
                // 不符合规则抛出异常
                if (!is_string($tmp)) {
                    $e = sprintf('String type only supports string,but %s given.', gettype($tmp));

                    throw new InvalidArgumentException($e);
                }

                // 表达式支持
                if (false !== strpos($tmp, '{') &&
                    preg_match('/^{(.+?)}$/', $tmp, $matches)) {
                    $tmp = $this->connect->normalizeExpression(
                        $matches[1],
                        $table
                    );
                }

                $this->setConditionItem($tmp, $key);
            }

            // 子表达式
            elseif (is_string($key) && in_array($key, [
                ':subor',
                ':suband',
            ], true)) {
                $typeAndLogic = $this->getTypeAndLogic();

                $select = new static($this->connect);
                $select->setTable($this->getTable());
                $select->setTypeAndLogic($typeAndLogic[0]);

                // 逻辑表达式
                if (isset($tmp[':logic'])) {
                    if (strtolower($tmp[':logic']) === static::LOGIC_OR) {
                        $select->setTypeAndLogic(null, static::LOGIC_OR);
                    }

                    unset($tmp[':logic']);
                }

                $select = $select->addConditions($tmp);

                // 解析结果
                $parseType = 'parse'.ucwords($typeAndLogic[0]);
                $oldLogic = $typeAndLogic[1];

                $this->setTypeAndLogic(null, ':subor' ? static::LOGIC_OR : static::LOGIC_AND);

                $this->setConditionItem(static::LOGIC_GROUP_LEFT.$select->{$parseType}(true).
                    static::LOGIC_GROUP_RIGHT, ':string');

                $this->setTypeAndLogic(null, $oldLogic);
            }

            // exists 支持
            elseif (is_string($key) && in_array($key, [
                ':exists',
                ':notexists',
            ], true)) {
                // having 不支持 [not] exists
                if ('having' === $this->getTypeAndLogic()[0]) {
                    $e = 'Having do not support [not] exists.';

                    throw new InvalidArgumentException($e);
                }

                if (is_object($tmp) && ($tmp instanceof self || $tmp instanceof Select)) {
                    $tmp = $tmp instanceof Select ? $tmp->databaseCondition()->makeSql() : $tmp->makeSql();
                } elseif (is_object($tmp) && $tmp instanceof Closure) {
                    $select = new static($this->connect);
                    $select->setTable($this->getTable());
                    call_user_func_array($tmp, [
                        $select,
                    ]);

                    $tmp = $select->makeSql();
                }

                $tmp = (':notexists' === $key ? 'NOT EXISTS ' : 'EXISTS ').
                    static::LOGIC_GROUP_LEFT.
                    $tmp.
                    static::LOGIC_GROUP_RIGHT;

                $this->setConditionItem($tmp, ':string');
            }

            // 其它
            else {
                // 处理字符串 "null"
                if (is_scalar($tmp)) {
                    $tmp = (array) $tmp;
                }

                // 合并字段到数组
                if (is_string($key)) {
                    array_unshift($tmp, $key);
                }

                // 处理默认 “=” 的类型
                if (2 === count($tmp) && !in_array($tmp[1], [
                    'null',
                    'not null',
                ], true)) {
                    $tmp[2] = $tmp[1];
                    $tmp[1] = '=';
                }

                // 字段
                $tmp[1] = trim($tmp[1]);

                // 特殊类型
                if (in_array($tmp[1], [
                    'between',
                    'not between',
                    'in',
                    'not in',
                    'null',
                    'not null',
                ], true)) {
                    if (isset($tmp[2]) && is_string($tmp[2])) {
                        $tmp[2] = explode(',', $tmp[2]);
                    }

                    $this->setConditionItem([$tmp[0], $tmp[1], $tmp[2] ?? null]);
                }

                // 普通类型
                else {
                    $this->setConditionItem($tmp);
                }
            }
        }

        return $this;
    }

    /**
     * 设置条件的一项.
     *
     * @param array|string $items
     * @param string       $type
     */
    protected function setConditionItem($items, string $type = ''): void
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
     *
     * @param string $type
     * @param string $logic
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
     *
     * @return array
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
     *
     * @param string $field
     * @param string $tableName
     *
     * @return string
     */
    protected function normalizeColumn(string $field, string $tableName): string
    {
        if (false !== strpos($field, '{') &&
            preg_match('/^{(.+?)}$/', $field, $matches)) {
            $field = $this->connect->normalizeExpression(
                $matches[1],
                $tableName
            );
        } elseif (!preg_match('/\(.*\)/', $field)) {
            if (preg_match('/(.+)\.(.+)/', $field, $matches)) {
                $currentTableName = $matches[1];
                $tmp = $matches[2];
            } else {
                $currentTableName = $tableName;
            }

            $field = $this->connect->normalizeTableOrColumn(
                "{$currentTableName}.{$field}"
            );
        }

        return $field;
    }

    /**
     * 连表 join 操作.
     *
     * @param string                                                                   $joinType
     * @param array|\Closure|\Leevel\Database\Condition|\Leevel\Database\Select|string $names
     * @param mixed                                                                    $cols
     * @param mixed                                                                    $cond
     *
     * @return $this
     */
    protected function addJoin(string $joinType, $names, $cols, $cond = null): self
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

        if (is_object($names) && ($names instanceof self || $names instanceof Select)) { // 对象子表达式
            $table = $names->makeSql(true);

            if (!$alias) {
                $alias = $names instanceof Select ? $names->databaseCondition()->getAlias() : $names->getAlias();
            }

            $parseSchema = false;
        } elseif (is_object($names) && $names instanceof Closure) { // 回调方法
            $condition = new static($this->connect);
            $condition->setTable($this->getTable());
            call_user_func_array($names, [
                $condition,
            ]);

            $table = $condition->makeSql(true);

            if (!$alias) {
                $alias = $condition->getAlias();
            }

            $parseSchema = false;
        } elseif (is_string($names) && 0 === strpos($names, '(')) { // 字符串子表达式
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
        } else {
            throw new InvalidArgumentException('Invalid table name.');
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

            $select = new static($this->connect);
            $select->setTable($alias);

            call_user_func_array([
                $select,
                'where',
            ], $args);

            $cond = $select->parseWhere(true);
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
     *
     * @param string $tableName
     * @param mixed  $cols
     */
    protected function addCols(string $tableName, $cols): void
    {
        // 处理条件表达式
        if (is_string($cols) &&
            false !== strpos($cols, ',') &&
            false !== strpos($cols, '{') &&
            preg_match_all('/{(.+?)}/', $cols, $matches)) {
            $cols = str_replace(
                $matches[1][0],
                base64_encode($matches[1][0]),
                $cols
            );
        }

        $cols = normalize($cols);

        // 还原
        if (!empty($matches)) {
            foreach ($matches[1] as $tmp) {
                $key = array_search('{'.base64_encode($tmp).'}', $cols, true);
                $cols[$key] = '{'.$tmp.'}';
            }
        }

        // 没有字段则退出
        if (empty($cols)) {
            return;
        }

        foreach ($cols as $alias => $col) {
            // 处理条件表达式
            if (false !== strpos($col, ',') &&
                false !== strpos($col, '{') &&
                preg_match_all('/{(.+?)}/', $col, $subMatches)) {
                $col = str_replace(
                    $subMatches[1][0],
                    base64_encode($subMatches[1][0]),
                    $col
                );
            }

            $col = normalize($col);

            // 还原
            if (!empty($subMatches)) {
                foreach ($subMatches[1] as $tmp) {
                    $key = array_search('{'.base64_encode($tmp).'}', $col, true);
                    $col[$key] = '{'.$tmp.'}';
                }
            }

            // 将包含多个字段的字符串打散
            foreach (normalize($col) as $col) {
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
     *
     * @param string $type  类型
     * @param string $field 字段
     * @param string $alias 别名
     *
     * @return $this
     */
    protected function addAggregate(string $type, string $field, string $alias): self
    {
        $this->options['columns'] = [];
        $tableName = $this->getTable();

        // 表达式支持
        if (false !== strpos($field, '{') &&
            preg_match('/^{(.+?)}$/', $field, $matches)) {
            $field = $this->connect->normalizeExpression(
                $matches[1],
                $tableName
            );
        } else {
            // 检查字段名是否包含表名称
            if (preg_match('/(.+)\.(.+)/', $field, $matches)) {
                $tableName = $matches[1];
                $field = $matches[2];
            }

            if ('*' === $field) {
                $tableName = '';
            }

            $field = $this->connect->normalizeColumn($field, $tableName);
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
     *
     * @param int|string $names
     */
    protected function deleteBindParams($names): void
    {
        if (isset($this->bindParams[$names])) {
            unset($this->bindParams[$names]);
        }
    }

    /**
     * 分析绑定参数数据.
     *
     * @param array $data
     * @param array $bind
     * @param int   $questionMark
     * @param int   $index
     *
     * @return array
     */
    protected function normalizeBindData(array $data, array $bind = [], int $questionMark = 0, int $index = 0): array
    {
        $fields = $values = [];
        $tableName = $this->getTable();

        foreach ($data as $key => $value) {
            // 表达式支持
            if (is_string($value) && false !== strpos($value, '{') &&
                preg_match('/^{(.+?)}$/', $value, $matches)) {
                $value = $this->connect->normalizeExpression(
                    $matches[1],
                    $tableName
                );
            } else {
                $value = $this->connect->normalizeColumnValue($value, false);
            }

            // 字段
            if (0 === $index) {
                $fields[] = $key;
            }

            if ((is_string($value) && 0 === strpos($value, ':')) || !empty($matches)) {
                $values[] = $value;
            } else {
                // 转换 ? 占位符至 : 占位符
                if ('?' === $value && isset($bind[$questionMark])) {
                    $key = 'questionmark_'.$questionMark;
                    $value = $bind[$questionMark];
                    unset($bind[$questionMark]);

                    $this->deleteBindParams($questionMark);
                    $questionMark++;
                }

                if ($index > 0) {
                    $key = $key.'_'.$index;
                }

                $values[] = ':'.$key;

                $this->bind($key, $value, $this->connect->normalizeBindParamType($value));
            }
        }

        return [
            $fields,
            $values,
            $bind,
            $questionMark,
        ];
    }

    /**
     * 设置当前表名字.
     *
     * @param string $table
     */
    protected function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * 获取当前表名字.
     *
     * @return string
     */
    protected function getTable(): string
    {
        return $this->table;
    }

    /**
     * 设置是否为表操作.
     *
     * @param bool $isTable
     */
    protected function setIsTable(bool $isTable = true): void
    {
        $this->isTable = $isTable;
    }

    /**
     * 返回是否为表操作.
     *
     * @return bool
     */
    protected function getIsTable(): bool
    {
        return $this->isTable;
    }

    /**
     * 解析时间信息.
     *
     * @param string $field
     * @param mixed  $value
     * @param string $type
     *
     * @return mixed
     */
    protected function parseTime(string $field, $value, string $type)
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
                    throw new InvalidArgumentException(
                        sprintf('Days can only be less than 31,but %s given.', $value)
                    );
                }

                $date = getdate();
                $value = mktime(0, 0, 0, $date['mon'], $value, $date['year']);

                break;
            case 'month':
                $value = (int) $value;

                if ($value > 12) {
                    throw new InvalidArgumentException(
                        sprintf('Months can only be less than 12,but %s given.', $value)
                    );
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
                    throw new InvalidArgumentException(
                        'Please enter a right time of strtotime.'
                    );
                }

                break;
        }

        return $value;
    }

    /**
     * 设置当前是否处于时间条件状态.
     *
     * @param string $inTimeCondition
     */
    protected function setInTimeCondition(?string $inTimeCondition = null): void
    {
        $this->inTimeCondition = $inTimeCondition;
    }

    /**
     * 返回当前是否处于时间条件状态.
     *
     * @return null|string
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
