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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database;

use BadMethodCallException;
use Exception;
use Leevel\Collection\Collection;
use Leevel\Flow\TControl;
use Leevel\Support\Arr;
use Leevel\Support\Type;
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
    use TControl;

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
     * @var Leevel\Database\Connect
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
     * 支持的 join 类型.
     *
     * @var array
     */
    protected static $joinTypes = [
        'inner join'   => 'inner join',
        'left join'    => 'left join',
        'right join'   => 'right join',
        'full join'    => 'full join',
        'cross join'   => 'cross join',
        'natural join' => 'natural join',
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
        'using'       => [],
        'index'       => [],
        'where'       => null,
        'group'       => [],
        'having'      => null,
        'order'       => [],
        'limitcount'  => null,
        'limitoffset' => null,
        'limitquery'  => true,
        'forupdate'   => false,
    ];

    /**
     * 原生 sql 类型.
     *
     * @var string
     */
    protected $nativeSql = 'select';

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
    protected $currentTable = '';

    /**
     * 是否为表操作.
     *
     * @var bool
     */
    protected $isTable = false;

    /**
     * 是否处于时间功能状态
     *
     * @var string
     */
    protected $inTimeCondition;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Connect $connect
     */
    public function __construct($connect)
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
        if ($this->placeholderTControl($method)) {
            return $this;
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
     * @return null|array
     */
    public function insert($data, $bind = [], $replace = false)
    {
        if (!Type::these($data, [
            'string',
            'array',
        ])) {
            throw new Exception('Unsupported parameters.');
        }

        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据插入
        if (is_array($data)) {
            $questionMark = 0;
            $bindData = $this->getBindData($data, $bind, $questionMark);
            $fields = $bindData[0];
            $values = $bindData[1];
            $tableName = $this->getCurrentTable();

            foreach ($fields as &$field) {
                $field = $this->qualifyOneColumn(
                    $field,
                    $tableName
                );
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

                unset($bindData, $fields, $values, $sql);
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
     * @return null|array
     */
    public function insertAll($data, $bind = [], $replace = false)
    {
        if (!is_array($data)) {
            throw new Exception('Unsupported parameters.');
        }

        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据批量插入
        if (is_array($data)) {
            $dataResult = [];
            $questionMark = 0;
            $tableName = $this->getCurrentTable();

            foreach ($data as $key => $tmp) {
                if (!is_array($tmp)) {
                    continue;
                }

                $bindData = $this->getBindData($tmp, $bind, $questionMark, $key);

                if (0 === $key) {
                    $fields = $bindData[0];

                    foreach ($fields as &$field) {
                        $field = $this->qualifyOneColumn($field, $tableName);
                    }
                }

                $values = $bindData[1];

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
     * @return null|array
     */
    public function update($data, $bind = [])
    {
        if (!Type::these($data, [
            'string',
            'array',
        ])) {
            throw new Exception('Unsupported parameters.');
        }

        // 绑定参数
        $bind = array_merge($this->getBindParams(), $bind);

        // 构造数据更新
        if (is_array($data)) {
            $questionMark = 0;
            $bindData = $this->getBindData($data, $bind, $questionMark);
            $fields = $bindData[0];
            $values = $bindData[1];
            $tableName = $this->getCurrentTable();

            // SET 语句
            $setData = [];

            foreach ($fields as $key => $field) {
                $field = $this->qualifyOneColumn($field, $tableName);
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
                $sql[] = $this->parseLimitcount();
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
     * @return null|array
     */
    public function delete($data = null, $bind = [])
    {
        if (!Type::these($data, [
            'string',
            'null',
        ])) {
            throw new Exception('Unsupported parameters.');
        }

        // 构造数据删除
        if (null === $data) {
            // 构造 delete 语句
            $sql = [];
            $sql[] = 'DELETE';

            // join 方式关联删除
            if (empty($this->options['using'])) {
                $sql[] = $this->parseTable(true, true);
                $sql[] = $this->parseFrom();
            }

            // using 方式关联删除
            else {
                $sql[] = 'FROM '.$this->parseTable(true);
                $sql[] = $this->parseUsing(true);
            }

            $sql[] = $this->parseWhere();
            $sql[] = $this->parseOrder(true);
            $sql[] = $this->parseLimitcount(true, true);
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
    public function truncate()
    {
        // 构造 truncate 语句
        $sql = [];
        $sql[] = 'TRUNCATE TABLE';
        $sql[] = $this->parseTable(true);
        $sql = implode(' ', $sql);

        return [
            'statement',
            $sql,
        ];
    }

    /**
     * 声明 statement 运行一般 sql,无返回.
     *
     * @param string $data
     * @param array  $bind
     * @param bool   $flag 指示是否不做任何操作只返回 SQL
     */
    public function statement(string $data, $bind = [], $flag = false)
    {
        $this->safeSql($flag)->

        setNativeSql('statement');

        return $this->runNativeSql(...[
            $data,
            $bind,
        ]);
    }

    /**
     * 返回所有记录.
     *
     * @return mixed
     */
    public function getAll()
    {
        if ($this->options['limitquery']) {
            return $this;
        }

        return $this->all();
    }

    /**
     * 根据分页设置条件.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return $this
     */
    public function forPage($page, $perPage = 15)
    {
        return $this->limit(($page - 1) * $perPage, $perPage);
    }

    /**
     * 时间控制语句开始.
     */
    public function time()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();

        $this->setInTimeCondition(
            isset($args[0]) &&
            in_array(
                $args[0],
                [
                    'date',
                    'month',
                    'year',
                    'day',
                ],
                true
            ) ?
            $args[0] :
            null
        );
    }

    /**
     * 时间控制语句结束
     */
    public function endTime()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setInTimeCondition(null);
    }

    /**
     * 重置查询条件.
     *
     * @param null|string $option
     *
     * @return $this
     */
    public function reset($option = null)
    {
        if ($this->checkTControl()) {
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
     * @param array|string $prefix
     *
     * @return $this
     */
    public function prefix($prefix)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $prefix = Arr::normalize($prefix);

        foreach ($prefix as $value) {
            $value = Arr::normalize($value);

            foreach ($value as $tmp) {
                $tmp = trim($tmp);

                if (empty($tmp)) {
                    continue;
                }

                $this->options['prefix'][] = strtoupper($tmp);
            }
        }

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
    public function table($table, $cols = '*')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setIsTable(true);
        $this->addJoin('inner join', $table, $cols);
        $this->setIsTable(false);

        return $this;
    }

    /**
     * 添加一个 using 用于删除操作.
     *
     * @param array|string $names
     *
     * @return $this
     */
    public function using($names)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $names = Arr::normalize($names);

        foreach ($names as $alias => $table) {
            // 字符串指定别名
            if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $table, $matches)) {
                $alias = $matches[2];
                $table = $matches[1];
            }

            if (!is_string($alias)) {
                $alias = $table;
            }

            // 确定 table_name 和 schema
            $tmp = explode('.', $table);
            if (isset($tmp[1])) {
                $schema = $tmp[0];
                $tableName = $tmp[1];
            } else {
                $schema = null;
                $tableName = $table;
            }

            // 获得一个唯一的别名
            $alias = $this->uniqueAlias(empty($alias) ? $tableName : $alias);

            $this->options['using'][$alias] = [
                'table_name' => $table,
                'schema'     => $schema,
            ];
        }

        return $this;
    }

    /**
     * 添加字段.
     *
     * @param mixed  $cols
     * @param string $table
     *
     * @return $this
     */
    public function columns($cols = '*', $table = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (null === $table) {
            $table = $this->getCurrentTable();
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
    public function setColumns($cols = '*', $table = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (null === $table) {
            $table = $this->getCurrentTable();
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
    public function where(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_AND);
        array_unshift($arr, 'where');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * whereBetween 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereBetween(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'between');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereNotBetween 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereNotBetween(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'not between');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereIn 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereIn(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'in');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereNotIn 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereNotIn(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'not in');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereNull 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereNull(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'null');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereNotNull 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereNotNull(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'not null');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereLike 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereLike(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'like');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereNotLike 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereNotLike(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('where', static::LOGIC_AND);
        array_unshift($arr, 'not like');

        return $this->aliasCondition(...$arr);
    }

    /**
     * whereDate 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereDate(...$arr)
    {
        $this->setInTimeCondition('date');

        $this->{'where'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * whereMonth 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereMonth(...$arr)
    {
        $this->setInTimeCondition('month');

        $this->{'where'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * whereDay 查询条件.
     *
     * @param mixed $cond
     *
     * @return $this
     */
    public function whereDay(...$arr)
    {
        $this->setInTimeCondition('day');

        $this->{'where'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * whereYear 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function whereYear(...$arr)
    {
        $this->setInTimeCondition('year');

        $this->{'where'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * orWhere 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function orWhere(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_OR);
        array_unshift($arr, 'where');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * exists 方法支持
     *
     * @param mixed $exists
     *
     * @return $this
     */
    public function whereExists($exists)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->{'addConditions'}([
            'exists__' => $exists,
        ]);
    }

    /**
     * not exists 方法支持
     *
     * @param mixed $exists
     *
     * @return $this
     */
    public function whereNotExists($exists)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->{'addConditions'}([
            'notexists__' => $exists,
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
    public function bind($names, $value = null, $type = PDO::PARAM_STR)
    {
        if ($this->checkTControl()) {
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
    public function forceIndex($indexs, $type = 'FORCE')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (!isset(static::$indexTypes[$type])) {
            throw new Exception(
                sprintf('Invalid Index type %s.', $type)
            );
        }

        $type = strtoupper($type);
        $indexs = Arr::normalize($indexs);

        foreach ($indexs as $value) {
            $value = Arr::normalize($value);

            foreach ($value as $tmp) {
                $tmp = trim($tmp);

                if (empty($tmp)) {
                    continue;
                }

                if (empty($this->options['index'][$type])) {
                    $this->options['index'][$type] = [];
                }

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
    public function ignoreIndex($indexs)
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
    public function join($table, $cols, $cond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'inner join');

        return $this->addJoin(...$args);
    }

    /**
     * innerJoin 查询.
     *
     * @param mixed        $table 同 table $table
     * @param array|string $cols  同 table $cols
     * @param mixed        $cond  同 where $cond
     *
     * @return $this
     */
    public function innerJoin($table, $cols, $cond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'inner join');

        return $this->addJoin(...$args);
    }

    /**
     * leftJoin 查询.
     *
     * @param mixed        $table 同 table $table
     * @param array|string $cols  同 table $cols
     * @param mixed        $cond  同 where $cond
     *
     * @return $this
     */
    public function leftJoin($table, $cols, $cond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'left join');

        return $this->addJoin(...$args);
    }

    /**
     * rightJoin 查询.
     *
     * @param mixed        $table 同 table $table
     * @param array|string $cols  同 table $cols
     * @param mixed        $cond  同 where $cond
     *
     * @return $this
     */
    public function rightJoin($table, $cols, $cond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'right join');

        return $this->addJoin(...$args);
    }

    /**
     * fullJoin 查询.
     *
     * @param mixed        $table 同 table $table
     * @param array|string $cols  同 table $cols
     * @param mixed        $cond  同 where $cond
     *
     * @return $this
     */
    public function fullJoin($table, $cols, $cond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'full join');

        return $this->addJoin(...$args);
    }

    /**
     * crossJoin 查询.
     *
     * @param mixed        $table 同 table $table
     * @param array|string $cols  同 table $cols
     *
     * @return $this
     */
    public function crossJoin($table, $cols)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'cross join');

        return $this->addJoin(...$args);
    }

    /**
     * naturalJoin 查询.
     *
     * @param mixed        $table 同 table $table
     * @param array|string $cols  同 table $cols
     *
     * @return $this
     */
    public function naturalJoin($table, $cols)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $args = func_get_args();
        array_unshift($args, 'natural join');

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
    public function union($selects, $type = 'UNION')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (!isset(static::$unionTypes[$type])) {
            throw new Exception(
                sprintf('Invalid UNION type %s.', $type)
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
    public function unionAll($selects)
    {
        if ($this->checkTControl()) {
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
    public function groupBy($expression)
    {
        if ($this->checkTControl()) {
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

        $expression = Arr::normalize($expression);

        // 还原
        if (!empty($matches)) {
            foreach ($matches[1] as $tmp) {
                $expression[
                    array_search(
                        '{'.base64_encode($tmp).'}',
                        $expression,
                        true
                    )
                ] = '{'.$tmp.'}';
            }
        }

        $currentTableName = $this->getCurrentTable();

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

            $value = Arr::normalize($value);

            // 还原
            if (!empty($subMatches)) {
                foreach ($subMatches[1] as $tmp) {
                    $value[
                        array_search(
                            '{'.base64_encode($tmp).'}',
                            $value,
                            true
                        )
                    ] = '{'.$tmp.'}';
                }
            }

            foreach ($value as $tmp) {
                $tmp = trim($tmp);

                if (empty($tmp)) {
                    continue;
                }

                if (preg_match('/(.+)\.(.+)/', $tmp, $matches)) {
                    $currentTableName = $matches[1];
                    $tmp = $matches[2];
                }

                // 表达式支持
                $tmp = $this->qualifyOneColumn(
                    $tmp,
                    $currentTableName
                );

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
    public function having(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_AND);
        array_unshift($arr, 'having');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * havingBetween 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingBetween(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'between');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingNotBetween 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingNotBetween(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'not between');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingIn 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingIn(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'in');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingNotIn 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingNotIn(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'not in');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingNull 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingNull(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'null');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingNotNull 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingNotNull(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'not null');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingLike 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingLike(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'like');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingNotLike 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingNotLike(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setTypeAndLogic('having', static::LOGIC_AND);

        array_unshift($arr, 'not like');

        return $this->aliasCondition(...$arr);
    }

    /**
     * havingDate 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingDate(...$arr)
    {
        $this->setInTimeCondition('date');

        $this->{'having'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * havingMonth 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingMonth(...$arr)
    {
        $this->setInTimeCondition('month');

        $this->{'having'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * havingDay 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingDay(...$arr)
    {
        $this->setInTimeCondition('day');

        $this->{'having'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * havingYear 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function havingYear(...$arr)
    {
        $this->setInTimeCondition('year');

        $this->{'having'}(...$arr);

        $this->setInTimeCondition(null);

        return $this;
    }

    /**
     * orHaving 查询条件.
     *
     * @param array $arr
     *
     * @return $this
     */
    public function orHaving(...$arr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        array_unshift($arr, static::LOGIC_OR);
        array_unshift($arr, 'having');

        return $this->aliatypeAndLogic(...$arr);
    }

    /**
     * 添加排序.
     *
     * @param array|string $expression
     * @param string       $orderDefault
     *
     * @return $this
     */
    public function orderBy($expression, $orderDefault = 'ASC')
    {
        if ($this->checkTControl()) {
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

        $expression = Arr::normalize($expression);

        // 还原
        if (!empty($matches)) {
            foreach ($matches[1] as $tmp) {
                $expression[
                    array_search(
                        '{'.base64_encode($tmp).'}',
                        $expression,
                        true
                    )
                ] = '{'.$tmp.'}';
            }
        }

        $tableName = $this->getCurrentTable();

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

            $value = Arr::normalize($value);

            // 还原
            if (!empty($subMatches)) {
                foreach ($subMatches[1] as $tmp) {
                    $value[
                        array_search(
                            '{'.base64_encode($tmp).'}',
                            $value,
                            true
                        )
                    ] = '{'.$tmp.'}';
                }
            }

            foreach ($value as $tmp) {
                $tmp = trim($tmp);

                if (empty($tmp)) {
                    continue;
                }

                // 表达式支持
                if (false !== strpos($tmp, '{') &&
                    preg_match('/^{(.+?)}$/', $tmp, $threeMatches)) {
                    $tmp = $this->connect->qualifyExpression(
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

                        $tmp = $this->connect->qualifyTableOrColumn(
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
    public function latest($field = 'create_at')
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
    public function oldest($field = 'create_at')
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
    public function distinct($flag = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->options['distinct'] = (bool) $flag;

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
    public function count($field = '*', $alias = 'row_count')
    {
        if ($this->checkTControl()) {
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
    public function avg($field, $alias = 'avg_value')
    {
        if ($this->checkTControl()) {
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
    public function max($field, $alias = 'max_value')
    {
        if ($this->checkTControl()) {
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
    public function min($field, $alias = 'min_value')
    {
        if ($this->checkTControl()) {
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
    public function sum($field, $alias = 'sum_value')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->addAggregate('SUM', $field, $alias);
    }

    /**
     * 指示仅查询第一个符合条件的记录.
     *
     * @return $this
     */
    public function one()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->options['limitcount'] = 1;
        $this->options['limitoffset'] = null;
        $this->options['limitquery'] = false;

        return $this;
    }

    /**
     * 指示查询所有符合条件的记录.
     *
     * @return $this
     */
    public function all()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->options['limitcount'] = null;
        $this->options['limitoffset'] = null;
        $this->options['limitquery'] = true;

        return $this;
    }

    /**
     * 查询几条记录.
     *
     * @param number $count
     *
     * @return $this
     */
    public function top($count = 30)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->limit(0, $count);
    }

    /**
     * limit 限制条数.
     *
     * @param number $offset
     * @param number $count
     *
     * @return $this
     */
    public function limit($offset = 0, $count = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (null === $count) {
            return $this->top($offset);
        }

        $this->options['limitcount'] = abs((int) $count);
        $this->options['limitoffset'] = abs((int) $offset);
        $this->options['limitquery'] = true;

        return $this;
    }

    /**
     * 是否构造一个 FOR UPDATE 查询.
     *
     * @param bool $flag
     *
     * @return $this
     */
    public function forUpdate($flag = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->options['forupdate'] = (bool) $flag;

        return $this;
    }

    /**
     * 获得查询字符串.
     *
     * @param $withLogicGroup
     *
     * @return string
     */
    public function makeSql($withLogicGroup = false)
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
            return static::LOGIC_GROUP_LEFT.
                $result.
                static::LOGIC_GROUP_RIGHT;
        }

        return $result;
    }

    /**
     * 返回所有参数绑定.
     *
     * @return array
     */
    public function getBindParamsAll()
    {
        return $this->bindParams;
    }

    /**
     * 解析 prefix 分析结果.
     *
     * @return string
     */
    protected function parsePrefix()
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
    protected function parseDistinct()
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
    protected function parseColumns()
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
                $columns[] = $this->connect->qualifyExpression(
                    $matches[1],
                    $tableName
                );
            } else {
                if ('*' !== $col && $alias) {
                    $columns[] = $this->connect->qualifyTableOrColumn(
                        "{$tableName}.{$col}",
                        $alias,
                        'AS'
                    );
                } else {
                    $columns[] = $this->connect->qualifyTableOrColumn(
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
    protected function parseAggregate()
    {
        if (empty($this->options['aggregate'])) {
            return '';
        }

        $columns = [];

        foreach ($this->options['aggregate'] as $item) {
            list(, $field, $alias) = $item;

            if ($alias) {
                $columns[] = $field.' AS '.$alias;
            } else {
                $columns[] = $field;
            }
        }

        return empty($columns) ? '' : implode(',', $columns);
    }

    /**
     * 解析 from 分析结果.
     *
     * @return string
     */
    protected function parseFrom()
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
                $tmp .= $this->connect->qualifyTableOrColumn(
                    "{$value['schema']}.{$value['table_name']}"
                );
            } else {
                $tmp .= $this->connect->qualifyTableOrColumn(
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

        if (!empty($from)) {
            return 'FROM '.implode(' ', $from);
        }

        return '';
    }

    /**
     * 解析 table 分析结果.
     *
     * @param bool $onlyAlias
     * @param bool $forDelete
     *
     * @return string
     */
    protected function parseTable($onlyAlias = true, $forDelete = false)
    {
        if (empty($this->options['from'])) {
            return '';
        }

        // 如果为删除,没有 join 则返回为空
        if (true === $forDelete &&
            1 === count($this->options['from'])) {
            return '';
        }

        foreach ($this->options['from'] as $alias => $value) {
            if ($alias === $value['table_name']) {
                return $this->connect->qualifyTableOrColumn(
                    "{$value['schema']}.{$value['table_name']}"
                );
            }

            if (true === $onlyAlias) {
                return $alias;
            }

            // 表名子表达式支持
            if (false !== strpos($value['table_name'], '(')) {
                return $value['table_name'].' '.$alias;
            }

            return $this->connect->qualifyTableOrColumn(
                "{$value['schema']}.{$value['table_name']}",
                $alias
            );
        }
    }

    /**
     * 解析 using 分析结果.
     *
     * @param bool $forDelete
     *
     * @return string
     */
    protected function parseUsing($forDelete = false)
    {
        // parse using 只支持删除操作
        if (false === $forDelete ||
            empty($this->options['using'])) {
            return '';
        }

        $using = [];
        $optionsUsing = $this->options['using'];

        // table 自动加入
        foreach ($this->options['from'] as $alias => $value) {
            $optionsUsing[$alias] = $value;

            break;
        }

        foreach ($optionsUsing as $alias => $value) {
            if ($alias === $value['table_name']) {
                $using[] = $this->connect->qualifyTableOrColumn(
                    "{$value['schema']}.{$value['table_name']}"
                );
            } else {
                $using[] = $this->connect->qualifyTableOrColumn(
                    "{$value['schema']}.{$value['table_name']}",
                    $alias
                );
            }
        }

        return 'USING '.implode(',', array_unique($using));
    }

    /**
     * 解析 index 分析结果.
     *
     * @return string
     */
    protected function parseIndex()
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
    protected function parseWhere($child = false)
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
    protected function parseUnion()
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
     * @param bool $forDelete
     *
     * @return string
     */
    protected function parseOrder($forDelete = false)
    {
        if (empty($this->options['order'])) {
            return '';
        }

        // 删除存在 join, order 无效
        if (true === $forDelete &&
            (count($this->options['from']) > 1 ||
                !empty($this->options['using']))) {
            return '';
        }

        return 'ORDER BY '.
            implode(',', array_unique($this->options['order']));
    }

    /**
     * 解析 group 分析结果.
     *
     * @return string
     */
    protected function parseGroup()
    {
        if (empty($this->options['group'])) {
            return '';
        }

        return 'GROUP BY '.
            implode(',', $this->options['group']);
    }

    /**
     * 解析 having 分析结果.
     *
     * @param bool $child
     *
     * @return string
     */
    protected function parseHaving($child = false)
    {
        if (empty($this->options['having'])) {
            return '';
        }

        return $this->analyseCondition('having', $child);
    }

    /**
     * 解析 limit 分析结果.
     *
     * @param bool $nullLimitOffset
     * @param bool $forDelete
     *
     * @return string
     */
    protected function parseLimitcount($nullLimitOffset = false, $forDelete = false)
    {
        // 删除存在 join, limit 无效
        if (true === $forDelete &&
            (count($this->options['from']) > 1 ||
                !empty($this->options['using']))) {
            return '';
        }

        if (true === $nullLimitOffset) {
            $this->options['limitoffset'] = null;
        }

        if (null === $this->options['limitoffset'] &&
            null === $this->options['limitcount']) {
            return '';
        }

        if (method_exists($this->connect, 'parseLimitcount')) {
            return $this->connect->{'parseLimitcount'}(
                $this->options['limitcount'],
                $this->options['limitoffset']
            );
        }

        throw new BadMethodCallException(
            sprintf(
                'Connect method %s is not exits',
                'parseLimitcount'
            )
        );
    }

    /**
     * 解析 forupdate 分析结果.
     *
     * @return string
     */
    protected function parseForUpdate()
    {
        if (!$this->options['forupdate']) {
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
    protected function analyseCondition($condType, $child = false)
    {
        if (!$this->options[$condType]) {
            return '';
        }

        $sqlCond = [];
        $table = $this->getCurrentTable();

        foreach ($this->options[$condType] as $key => $cond) {
            // 逻辑连接符
            if (in_array($cond, [
                static::LOGIC_AND,
                static::LOGIC_OR,
            ], true)) {
                $sqlCond[] = strtoupper($cond);

                continue;
            }

            // 特殊处理
            if (is_string($key)) {
                if (in_array($key, [
                    'string__',
                ], true)) {
                    $sqlCond[] = implode(' AND ', $cond);
                }
            } elseif (is_array($cond)) {
                // 表达式支持
                if (false !== strpos($cond[0], '{') &&
                    preg_match('/^{(.+?)}$/', $cond[0], $matches)) {
                    $cond[0] = $this->connect->qualifyExpression(
                        $matches[1],
                        $table
                    );
                } else {
                    // 字段处理
                    if (false !== strpos($cond[0], ',')) {
                        $tmp = explode(',', $cond[0]);
                        $cond[0] = $tmp[1];
                        $currentTable = $cond[0];
                    } else {
                        $currentTable = $table;
                    }

                    $cond[0] = $this->connect->qualifyColumn(
                        $cond[0],
                        $currentTable
                    );
                }

                // 分析是否存在自动格式化时间标识
                $findTime = null;

                if (0 === strpos($cond[1], '@')) {
                    foreach ([
                        'date',
                        'month',
                        'day',
                        'year',
                    ] as $timeType) {
                        if (0 === stripos($cond[1], '@'.$timeType)) {
                            $findTime = $timeType;
                            $cond[1] = ltrim(substr($cond[1], strlen($timeType) + 1));

                            break;
                        }
                    }
                    if (null === $findTime) {
                        throw new Exception(
                            'You are trying to an unsupported time processing grammar.'
                        );
                    }
                }

                // 格式化字段值，支持数组
                if (isset($cond[2])) {
                    $isArray = true;

                    if (!is_array($cond[2])) {
                        $cond[2] = (array) $cond[2];
                        $isArray = false;
                    }

                    foreach ($cond[2] as &$tmp) {
                        // 对象子表达式支持
                        if ($tmp instanceof self || $tmp instanceof Select) {
                            $tmp = $tmp->makeSql(true);
                        }

                        // 回调方法子表达式支持
                        elseif (!is_string($tmp) && is_callable($tmp)) {
                            $select = new static($this->connect);
                            $select->setCurrentTable($this->getCurrentTable());
                            $resultCallback = call_user_func_array($tmp, [
                                &$select,
                            ]);

                            if (null === $resultCallback) {
                                $tmp = $select->makeSql(true);
                            } else {
                                $tmp = $resultCallback;
                            }
                        }

                        // 字符串子表达式支持
                        elseif (is_string($tmp) && 0 === strpos($tmp, '(')) {
                        }

                        // 表达式支持
                        elseif (is_string($tmp) &&
                            false !== strpos($tmp, '{') &&
                            preg_match('/^{(.+?)}$/', $tmp, $matches)) {
                            $tmp = $this->connect->qualifyExpression(
                                $matches[1],
                                $table
                            );
                        } else {
                            // 自动格式化时间
                            if (null !== $findTime) {
                                $tmp = $this->parseTime($cond[0], $tmp, $findTime);
                            }

                            $tmp = $this->connect->qualifyColumnValue($tmp);
                        }
                    }

                    if (false === $isArray ||
                        (1 === count($cond[2]) &&
                            0 === strpos(trim($cond[2][0]), '('))) {
                        $cond[2] = reset($cond[2]);
                    }
                }

                // 拼接结果
                if (in_array($cond[1], [
                    'null',
                    'not null',
                ], true)) {
                    $sqlCond[] = $cond[0].' IS '.strtoupper($cond[1]);
                } elseif (in_array($cond[1], [
                    'in',
                    'not in',
                ], true)) {
                    $sqlCond[] = $cond[0].' '.
                        strtoupper($cond[1]).' '.
                        (
                            is_array($cond[2]) ?
                            '('.implode(',', $cond[2]).')' :
                            $cond[2]
                        );
                } elseif (in_array($cond[1], [
                    'between',
                    'not between',
                ], true)) {
                    if (!is_array($cond[2]) || count($cond[2]) < 2) {
                        throw new Exception(
                            'The [not] between parameter value must be '.
                            'an array of not less than two elements.'
                        );
                    }

                    $sqlCond[] = $cond[0].' '.
                        strtoupper($cond[1]).' '.
                        $cond[2][0].' AND '.$cond[2][1];
                } elseif (is_scalar($cond[2])) {
                    $sqlCond[] = $cond[0].' '.
                        strtoupper($cond[1]).' '.
                        $cond[2];
                } elseif (null === $cond[2]) {
                    $sqlCond[] = $cond[0].' IS NULL';
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
    protected function aliasCondition($conditionType, $cond)
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
     */
    protected function aliatypeAndLogic($type, $logic, $cond)
    {
        $this->setTypeAndLogic($type, $logic);

        if (!is_string($cond) && is_callable($cond)) {
            $select = new static($this->connect);
            $select->setCurrentTable($this->getCurrentTable());
            $resultCallback = call_user_func_array($cond, [
                &$select,
            ]);

            if (null === $resultCallback) {
                $tmp = $select->{'parse'.ucwords($type)}(true);
            } else {
                $tmp = $resultCallback;
            }

            $this->setConditioitem(
                static::LOGIC_GROUP_LEFT.
                $tmp.
                static::LOGIC_GROUP_RIGHT,
                'string__'
            );

            return $this;
        }

        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        return $this->{'addConditions'}(...$args);
    }

    /**
     * 组装条件.
     *
     * @return $this
     */
    protected function addConditions()
    {
        $args = func_get_args();
        $table = $this->getCurrentTable();

        // 整理多个参数到二维数组
        if (!is_array($args[0])) {
            $conditions = [
                $args,
            ];
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
                $conditions = [
                    $args[0],
                ];
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
            if (is_string($key) && 'string__' === $key) {
                // 不符合规则抛出异常
                if (!is_string($tmp)) {
                    throw new Exception(
                        'String__ type only supports string.'
                    );
                }

                // 表达式支持
                if (false !== strpos($tmp, '{') &&
                    preg_match('/^{(.+?)}$/', $tmp, $matches)) {
                    $tmp = $this->connect->qualifyExpression(
                        $matches[1],
                        $table
                    );
                }

                $this->setConditioitem($tmp, 'string__');
            }

            // 子表达式
            elseif (is_string($key) && in_array($key, [
                'subor__',
                'suband__',
            ], true)) {
                $typeAndLogic = $this->getTypeAndLogic();

                $select = new static($this->connect);
                $select->setCurrentTable($this->getCurrentTable());
                $select->setTypeAndLogic($typeAndLogic[0]);

                // 逻辑表达式
                if (isset($tmp['logic__'])) {
                    if (strtolower($tmp['logic__']) === static::LOGIC_OR) {
                        $select->setTypeAndLogic(null, static::LOGIC_OR);
                    }

                    unset($tmp['logic__']);
                }

                $select = $select->addConditions(
                    $tmp
                );

                // 解析结果
                $parseType = 'parse'.ucwords($typeAndLogic[0]);
                $oldLogic = $typeAndLogic[1];

                $this->setTypeAndLogic(
                    null, 'subor__' ?
                    static::LOGIC_OR :
                    static::LOGIC_AND
                );

                $this->setConditioitem(
                    static::LOGIC_GROUP_LEFT.
                    $select->{$parseType}(true).
                    static::LOGIC_GROUP_RIGHT,
                    'string__'
                );

                $this->setTypeAndLogic(null, $oldLogic);
            }

            // exists 支持
            elseif (is_string($key) && in_array($key, [
                'exists__',
                'notexists__',
            ], true)) {
                // having 不支持 [not] exists
                if ('having' === $this->getTypeAndLogic()[0]) {
                    throw new Exception(
                        'Having do not support [not] exists writing.'
                    );
                }

                if ($tmp instanceof self || $tmp instanceof Select) {
                    $tmp = $tmp->makeSql();
                } elseif (!is_string($tmp) && is_callable($tmp)) {
                    $select = new static($this->connect);
                    $select->setCurrentTable($this->getCurrentTable());

                    $resultCallback = call_user_func_array($tmp, [
                        &$select,
                    ]);

                    if (null === $resultCallback) {
                        $tmp = $tmp = $select->makeSql();
                    } else {
                        $tmp = $resultCallback;
                    }
                }

                $tmp = ('notexists__' === $key ? 'NOT EXISTS ' : 'EXISTS ').
                    static::LOGIC_GROUP_LEFT.
                    $tmp.
                    static::LOGIC_GROUP_RIGHT;

                $this->setConditioitem($tmp, 'string__');
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

                    $this->setConditioitem([
                        $tmp[0],
                        $tmp[1],
                        $tmp[2] ?? null,
                    ]);
                }

                // 普通类型
                else {
                    $this->setConditioitem($tmp);
                }
            }
        }

        return $this;
    }

    /**
     * 设置条件的一项.
     *
     * @param array  $items
     * @param string $type
     */
    protected function setConditioitem($items, $type = '')
    {
        $typeAndLogic = $this->getTypeAndLogic();

        // 字符串类型
        if ($type) {
            if (empty($this->options[$typeAndLogic[0]][$type])) {
                $this->options[$typeAndLogic[0]][] = $typeAndLogic[1];
                $this->options[$typeAndLogic[0]][$type] = [];
            }

            $this->options[$typeAndLogic[0]][$type][] = $items;
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
    protected function setTypeAndLogic($type = null, $logic = null)
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
    protected function getTypeAndLogic()
    {
        return [
            $this->conditionType,
            $this->conditionLogic,
        ];
    }

    /**
     * 格式化一个字段.
     *
     * @param string $field
     * @param string $tableName
     *
     * @return string
     */
    protected function qualifyOneColumn($field, $tableName = null)
    {
        $field = trim($field);

        if (empty($field)) {
            return '';
        }

        if (null === $tableName) {
            $tableName = $this->getCurrentTable();
        }

        if (false !== strpos($field, '{') &&
            preg_match('/^{(.+?)}$/', $field, $matches)) {
            $field = $this->connect->qualifyExpression(
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

            $field = $this->connect->qualifyTableOrColumn(
                "{$currentTableName}.{$field}"
            );
        }

        return $field;
    }

    /**
     * 连表 join 操作.
     *
     * @param string     $joinType
     * @param mixed      $names
     * @param mixed      $cols
     * @param null|array $arrCondArgs
     * @param null|mixed $cond
     *
     * @return $this
     */
    protected function addJoin($joinType, $names, $cols, $cond = null)
    {
        // 验证 join 类型
        if (!isset(static::$joinTypes[$joinType])) {
            throw new Exception(
                sprintf('Invalid JOIN type %s.', $joinType)
            );
        }

        // 不能在使用 UNION 查询的同时使用 JOIN 查询
        if (count($this->options['union'])) {
            throw new Exception(
                'JOIN queries cannot be used while using UNION queries.'
            );
        }

        // 是否分析 schema，子表达式不支持
        $parseSchema = true;

        // 没有指定表，获取默认表
        if (empty($names)) {
            $table = $this->getCurrentTable();
            $alias = '';
        }

        // $names 为数组配置
        elseif (is_array($names)) {
            foreach ($names as $alias => $table) {
                if (!is_string($alias)) {
                    $alias = '';
                }

                // 对象子表达式
                if ($table instanceof self || $table instanceof Select) {
                    $table = $table->makeSql(true);

                    if (!$alias) {
                        $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
                    }

                    $parseSchema = false;
                }

                // 回调方法子表达式
                elseif (!is_string($table) &&
                    is_callable($table)) {
                    $select = new static($this->connect);
                    $select->setCurrentTable($this->getCurrentTable());
                    $resultCallback = call_user_func_array($table, [
                        &$select,
                    ]);

                    if (null === $resultCallback) {
                        $table = $select->makeSql(true);
                    } else {
                        $table = $resultCallback;
                    }

                    if (!$alias) {
                        $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
                    }

                    $parseSchema = false;
                }

                break;
            }
        }

        // 对象子表达式
        elseif ($names instanceof self || $names instanceof Select) {
            $table = $names->makeSql(true);
            $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            $parseSchema = false;
        }

        // 回调方法
        elseif (!is_string($names) && is_callable($names)) {
            $select = new static($this->connect);
            $select->setCurrentTable($this->getCurrentTable());
            $resultCallback = call_user_func_array($names, [
                &$select,
            ]);

            if (null === $resultCallback) {
                $table = $select->makeSql(true);
            } else {
                $table = $resultCallback;
            }

            $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            $parseSchema = false;
        }

        // 字符串子表达式
        elseif (0 === strpos(trim($names), '(')) {
            if (false !== ($position = strripos($names, 'as'))) {
                $table = trim(substr($names, 0, $position - 1));
                $alias = trim(substr($names, $position + 2));
            } else {
                $table = $names;
                $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            }

            $parseSchema = false;
        } else {
            // 字符串指定别名
            if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $names, $matches)) {
                $table = $matches[1];
                $alias = $matches[2];
            } else {
                $table = $names;
                $alias = '';
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

        // 获得一个唯一的别名
        $alias = $this->uniqueAlias(
            empty($alias) ? $tableName : $alias
        );

        // 只有表操作才设置当前表
        if ($this->getIsTable()) {
            $this->setCurrentTable(
                ($schema ? $schema.'.' : '').$alias
            );
        }

        // 查询条件
        $args = func_get_args();

        if (count($args) > 3) {
            for ($i = 0; $i <= 2; $i++) {
                array_shift($args);
            }

            $select = new static($this->connect);
            $select->setCurrentTable($alias);

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
    protected function addCols($tableName, $cols)
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

        $cols = Arr::normalize($cols);

        // 还原
        if (!empty($matches)) {
            foreach ($matches[1] as $tmp) {
                $cols[
                    array_search(
                        '{'.base64_encode($tmp).'}',
                        $cols,
                        true
                    )
                ] = '{'.$tmp.'}';
            }
        }

        if (null === $tableName) {
            $tableName = '';
        }

        // 没有字段则退出
        if (empty($cols)) {
            return;
        }

        foreach ($cols as $alias => $col) {
            if (is_string($col)) {
                // 处理条件表达式
                if (is_string($col) &&
                    false !== strpos($col, ',') &&
                    false !== strpos($col, '{') &&
                    preg_match_all('/{(.+?)}/', $col, $subMatches)) {
                    $col = str_replace(
                        $subMatches[1][0],
                        base64_encode($subMatches[1][0]),
                        $col
                    );
                }

                $col = Arr::normalize($col);

                // 还原
                if (!empty($subMatches)) {
                    foreach ($subMatches[1] as $tmp) {
                        $col[
                            array_search(
                                '{'.base64_encode($tmp).'}',
                                $col,
                                true
                            )
                        ] = '{'.$tmp.'}';
                    }
                }

                // 将包含多个字段的字符串打散
                foreach (Arr::normalize($col) as $col) {
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
            } else {
                $this->options['columns'][] = [
                    $tableName,
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
    protected function addAggregate($type, $field, $alias)
    {
        $this->options['columns'] = [];
        $tableName = $this->getCurrentTable();

        // 表达式支持
        if (false !== strpos($field, '{') &&
            preg_match('/^{(.+?)}$/', $field, $matches)) {
            $field = $this->connect->qualifyExpression(
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

            $field = $this->connect->qualifyColumn(
                $field,
                $tableName
            );
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
     * 以数组返回结果.
     *
     * @param array $data
     */
    protected function queryDefault(&$data)
    {
        if (empty($data)) {
            if (!$this->options['limitquery']) {
                $data = null;
            }

            return;
        }

        // 返回一条记录
        if (!$this->options['limitquery']) {
            $data = reset($data) ?: null;
        }
    }

    /**
     * 以 class 返回结果.
     *
     * @param array $data
     */
    protected function queryClass(&$data)
    {
        if (empty($data)) {
            if (!$this->options['limitquery']) {
                $data = null;
            } else {
                if ($this->queryParams['as_collection']) {
                    $data = new Collection();
                }
            }

            return;
        }

        // 模型类不存在，直接以数组结果返回
        $className = $this->queryParams['as_class'];

        if ($className && !class_exists($className)) {
            $this->queryDefault($data);

            return;
        }

        foreach ($data as &$tmp) {
            $tmp = new $className((array) $tmp);
        }

        // 创建一个单独的对象
        if (!$this->options['limitquery']) {
            $data = reset($data) ?: null;
        } else {
            if ($this->queryParams['as_collection']) {
                $data = new Collection($data, [$className]);
            }
        }
    }

    /**
     * 设置原生 sql 类型.
     *
     * @param string $nativeSql
     */
    protected function setNativeSql($nativeSql)
    {
        $this->nativeSql = $nativeSql;
    }

    /**
     * 返回原生 sql 类型.
     *
     * @return string
     */
    protected function getNativeSql()
    {
        return $this->nativeSql;
    }

    /**
     * 返回参数绑定.
     *
     * @param mixed      $strBind
     * @param null|mixed $names
     *
     * @return array
     */
    protected function getBindParams($names = null)
    {
        if (null === $names) {
            return $this->bindParams;
        }

        return $this->bindParams[$names] ?? null;
    }

    /**
     * 判断是否有参数绑定支持
     *
     * @param mixed(int|string) $names
     *
     * @return bool
     */
    protected function isBindParams($names)
    {
        return isset($this->bindParams[$names]);
    }

    /**
     * 删除参数绑定支持
     *
     * @param mixed(int|string) $names
     *
     * @return bool
     */
    protected function deleteBindParams($names)
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
     */
    protected function getBindData($data, &$bind = [], &$questionMark = 0, $index = 0)
    {
        $fields = $values = [];
        $tableName = $this->getCurrentTable();

        foreach ($data as $key => $value) {
            // 表达式支持
            if (false !== strpos($value, '{') &&
                preg_match('/^{(.+?)}$/', $value, $matches)) {
                $value = $this->connect->qualifyExpression(
                    $matches[1],
                    $tableName
                );
            } else {
                $value = $this->connect->qualifyColumnValue(
                    $value,
                    false
                );
            }

            // 字段
            if (0 === $index) {
                $fields[] = $key;
            }

            if (0 === strpos($value, ':') || !empty($matches)) {
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

                $this->bind(
                    $key,
                    $value,
                    $this->connect->getBindParamType($value)
                );
            }
        }

        return [
            $fields,
            $values,
        ];
    }

    /**
     * 设置当前表名字.
     *
     * @param mixed $table
     */
    protected function setCurrentTable($table)
    {
        $this->currentTable = $table;
    }

    /**
     * 获取当前表名字.
     *
     * @return string
     */
    protected function getCurrentTable()
    {
        // 数组
        if (is_array($this->currentTable)) {
            while ((list($alias) = each($this->currentTable)) !== false) {
                return $this->currentTable = $alias;
            }
        } else {
            return $this->currentTable;
        }
    }

    /**
     * 设置是否为表操作.
     *
     * @param bool $isTable
     */
    protected function setIsTable($isTable = true)
    {
        $this->isTable = $isTable;
    }

    /**
     * 返回是否为表操作.
     *
     * @return bool
     */
    protected function getIsTable()
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
    protected function parseTime($field, $value, $type)
    {
        static $date = null, $columns = [];

        // 获取时间和字段信息
        if (null === $date) {
            $date = getdate();
        }

        $field = str_replace('`', '', $field);
        $table = $this->getCurrentTable();

        if (!preg_match('/\(.*\)/', $field)) {
            if (preg_match('/(.+)\.(.+)/', $field, $matches)) {
                $table = $matches[1];
                $field = $matches[2];
            }
        }

        if ('*' === $field) {
            return '';
        }

        if (!isset($columns[$table])) {
            $columns[$table] = $this->connect->getTableColumnsCache(
                $table
            )['list'];
        }

        // 支持类型
        switch ($type) {
            case 'day':
                $value = mktime(0, 0, 0, $date['mon'], (int) $value, $date['year']);

                break;
            case 'month':
                $value = mktime(0, 0, 0, (int) $value, 1, $date['year']);

                break;
            case 'year':
                $value = mktime(0, 0, 0, 1, 1, (int) $value);

                break;
            case 'date':
                $value = strtotime($value);
                if (false === $value) {
                    throw new Exception(
                        'Please enter a right time of strtotime.'
                    );
                }

                break;
            default:
                throw new Exception(
                    sprintf(
                        'Unsupported time formatting type %s.',
                        $type
                    )
                );

                break;
        }

        // 自动格式化时间
        if (!empty($columns[$table][$field])) {
            $fieldType = $columns[$table][$field]['type'];

            if (in_array($fieldType, [
                'datetime',
                'timestamp',
            ], true)) {
                $value = date('Y-m-d H:i:s', $value);
            } elseif ('date' === $fieldType) {
                $value = date('Y-m-d', $value);
            } elseif ('time' === $fieldType) {
                $value = date('H:i:s', $value);
            } elseif (0 === strpos($fieldType, 'year')) {
                $value = date('Y', $value);
            }
        }

        return $value;
    }

    /**
     * 别名唯一
     *
     * @param mixed $names
     *
     * @return string
     */
    protected function uniqueAlias($names)
    {
        if (empty($names)) {
            return '';
        }

        // 数组，返回最后一个元素
        if (is_array($names)) {
            $result = end($names);
        }

        // 字符串
        else {
            $dot = strrpos($names, '.');
            $result = false === $dot ?
                $names :
                substr($names, $dot + 1);
        }

        for ($i = 2; array_key_exists($result, $this->options['from']); $i++) {
            $result = $names.'_'.(string) $i;
        }

        return $result;
    }

    /**
     * 设置当前是否处于时间条件状态
     *
     * @param string $inTimeCondition
     */
    protected function setInTimeCondition($inTimeCondition = null)
    {
        $this->inTimeCondition = $inTimeCondition;
    }

    /**
     * 返回当前是否处于时间条件状态
     *
     * @return null|string
     */
    protected function getInTimeCondition()
    {
        return $this->inTimeCondition;
    }

    /**
     * 初始化查询条件.
     */
    protected function initOption()
    {
        $this->options = static::$optionsDefault;
    }
}
