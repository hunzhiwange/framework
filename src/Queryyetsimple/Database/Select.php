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
use Leevel\Page\PageWithoutTotal;
use Leevel\Support\Arr;
use Leevel\Support\Type;
use PDO;

/**
 * 数据库查询器
 * This class borrows heavily from the QeePHP Framework and is part of the QeePHP package.
 * 查询器主体方法来自于早年 QeePHP 数据库查询 Api,这个 10 年前的作品设计理念非常先进.
 * 在这个思想下大量进行了重构，在查询 API 用法上我们将一些与 Laravel 的用法习惯靠拢，实现了大量语法糖.
 * 也支持 ThinkPHP 这种的数组方式传入查询，查询构造器非常复杂，为保证结果符合预期这里编写了大量的单元测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.03.09
 *
 * @version 1.0
 *
 * @see http://qeephp.com
 * @see http://qeephp.cn/docs/qeephp-manual/
 */
class Select
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
     * 查询类型.
     *
     * @var array
     */
    protected $queryParams = [];

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
     * 查询类型.
     *
     * @var array
     */
    protected static $queryParamsDefault = [
        // PDO:fetchAll 参数
        'fetch_type' => [
            'fetch_type'     => null,
            'fetch_argument' => null,
            'ctor_args'      => [],
        ],

        // 查询主服务器
        'master' => false,

        // 每一项记录以对象返回
        'as_class' => null,

        // 数组或者默认
        'as_default' => true,

        // 以对象集合方法返回
        'as_collection' => false,
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
     * 不查询直接返回 SQL.
     *
     * @var bool
     */
    protected $onlyMakeSql = false;

    /**
     * 是否处于时间功能状态
     *
     * @var string
     */
    protected $inTimeCondition;

    /**
     * 额外的查询扩展.
     *
     * @var object
     */
    protected $callSelect;

    /**
     * 分页查询条件备份.
     *
     * @var array
     */
    protected $backupPage = [];

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

        // 动态查询支持
        if (0 === strncasecmp($method, 'get', 3)) {
            $method = substr($method, 3);

            // support get10start3 etc.
            if (false !== strpos(strtolower($method), 'start')) {
                $values = explode('start', strtolower($method));
                $num = (int) (array_shift($values));
                $offset = (int) (array_shift($values));

                return $this->limit($offset, $num)->

                get();
            }

            // support getByName getByNameAndSex etc.
            // support getAllByNameAndSex etc.
            if (0 === strncasecmp($method, 'By', 2) || 
                0 === strncasecmp($method, 'AllBy', 5)) {
                $method = substr(
                    $method,
                    ($isOne = 0 === strncasecmp($method, 'By', 2)) ? 2 : 5
                );

                $isKeep = false;

                if ('_' === substr($method, -1)) {
                    $isKeep = true;
                    $method = substr($method, 0, -1);
                }

                $keys = explode('And', $method);

                if (count($keys) !== count($args)) {
                    throw new Exception(
                        'Parameter quantity does not correspond.'
                    );
                }

                if (!$isKeep) {
                    $keys = array_map(function ($item) {
                        return $this->unCamelize($item);
                    }, $keys);
                }

                return $this->where(
                    array_combine($keys, $args)
                )->{'get'.($isOne ? 'One' : 'All')}();
            }

            return $this->top((int) (substr($method, 3)));
        }

        // 查询组件
        if (!$this->callSelect) {
            throw new Exception(
                sprintf(
                    'Select do not implement magic method %s.',
                    $method
                )
            );
        }

        // 调用事件
        return $this->callSelect->{$method}(...$args);
    }

    /**
     * 返回数据库连接对象
     *
     * @return \Leevel\Database\Connect
     */
    public function databaseConnect()
    {
        return $this->connect;
    }

    /**
     * 占位符返回本对象
     *
     * @return $this
     */
    public function selfQuerySelect()
    {
        return $this;
    }

    /**
     * 注册额外的查询扩展.
     *
     * @param object $callSelect
     *
     * @return $this
     */
    public function registerCallSelect($callSelect)
    {
        $this->callSelect = $callSelect;

        if (method_exists($this->callSelect, 'registerSelect')) {
            $this->callSelect->registerSelect($this);
        }

        return $this;
    }

    /**
     * 原生 sql 查询数据 select.
     *
     * @param null|callable|select|string $data
     * @param array                       $bind
     * @param bool                        $flag   指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function select($data = null, $bind = [], $flag = false)
    {
        if (!Type::these($data, [
            'string',
            'null',
            'callback',
        ]) && !$data instanceof self) {
            throw new Exception('Unsupported parameters.');
        }

        // 查询对象直接查询
        if ($data instanceof self) {
            return $data->get(null, $this->onlyMakeSql);
        }

        // 回调
        if (!is_string($data) && is_callable($data)) {
            call_user_func_array($data, [
                &$this,
            ]);
            $data = null;
        }

        // 调用查询
        if (null === $data) {
            return $this->get(null, $flag);
        }

        $this->safeSql($flag)->

        setNativeSql('select');

        return $this->{'runNativeSql'}(...[
            $data,
            $bind,
        ]);
    }

    /**
     * 插入数据 insert (支持原生 sql).
     *
     * @param array|string $data
     * @param array        $bind
     * @param bool         $replace
     * @param bool         $flag      指示是否不做任何操作只返回 SQL
     *
     * @return int 最后插入ID
     */
    public function insert($data, $bind = [], $replace = false, $flag = false)
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

        // 执行查询
        $this->safeSql($flag)->

        setNativeSql(
            false === $replace ? 'insert' : 'replace'
        );

        return $this->{'runNativeSql'}(...[
            $data,
            $bind,
        ]);
    }

    /**
     * 批量插入数据 insertAll.
     *
     * @param array $data
     * @param array $bind
     * @param bool  $replace
     * @param bool  $flag      指示是否不做任何操作只返回 SQL
     *
     * @return int 最后插入ID
     */
    public function insertAll($data, $bind = [], $replace = false, $flag = false)
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

            foreach ($data as $key => $arrTemp) {
                if (!is_array($arrTemp)) {
                    continue;
                }

                $bindData = $this->getBindData($arrTemp, $bind, $questionMark, $key);
                
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

        // 执行查询
        $this->safeSql($flag)->

        setNativeSql(
            false === $replace ? 'insert' : 'replace'
        );

        return $this->{'runNativeSql'}(...[
            $data,
            $bind,
        ]);
    }

    /**
     * 更新数据 update (支持原生 sql).
     *
     * @param array|string $data
     * @param array        $bind
     * @param bool         $flag   指示是否不做任何操作只返回 SQL
     *
     * @return int 影响记录
     */
    public function update($data, $bind = [], $flag = false)
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

        $this->safeSql($flag)->

        setNativeSql('update');

        return $this->{'runNativeSql'}(...[
            $data,
            $bind,
        ]);
    }

    /**
     * 更新某个字段的值
     *
     * @param string $column
     * @param mixed  $value
     * @param array  $bind
     * @param bool   $flag     指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateColumn($column, $value, $bind = [], $flag = false)
    {
        if (!is_string($column)) {
            throw new Exception('Unsupported parameters.');
        }

        return $this->update(
            [
                $column => $value,
            ],
            $bind,
            $flag
        );
    }

    /**
     * 字段递增.
     *
     * @param string $column
     * @param int    $step
     * @param array  $bind
     * @param bool   $flag     指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateIncrease($column, $step = 1, $bind = [], $flag = false)
    {
        return $this->updateColumn(
            $column,
            '{['.$column.']+'.$step.'}',
            $bind,
            $flag
        );
    }

    /**
     * 字段减少.
     *
     * @param string $column
     * @param int    $step
     * @param array  $bind
     * @param bool   $flag     指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateDecrease($column, $step = 1, $bind = [], $flag = false)
    {
        return $this->updateColumn(
            $column,
            '{['.$column.']-'.$step.'}',
            $bind,
            $flag
        );
    }

    /**
     * 删除数据 delete (支持原生 sql).
     *
     * @param null|string $data
     * @param array       $bind
     * @param bool        $flag   指示是否不做任何操作只返回 SQL
     *
     * @return int 影响记录
     */
    public function delete($data = null, $bind = [], $flag = false)
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

        $this->safeSql($flag)->

        setNativeSql('delete');

        return $this->{'runNativeSql'}(...[
            $data,
            $bind,
        ]);
    }

    /**
     * 清空表重置自增 ID.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     */
    public function truncate($flag = false)
    {
        // 构造 truncate 语句
        $sql = [];
        $sql[] = 'TRUNCATE TABLE';
        $sql[] = $this->parseTable(true);
        $sql = implode(' ', $sql);

        $this->safeSql($flag)->

        setNativeSql('statement');

        return $this->{'runNativeSql'}(...[
            $sql,
        ]);
    }

    /**
     * 声明 statement 运行一般 sql,无返回.
     *
     * @param string $data
     * @param array  $bind
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
     */
    public function statement(string $data, $bind = [], $flag = false)
    {
        $this->safeSql($flag)->

        setNativeSql('statement');

        return $this->{'runNativeSql'}(...[
            $data,
            $bind,
        ]);
    }

    /**
     * 返回一条记录.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function getOne($flag = false)
    {
        return $this->safeSql($flag)->

        one()->

        query();
    }

    /**
     * 返回所有记录.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function getAll($flag = false)
    {
        if ($this->options['limitquery']) {
            return $this->safeSql($flag)->

            query();
        }

        return $this->safeSql($flag)->

        all()->

        query();
    }

    /**
     * 返回最后几条记录.
     *
     * @param mixed $num
     * @param bool  $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function get($num = null, $flag = false)
    {
        if (null !== $num) {
            return $this->safeSql($flag)->

            top($num)->

            query();
        }

        return $this->safeSql($flag)->

        query();
    }

    /**
     * 返回一个字段的值
     *
     * @param string $field
     * @param bool   $flag    指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function value($field, $flag = false)
    {
        $row = $this->safeSql($flag)->

        asDefault()->

        setColumns($field)->

        getOne();

        if (true === $this->onlyMakeSql) {
            return $row;
        }

        return $row[$field] ?? null;
    }

    /**
     * 返回一个字段的值(别名).
     *
     * @param string $field
     * @param bool   $flag    指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function pull($field, $flag = false)
    {
        return $this->value($field, $flag);
    }

    /**
     * 返回一列数据.
     *
     * @param mixed  $fieldValue
     * @param string $fieldKey
     * @param bool   $flag         指示是否不做任何操作只返回 SQL
     *
     * @return array
     */
    public function lists($fieldValue, $fieldKey = null, $flag = false)
    {
        // 纵然有弱水三千，我也只取一瓢 (第一个字段为值，第二个字段为键值，多余的字段丢弃)
        $fields = [];

        if (is_array($fieldValue)) {
            $fields = $fieldValue;
        } else {
            $fields[] = $fieldValue;
        }

        if (is_string($fieldKey)) {
            $fields[] = $fieldKey;
        }

        $tmps = $this->safeSql($flag)->

        asDefault()->

        setColumns($fields)->

        getAll();

        if (true === $this->onlyMakeSql) {
            return $tmps;
        }

        // 解析结果
        $result = [];

        foreach ($tmps as $arrTemp) {
            if (1 === count($arrTemp)) {
                $result[] = reset($arrTemp);
            } else {
                $value = array_shift($arrTemp);
                $mixKey = array_shift($arrTemp);
                $result[$mixKey] = $value;
            }
        }

        return $result;
    }

    /**
     * 数据分块处理.
     *
     * @param int      $count
     * @param callable $calCallback
     *
     * @return bool
     */
    public function chunk($count, callable $calCallback)
    {
        $result = $this->forPage($page = 1, $count)->getAll();

        while (count($result) > 0) {
            if (false === call_user_func_array($calCallback, [
                $result,
                $page,
            ])) {
                return false;
            }

            $page++;
            $result = $this->forPage($page, $count)->getAll();
        }

        return true;
    }

    /**
     * 数据分块处理依次回调.
     *
     * @param int      $count
     * @param callable $calCallback
     *
     * @return bool
     */
    public function each($count, callable $calCallback)
    {
        return $this->chunk($count, function ($result, $page) use ($calCallback) {
            foreach ($result as $key => $value) {
                if (false === $calCallback($value, $key, $page)) {
                    return false;
                }
            }
        });
    }

    /**
     * 总记录数.
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag    指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function getCount($field = '*', $alias = 'row_count', $flag = false)
    {
        $row = (array) $this->safeSql($flag)->

        asDefault()->

        count($field, $alias)->

        get();

        if (true === $this->onlyMakeSql) {
            return $row;
        }

        return (int) ($row[$alias]);
    }

    /**
     * 平均数.
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getAvg($field, $alias = 'avg_value', $flag = false)
    {
        $row = (array) $this->safeSql($flag)->

        asDefault()->

        avg($field, $alias)->

        get();

        if (true === $this->onlyMakeSql) {
            return $row;
        }

        return (float) $row[$alias];
    }

    /**
     * 最大值
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getMax($field, $alias = 'max_value', $flag = false)
    {
        $row = (array) $this->safeSql($flag)->

        asDefault()->

        max($field, $alias)->

        get();

        if (true === $this->onlyMakeSql) {
            return $row;
        }

        return (float) $row[$alias];
    }

    /**
     * 最小值
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getMin($field, $alias = 'min_value', $flag = false)
    {
        $row = (array) $this->safeSql($flag)->

        asDefault()->

        min($field, $alias)->

        get();

        if (true === $this->onlyMakeSql) {
            return $row;
        }

        return (float) $row[$alias];
    }

    /**
     * 合计
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getSum($field, $alias = 'sum_value', $flag = false)
    {
        $row = (array) $this->safeSql($flag)->

        asDefault()->

        sum($field, $alias)->

        get();

        if (true === $this->onlyMakeSql) {
            return $row;
        }

        return $row[$alias];
    }

    /**
     * 分页查询.
     *
     * @param int   $perPage
     * @param mixed $cols
     * @param array $options
     *
     * @return array
     */
    public function paginate($perPage = 10, $cols = '*', array $options = [])
    {
        $page = new page_with_total(
            $perPage,
            $this->getPaginateCount($cols),
            $options
        );

        return [
            $page,
            $this->limit(
                $page->getFirstRecord(),
                $perPage
            )->

            getAll(),
        ];
    }

    /**
     * 简单分页查询.
     *
     * @param int   $perPage
     * @param mixed $cols
     * @param array $options
     *
     * @return array
     */
    public function simplePaginate($perPage = 10, $cols = '*', array $options = [])
    {
        $page = new PageWithoutTotal(
            $perPage,
            $options
        );

        return [
            $page,
            $this->limit(
                $page->getFirstRecord(),
                $perPage
            )->

            getAll(),
        ];
    }

    /**
     * 取得分页查询记录数量.
     *
     * @param mixed $cols
     *
     * @return int
     */
    public function getPaginateCount($cols = '*')
    {
        $this->backupPaginateArgs();

        $count = $this->getCount(
            is_array($cols) ? reset($cols) : $cols
        );

        $this->restorePaginateArgs();

        return $count;
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
            isset($args[0]) && in_array($args[0], [
                'date',
                'month',
                'year',
                'day',
            ], true) ? 
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
     * 指定返回 SQL 不做任何操作.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return $this
     */
    public function sql($flag = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->onlyMakeSql = (bool) $flag;

        return $this;
    }

    /**
     * 设置是否查询主服务器.
     *
     * @param bool $master
     *
     * @return $this
     */
    public function asMaster($master = false)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->queryParams['master'] = $master;

        return $this;
    }

    /**
     * 设置查询结果类型.
     *
     * @param mixed $type
     * @param mixed $value
     *
     * @return $this
     */
    public function asFetchType($type, $value = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_array($type)) {
            $this->queryParams['fetch_type'] = array_merge(
                $this->queryParams['fetch_type'],
                $type
            );
        } else {
            if (null === $value) {
                $this->queryParams['fetch_type']['fetch_type'] = $type;
            } else {
                $this->queryParams['fetch_type'][$type] = $value;
            }
        }

        return $this;
    }

    /**
     * 设置以类返会结果.
     *
     * @param string $className
     *
     * @return $this
     */
    public function asClass($className)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->queryParams['as_class'] = $className;
        $this->queryParams['as_default'] = false;

        return $this;
    }

    /**
     * 设置默认形式返回.
     *
     * @return $this
     */
    public function asDefault()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->queryParams['as_class'] = null;
        $this->queryParams['as_default'] = true;

        return $this;
    }

    /**
     * 设置是否以集合返回.
     *
     * @param string $asCollection
     *
     * @return $this
     */
    public function asCollection($asCollection = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->queryParams['as_collection'] = $asCollection;
        $this->queryParams['as_default'] = false;

        return $this;
    }

    /**
     * 重置查询条件.
     *
     * @param string $sOption
     *
     * @return $this
     */
    public function reset($sOption = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (null === $sOption) {
            $this->initOption();
        } elseif (array_key_exists($sOption, static::$optionsDefault)) {
            $this->options[$sOption] = static::$optionsDefault[$sOption];
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

        foreach ($prefix as $strValue) {
            $strValue = Arr::normalize($strValue);

            foreach ($strValue as $tmp) {
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
     * @param array|string $mixName
     *
     * @return $this
     */
    public function using($mixName)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $mixName = Arr::normalize($mixName);

        foreach ($mixName as $alias => $sTable) {
            // 字符串指定别名
            if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $sTable, $arrMatch)) {
                $alias = $arrMatch[2];
                $sTable = $arrMatch[1];
            }

            if (!is_string($alias)) {
                $alias = $sTable;
            }

            // 确定 table_name 和 schema
            $arrTemp = explode('.', $sTable);
            if (isset($arrTemp[1])) {
                $sSchema = $arrTemp[0];
                $tableName = $arrTemp[1];
            } else {
                $sSchema = null;
                $tableName = $sTable;
            }

            // 获得一个唯一的别名
            $alias = $this->uniqueAlias(empty($alias) ? $tableName : $alias);

            $this->options['using'][$alias] = [
                'table_name' => $sTable,
                'schema'     => $sSchema,
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

        return $this->{'aliasTypeAndLogic'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasTypeAndLogic'}(...$arr);
    }

    /**
     * exists 方法支持
     *
     * @param mixed $mixExists
     *
     * @return $this
     */
    public function whereExists($mixExists)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->{'addConditions'}([
            'exists__' => $mixExists,
        ]);
    }

    /**
     * not exists 方法支持
     *
     * @param mixed $mixExists
     *
     * @return $this
     */
    public function whereNotExists($mixExists)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->{'addConditions'}([
            'notexists__' => $mixExists,
        ]);
    }

    /**
     * 参数绑定支持
     *
     * @param mixed $mixName
     * @param mixed $value
     * @param int   $intType
     *
     * @return $this
     */
    public function bind($mixName, $value = null, $intType = PDO::PARAM_STR)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_array($mixName)) {
            foreach ($mixName as $mixKey => $item) {
                if (!is_array($item)) {
                    $item = [
                        $item,
                        $intType,
                    ];
                }
                $this->bindParams[$mixKey] = $item;
            }
        } else {
            if (!is_array($value)) {
                $value = [
                    $value,
                    $intType,
                ];
            }
            $this->bindParams[$mixName] = $value;
        }

        return $this;
    }

    /**
     * index 强制索引（或者忽略索引）.
     *
     * @param array|string $mixIndex
     * @param string       $sType
     *
     * @return $this
     */
    public function forceIndex($mixIndex, $sType = 'FORCE')
    {
        if ($this->checkTControl()) {
            return $this;
        }
        if (!isset(static::$indexTypes[$sType])) {
            throw new Exception(sprintf('Invalid Index type %s.', $sType));
        }
        $sType = strtoupper($sType);
        $mixIndex = Arr::normalize($mixIndex);
        foreach ($mixIndex as $strValue) {
            $strValue = Arr::normalize($strValue);
            foreach ($strValue as $tmp) {
                $tmp = trim($tmp);
                if (empty($tmp)) {
                    continue;
                }
                if (empty($this->options['index'][$sType])) {
                    $this->options['index'][$sType] = [];
                }
                $this->options['index'][$sType][] = $tmp;
            }
        }

        return $this;
    }

    /**
     * index 忽略索引.
     *
     * @param array|string $mixIndex
     *
     * @return $this
     */
    public function ignoreIndex($mixIndex)
    {
        return $this->forceIndex($mixIndex, 'IGNORE');
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

        return $this->{'addJoin'}(...$args);
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

        return $this->{'addJoin'}(...$args);
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

        return $this->{'addJoin'}(...$args);
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

        return $this->{'addJoin'}(...$args);
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

        return $this->{'addJoin'}(...$args);
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

        return $this->{'addJoin'}(...$args);
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

        return $this->{'addJoin'}(...$args);
    }

    /**
     * 添加一个 UNION 查询.
     *
     * @param array|callable|string $mixSelect
     * @param string                $sType
     *
     * @return $this
     */
    public function union($mixSelect, $sType = 'UNION')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (!isset(static::$unionTypes[$sType])) {
            throw new Exception(sprintf('Invalid UNION type %s.', $sType));
        }

        if (!is_array($mixSelect)) {
            $mixSelect = [
                $mixSelect,
            ];
        }

        foreach ($mixSelect as $mixTemp) {
            $this->options['union'][] = [
                $mixTemp,
                $sType,
            ];
        }

        return $this;
    }

    /**
     * 添加一个 UNION ALL 查询.
     *
     * @param array|callable|string $mixSelect
     *
     * @return $this
     */
    public function unionAll($mixSelect)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->union($mixSelect, 'UNION ALL');
    }

    /**
     * 指定 GROUP BY 子句.
     *
     * @param array|string $mixExpr
     *
     * @return $this
     */
    public function groupBy($mixExpr)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_string($mixExpr) && false !== strpos($mixExpr, ',') && false !== strpos($mixExpr, '{') && preg_match_all('/{(.+?)}/', $mixExpr, $arrRes)) {
            $mixExpr = str_replace($arrRes[1][0], base64_encode($arrRes[1][0]), $mixExpr);
        }

        $mixExpr = Arr::normalize($mixExpr);

        // 还原
        if (!empty($arrRes)) {
            foreach ($arrRes[1] as $tmp) {
                $mixExpr[
                    array_search(
                        '{'.base64_encode($tmp).'}', 
                        $mixExpr, 
                        true
                    )
                ] = '{'.$tmp.'}';
            }
        }

        $sCurrentTableName = $this->getCurrentTable();
        foreach ($mixExpr as $strValue) {
            // 处理条件表达式
            if (is_string($strValue) && false !== strpos($strValue, ',') && false !== strpos($strValue, '{') && preg_match_all('/{(.+?)}/', $strValue, $arrResTwo)) {
                $strValue = str_replace(
                    $arrResTwo[1][0], 
                    base64_encode($arrResTwo[1][0]), 
                    $strValue
                );
            }

            $strValue = Arr::normalize($strValue);

            // 还原
            if (!empty($arrResTwo)) {
                foreach ($arrResTwo[1] as $tmp) {
                    $strValue[
                        array_search(
                            '{'.base64_encode($tmp).'}', 
                            $strValue,
                            true
                        )
                    ] = '{'.$tmp.'}';
                }
            }

            foreach ($strValue as $tmp) {
                $tmp = trim($tmp);

                if (empty($tmp)) {
                    continue;
                }

                if (preg_match('/(.+)\.(.+)/', $tmp, $arrMatch)) {
                    $sCurrentTableName = $arrMatch[1];
                    $tmp = $arrMatch[2];
                }

                // 表达式支持
                $tmp = $this->qualifyOneColumn($tmp, $sCurrentTableName);
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

        return $this->{'aliasTypeAndLogic'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasCondition'}(...$arr);
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

        return $this->{'aliasTypeAndLogic'}(...$arr);
    }

    /**
     * 添加排序.
     *
     * @param array|string $mixExpr
     * @param string       $sOrderDefault
     *
     * @return $this
     */
    public function orderBy($mixExpr, $sOrderDefault = 'ASC')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $sOrderDefault = strtoupper($sOrderDefault); // 格式化为大写

        // 处理条件表达式
        if (is_string($mixExpr) && false !== strpos($mixExpr, ',') && false !== strpos($mixExpr, '{') && preg_match_all('/{(.+?)}/', $mixExpr, $arrRes)) {
            $mixExpr = str_replace($arrRes[1][0], base64_encode($arrRes[1][0]), $mixExpr);
        }
        $mixExpr = Arr::normalize($mixExpr);
        // 还原
        if (!empty($arrRes)) {
            foreach ($arrRes[1] as $tmp) {
                $mixExpr[
                    array_search(
                        '{'.base64_encode($tmp).'}', 
                        $mixExpr, 
                        true
                    )
                ] = '{'.$tmp.'}';
            }
        }

        $tableName = $this->getCurrentTable();
        foreach ($mixExpr as $strValue) {
            // 处理条件表达式
            if (is_string($strValue) && 
                false !== strpos($strValue, ',') && 
                false !== strpos($strValue, '{') && 
                preg_match_all('/{(.+?)}/', $strValue, $arrResTwo)) {
                $strValue = str_replace(
                    $arrResTwo[1][0], 
                    base64_encode($arrResTwo[1][0]), 
                    $strValue
                );
            }

            $strValue = Arr::normalize($strValue);

            // 还原
            if (!empty($arrResTwo)) {
                foreach ($arrResTwo[1] as $tmp) {
                    $strValue[
                        array_search(
                            '{'.base64_encode($tmp).'}', 
                            $strValue, 
                            true
                        )
                    ] = '{'.$tmp.'}';
                }
            }

            foreach ($strValue as $tmp) {
                $tmp = trim($tmp);
                if (empty($tmp)) {
                    continue;
                }

                // 表达式支持
                if (false !== strpos($tmp, '{') && 
                    preg_match('/^{(.+?)}$/', $tmp, $arrResThree)) {
                    $tmp = $this->connect->qualifyExpression(
                        $arrResThree[1], $tableName
                    );

                    if (preg_match('/(.*\W)('.'ASC'.'|'.'DESC'.')\b/si', $tmp, $arrMatch)) {
                        $tmp = trim($arrMatch[1]);
                        $sSort = strtoupper($arrMatch[2]);
                    } else {
                        $sSort = $sOrderDefault;
                    }

                    $this->options['order'][] = $tmp.' '.$sSort;
                } else {
                    $sCurrentTableName = $tableName;
                    $sSort = $sOrderDefault;

                    if (preg_match('/(.*\W)('.'ASC'.'|'.'DESC'.')\b/si', $tmp, $arrMatch)) {
                        $tmp = trim($arrMatch[1]);
                        $sSort = strtoupper($arrMatch[2]);
                    }

                    if (!preg_match('/\(.*\)/', $tmp)) {
                        if (preg_match('/(.+)\.(.+)/', $tmp, $arrMatch)) {
                            $sCurrentTableName = $arrMatch[1];
                            $tmp = $arrMatch[2];
                        }

                        $tmp = $this->connect->qualifyTableOrColumn(
                            "{$sCurrentTableName}.{$tmp}"
                        );
                    }

                    $this->options['order'][] = $tmp.' '.$sSort;
                }
            }
        }

        return $this;
    }

    /**
     * 最近排序数据.
     *
     * @param string $mixField
     *
     * @return $this
     */
    public function latest($mixField = 'create_at')
    {
        return $this->orderBy($mixField, 'DESC');
    }

    /**
     * 最早排序数据.
     *
     * @param string $mixField
     *
     * @return $this
     */
    public function oldest($mixField = 'create_at')
    {
        return $this->orderBy($mixField, 'ASC');
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
     * @param number $nCount
     *
     * @return $this
     */
    public function top($nCount = 30)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->limit(0, $nCount);
    }

    /**
     * limit 限制条数.
     *
     * @param number $offset
     * @param number $nCount
     *
     * @return $this
     */
    public function limit($offset = 0, $nCount = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        if (null === $nCount) {
            return $this->top($offset);
        }
        $this->options['limitcount'] = abs((int) $nCount);
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
     * @param $booWithLogicGroup
     *
     * @return string
     */
    public function makeSql($booWithLogicGroup = false)
    {
        $sql = [
            'SELECT',
        ];

        foreach (array_keys($this->options) as $sOption) {
            if ('from' === $sOption) {
                $sql['from'] = '';
            } elseif ('union' === $sOption) {
                continue;
            } else {
                $method = 'parse'.ucfirst($sOption);

                if (method_exists($this, $method)) {
                    $sql[$sOption] = $this->{$method}();
                }
            }
        }

        $sql['from'] = $this->parseFrom();

        // 删除空元素
        foreach ($sql as $offset => $sOption) {
            if ('' === trim($sOption)) {
                unset($sql[$offset]);
            }
        }

        $sql[] = $this->parseUnion();
        $sLastSql = trim(implode(' ', $sql));

        if (true === $booWithLogicGroup) {
            return static::LOGIC_GROUP_LEFT.
                $sLastSql.
                static::LOGIC_GROUP_RIGHT;
        }

        return $sLastSql;
    }

    /**
     * 安全格式指定返回 SQL 不做任何操作.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return $this
     */
    protected function safeSql($flag = true)
    {
        if (true === $this->onlyMakeSql) {
            return $this;
        }

        $this->onlyMakeSql = (bool) $flag;

        return $this;
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

        $arrColumns = [];
        foreach ($this->options['columns'] as $arrEntry) {
            list($tableName, $sCol, $alias) = $arrEntry;

            // 表达式支持
            if (false !== strpos($sCol, '{') && 
                preg_match('/^{(.+?)}$/', $sCol, $arrRes)) {
                $arrColumns[] = $this->connect->qualifyExpression(
                    $arrRes[1],
                    $tableName
                );
            } else {
                if ('*' !== $sCol && $alias) {
                    $arrColumns[] = $this->connect->qualifyTableOrColumn(
                        "{$tableName}.{$sCol}", 
                        $alias, 
                        'AS'
                    );
                } else {
                    $arrColumns[] = $this->connect->qualifyTableOrColumn(
                        "{$tableName}.{$sCol}"
                    );
                }
            }
        }

        return implode(',', $arrColumns);
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

        $arrColumns = [];

        foreach ($this->options['aggregate'] as $arrAggregate) {
            list(, $sField, $alias) = $arrAggregate;

            if ($alias) {
                $arrColumns[] = $sField.' AS '.$alias;
            } else {
                $arrColumns[] = $sField;
            }
        }

        return (empty($arrColumns)) ? '' : implode(',', $arrColumns);
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

        $arrFrom = [];
        foreach ($this->options['from'] as $alias => $arrTable) {
            $sTmp = '';

            // 如果不是第一个 FROM，则添加 JOIN
            if (!empty($arrFrom)) {
                $sTmp .= strtoupper($arrTable['join_type']).' ';
            }

            // 表名子表达式支持
            if (false !== strpos($arrTable['table_name'], '(')) {
                $sTmp .= $arrTable['table_name'].' '.$alias;
            } elseif ($alias === $arrTable['table_name']) {
                $sTmp .= $this->connect->qualifyTableOrColumn(
                    "{$arrTable['schema']}.{$arrTable['table_name']}"
                );
            } else {
                $sTmp .= $this->connect->qualifyTableOrColumn(
                    "{$arrTable['schema']}.{$arrTable['table_name']}",
                    $alias
                );
            }

            // 添加 JOIN 查询条件
            if (!empty($arrFrom) && !empty($arrTable['join_cond'])) {
                $sTmp .= ' ON '.$arrTable['join_cond'];
            }

            $arrFrom[] = $sTmp;
        }

        if (!empty($arrFrom)) {
            return 'FROM '.implode(' ', $arrFrom);
        }

        return '';
    }

    /**
     * 解析 table 分析结果.
     *
     * @param bool $booOnlyAlias
     * @param bool $booForDelete
     *
     * @return string
     */
    protected function parseTable($booOnlyAlias = true, $booForDelete = false)
    {
        if (empty($this->options['from'])) {
            return '';
        }

        // 如果为删除,没有 join 则返回为空
        if (true === $booForDelete && 1 === count($this->options['from'])) {
            return '';
        }

        foreach ($this->options['from'] as $alias => $arrTable) {
            if ($alias === $arrTable['table_name']) {
                return $this->connect->qualifyTableOrColumn(
                    "{$arrTable['schema']}.{$arrTable['table_name']}"
                );
            }

            if (true === $booOnlyAlias) {
                return $alias;
            }

            // 表名子表达式支持
            if (false !== strpos($arrTable['table_name'], '(')) {
                return $arrTable['table_name'].' '.$alias;
            }

            return $this->connect->qualifyTableOrColumn(
                "{$arrTable['schema']}.{$arrTable['table_name']}",
                $alias
            );
        }
    }

    /**
     * 解析 using 分析结果.
     *
     * @param bool $booForDelete
     *
     * @return string
     */
    protected function parseUsing($booForDelete = false)
    {
        // parse using 只支持删除操作
        if (false === $booForDelete || empty($this->options['using'])) {
            return '';
        }

        $arrUsing = [];
        $optionsUsing = $this->options['using'];

        // table 自动加入
        foreach ($this->options['from'] as $alias => $arrTable) {
            $optionsUsing[$alias] = $arrTable;

            break;
        }

        foreach ($optionsUsing as $alias => $arrTable) {
            if ($alias === $arrTable['table_name']) {
                $arrUsing[] = $this->connect->qualifyTableOrColumn(
                    "{$arrTable['schema']}.{$arrTable['table_name']}"
                );
            } else {
                $arrUsing[] = $this->connect->qualifyTableOrColumn(
                    "{$arrTable['schema']}.{$arrTable['table_name']}",
                    $alias
                );
            }
        }

        return 'USING '.implode(',', array_unique($arrUsing));
    }

    /**
     * 解析 index 分析结果.
     *
     * @return string
     */
    protected function parseIndex()
    {
        $strIndex = '';

        foreach ([
            'FORCE',
            'IGNORE',
        ] as $sType) {
            if (empty($this->options['index'][$sType])) {
                continue;
            }

            $strIndex .= ($strIndex ? ' ' : '').
                $sType.' INDEX('.
                implode(',', $this->options['index'][$sType]).
                ')';
        }

        return $strIndex;
    }

    /**
     * 解析 where 分析结果.
     *
     * @param bool $booChild
     *
     * @return string
     */
    protected function parseWhere($booChild = false)
    {
        if (empty($this->options['where'])) {
            return '';
        }

        return $this->analyseCondition('where', $booChild);
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

        $sSql = '';

        if ($this->options['union']) {
            $nOptions = count($this->options['union']);

            foreach ($this->options['union'] as $nCnt => $arrUnion) {
                list($mixUnion, $sType) = $arrUnion;

                if ($mixUnion instanceof self) {
                    $mixUnion = $mixUnion->makeSql();
                }

                if ($nCnt <= $nOptions - 1) {
                    $sSql .= "\n".$sType.' '.$mixUnion;
                }
            }
        }

        return $sSql;
    }

    /**
     * 解析 order 分析结果.
     *
     * @param bool $booForDelete
     *
     * @return string
     */
    protected function parseOrder($booForDelete = false)
    {
        if (empty($this->options['order'])) {
            return '';
        }
        // 删除存在 join, order 无效
        if (true === $booForDelete && 
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

        return 'GROUP BY '.implode(',', $this->options['group']);
    }

    /**
     * 解析 having 分析结果.
     *
     * @param bool $booChild
     *
     * @return string
     */
    protected function parseHaving($booChild = false)
    {
        if (empty($this->options['having'])) {
            return '';
        }

        return $this->analyseCondition('having', $booChild);
    }

    /**
     * 解析 limit 分析结果.
     *
     * @param bool $booNullLimitOffset
     * @param bool $booForDelete
     *
     * @return string
     */
    protected function parseLimitcount($booNullLimitOffset = false, $booForDelete = false)
    {
        // 删除存在 join, limit 无效
        if (true === $booForDelete && 
            (count($this->options['from']) > 1 || 
                !empty($this->options['using']))) {
            return '';
        }

        if (true === $booNullLimitOffset) {
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
            sprintf('Connect method %s is not exits', 'parseLimitcount')
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
     * @param bool   $booChild
     *
     * @return string
     */
    protected function analyseCondition($condType, $booChild = false)
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
                    preg_match('/^{(.+?)}$/', $cond[0], $arrRes)) {
                    $cond[0] = $this->connect->qualifyExpression(
                        $arrRes[1],
                        $table
                    );
                } else {
                    // 字段处理
                    if (false !== strpos($cond[0], ',')) {
                        $arrTemp = explode(',', $cond[0]);
                        $cond[0] = $arrTemp[1];
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
                    ] as $strTimeType) {
                        if (0 === stripos($cond[1], '@'.$strTimeType)) {
                            $findTime = $strTimeType;
                            $cond[1] = ltrim(substr($cond[1], strlen($strTimeType) + 1));
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
                    $booIsArray = true;

                    if (!is_array($cond[2])) {
                        $cond[2] = (array) $cond[2];
                        $booIsArray = false;
                    }

                    foreach ($cond[2] as &$tmp) {
                        // 对象子表达式支持
                        if ($tmp instanceof self) {
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
                            preg_match('/^{(.+?)}$/', $tmp, $arrRes)) {
                            $tmp = $this->connect->qualifyExpression(
                                $arrRes[1],
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

                    if (false === $booIsArray || 
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

        return (false === $booChild ? strtoupper($condType).' ' : '').
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
            foreach ($cond as $arrTemp) {
                $this->addConditions($arrTemp[0], $conditionType, $arrTemp[1]);
            }
        }

        return $this;
    }

    /**
     * 别名类型和逻辑.
     *
     * @param string $strType
     * @param string $strLogic
     * @param mixed  $cond
     *
     * @return $this
     */
    protected function aliasTypeAndLogic($strType, $strLogic, $cond)
    {
        $this->setTypeAndLogic($strType, $strLogic);

        if (!is_string($cond) && is_callable($cond)) {
            $select = new static($this->connect);
            $select->setCurrentTable($this->getCurrentTable());
            $resultCallback = call_user_func_array($cond, [
                &$select,
            ]);
            if (null === $resultCallback) {
                $strParseType = 'parse'.ucwords($strType);
                $tmp = $select->{$strParseType}(true);
            } else {
                $tmp = $resultCallback;
            }
            $this->setConditionItem(static::LOGIC_GROUP_LEFT.$tmp.static::LOGIC_GROUP_RIGHT, 'string__');

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
            $booOneImension = false;

            foreach ($args[0] as $mixKey => $value) {
                if (is_int($mixKey) && !is_array($value)) {
                    $booOneImension = true;
                }

                break;
            }

            if (true === $booOneImension) {
                $conditions = [
                    $args[0],
                ];
            } else {
                $conditions = $args[0];
            }
        }

        // 遍历数组拼接结果
        foreach ($conditions as $strKey => $arrTemp) {
            if (!is_int($strKey)) {
                $strKey = trim($strKey);
            }

            // 字符串表达式
            if (is_string($strKey) && 'string__' === $strKey) {
                // 不符合规则抛出异常
                if (!is_string($arrTemp)) {
                    throw new Exception('String__ type only supports string.');
                }

                // 表达式支持
                if (false !== strpos($arrTemp, '{') && preg_match('/^{(.+?)}$/', $arrTemp, $arrRes)) {
                    $arrTemp = $this->connect->qualifyExpression($arrRes[1], $table);
                }
                $this->setConditionItem($arrTemp, 'string__');
            }

            // 子表达式
            elseif (is_string($strKey) && in_array($strKey, [
                'subor__',
                'suband__',
            ], true)) {
                $arrTypeAndLogic = $this->getTypeAndLogic();

                $select = new static($this->connect);
                $select->setCurrentTable($this->getCurrentTable());
                $select->setTypeAndLogic($arrTypeAndLogic[0]);

                // 逻辑表达式
                if (isset($arrTemp['logic__'])) {
                    if (strtolower($arrTemp['logic__']) === static::LOGIC_OR) {
                        $select->setTypeAndLogic(null, static::LOGIC_OR);
                    }
                    unset($arrTemp['logic__']);
                }

                $select = $select->addConditions(
                    $arrTemp
                );

                // 解析结果
                $strParseType = 'parse'.ucwords($arrTypeAndLogic[0]);
                $strOldLogic = $arrTypeAndLogic[1];
                $this->setTypeAndLogic(null, 'subor__' ? static::LOGIC_OR : static::LOGIC_AND);
                $this->setConditionItem(static::LOGIC_GROUP_LEFT.$select->{$strParseType}(true).static::LOGIC_GROUP_RIGHT, 'string__');
                $this->setTypeAndLogic(null, $strOldLogic);
            }

            // exists 支持
            elseif (is_string($strKey) && in_array($strKey, [
                'exists__',
                'notexists__',
            ], true)) {
                // having 不支持 [not] exists
                if ('having' === $this->getTypeAndLogic()[0]) {
                    throw new Exception('Having do not support [not] exists writing.');
                }

                if ($arrTemp instanceof self) {
                    $arrTemp = $arrTemp->makeSql();
                } elseif (!is_string($arrTemp) && is_callable($arrTemp)) {
                    $select = new static($this->connect);
                    $select->setCurrentTable($this->getCurrentTable());
                    $resultCallback = call_user_func_array($arrTemp, [
                        &$select,
                    ]);
                    if (null === $resultCallback) {
                        $tmp = $arrTemp = $select->makeSql();
                    } else {
                        $tmp = $resultCallback;
                    }
                }

                $arrTemp = ('notexists__' === $strKey ? 'NOT EXISTS ' : 'EXISTS ').
                    static::LOGIC_GROUP_LEFT.
                    $arrTemp.
                    static::LOGIC_GROUP_RIGHT;

                $this->setConditionItem($arrTemp, 'string__');
            }

            // 其它
            else {
                // 处理字符串 "null"
                if (is_scalar($arrTemp)) {
                    $arrTemp = (array) $arrTemp;
                }

                // 合并字段到数组
                if (is_string($strKey)) {
                    array_unshift($arrTemp, $strKey);
                }

                // 处理默认 “=” 的类型
                if (2 === count($arrTemp) && !in_array($arrTemp[1], [
                    'null',
                    'not null',
                ], true)) {
                    $arrTemp[2] = $arrTemp[1];
                    $arrTemp[1] = '=';
                }

                // 字段
                $arrTemp[1] = trim($arrTemp[1]);

                // 特殊类型
                if (in_array($arrTemp[1], [
                    'between',
                    'not between',
                    'in',
                    'not in',
                    'null',
                    'not null',
                ], true)) {
                    if (isset($arrTemp[2]) && is_string($arrTemp[2])) {
                        $arrTemp[2] = explode(',', $arrTemp[2]);
                    }
                    $this->setConditionItem([
                        $arrTemp[0],
                        $arrTemp[1],
                        $arrTemp[2] ?? null,
                    ]);
                }

                // 普通类型
                else {
                    $this->setConditionItem($arrTemp);
                }
            }
        }

        return $this;
    }

    /**
     * 设置条件的一项.
     *
     * @param array  $arrItem
     * @param string $strType
     */
    protected function setConditionItem($arrItem, $strType = '')
    {
        $arrTypeAndLogic = $this->getTypeAndLogic();
        // 字符串类型
        if ($strType) {
            if (empty($this->options[$arrTypeAndLogic[0]][$strType])) {
                $this->options[$arrTypeAndLogic[0]][] = $arrTypeAndLogic[1];
                $this->options[$arrTypeAndLogic[0]][$strType] = [];
            }
            $this->options[$arrTypeAndLogic[0]][$strType][] = $arrItem;
        } else {
            // 格式化时间
            if (($inTimeCondition = $this->getInTimeCondition())) {
                $arrItem[1] = '@'.$inTimeCondition.' '.$arrItem[1];
            }
            $this->options[$arrTypeAndLogic[0]][] = $arrTypeAndLogic[1];
            $this->options[$arrTypeAndLogic[0]][] = $arrItem;
        }
    }

    /**
     * 设置条件的逻辑和类型.
     *
     * @param string $strType
     * @param string $strLogic
     */
    protected function setTypeAndLogic($strType = null, $strLogic = null)
    {
        if (null !== $strType) {
            $this->conditionType = $strType;
        }
        if (null !== $strLogic) {
            $this->conditionLogic = $strLogic;
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

        if (false !== strpos($field, '{') && preg_match('/^{(.+?)}$/', $field, $arrRes)) {
            $field = $this->connect->qualifyExpression($arrRes[1], $tableName);
        } elseif (!preg_match('/\(.*\)/', $field)) {
            if (preg_match('/(.+)\.(.+)/', $field, $arrMatch)) {
                $sCurrentTableName = $arrMatch[1];
                $tmp = $arrMatch[2];
            } else {
                $sCurrentTableName = $tableName;
            }
            $field = $this->connect->qualifyTableOrColumn("{$sCurrentTableName}.{$field}");
        }

        return $field;
    }

    /**
     * 连表 join 操作.
     *
     * @param string     $sJoinType
     * @param mixed      $mixName
     * @param mixed      $cols
     * @param null|array $arrCondArgs
     * @param null|mixed $cond
     *
     * @return $this
     */
    protected function addJoin($sJoinType, $mixName, $cols, $cond = null)
    {
        // 验证 join 类型
        if (!isset(static::$joinTypes[$sJoinType])) {
            throw new Exception(sprintf('Invalid JOIN type %s.', $sJoinType));
        }

        // 不能在使用 UNION 查询的同时使用 JOIN 查询
        if (count($this->options['union'])) {
            throw new Exception('JOIN queries cannot be used while using UNION queries.');
        }

        // 是否分析 schema，子表达式不支持
        $booParseSchema = true;

        // 没有指定表，获取默认表
        if (empty($mixName)) {
            $sTable = $this->getCurrentTable();
            $alias = '';
        }

        // $mixName 为数组配置
        elseif (is_array($mixName)) {
            foreach ($mixName as $alias => $sTable) {
                if (!is_string($alias)) {
                    $alias = '';
                }

                // 对象子表达式
                if ($sTable instanceof self) {
                    $sTable = $sTable->makeSql(true);
                    if (!$alias) {
                        $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
                    }
                    $booParseSchema = false;
                }

                // 回调方法子表达式
                elseif (!is_string($sTable) && is_callable($sTable)) {
                    $select = new static($this->connect);
                    $select->setCurrentTable($this->getCurrentTable());
                    $resultCallback = call_user_func_array($sTable, [
                        &$select,
                    ]);
                    if (null === $resultCallback) {
                        $sTable = $select->makeSql(true);
                    } else {
                        $sTable = $resultCallback;
                    }
                    if (!$alias) {
                        $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
                    }
                    $booParseSchema = false;
                }

                break;
            }
        }

        // 对象子表达式
        elseif ($mixName instanceof self) {
            $sTable = $mixName->makeSql(true);
            $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            $booParseSchema = false;
        }

        // 回调方法
        elseif (!is_string($mixName) && is_callable($mixName)) {
            $select = new static($this->connect);
            $select->setCurrentTable($this->getCurrentTable());
            $resultCallback = call_user_func_array($mixName, [
                &$select,
            ]);
            if (null === $resultCallback) {
                $sTable = $select->makeSql(true);
            } else {
                $sTable = $resultCallback;
            }
            $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            $booParseSchema = false;
        }

        // 字符串子表达式
        elseif (0 === strpos(trim($mixName), '(')) {
            if (false !== ($intAsPosition = strripos($mixName, 'as'))) {
                $sTable = trim(substr($mixName, 0, $intAsPosition - 1));
                $alias = trim(substr($mixName, $intAsPosition + 2));
            } else {
                $sTable = $mixName;
                $alias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            }
            $booParseSchema = false;
        } else {
            // 字符串指定别名
            if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $mixName, $arrMatch)) {
                $sTable = $arrMatch[1];
                $alias = $arrMatch[2];
            } else {
                $sTable = $mixName;
                $alias = '';
            }
        }

        // 确定 table_name 和 schema
        if (true === $booParseSchema) {
            $arrTemp = explode('.', $sTable);
            if (isset($arrTemp[1])) {
                $sSchema = $arrTemp[0];
                $tableName = $arrTemp[1];
            } else {
                $sSchema = null;
                $tableName = $sTable;
            }
        } else {
            $sSchema = null;
            $tableName = $sTable;
        }

        // 获得一个唯一的别名
        $alias = $this->uniqueAlias(empty($alias) ? $tableName : $alias);

        // 只有表操作才设置当前表
        if ($this->getIsTable()) {
            $this->setCurrentTable(($sSchema ? $sSchema.'.' : '').$alias);
        }

        // 查询条件
        $args = func_get_args();
        if (count($args) > 3) {
            for ($nI = 0; $nI <= 2; $nI++) {
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
            'join_type'  => $sJoinType,
            'table_name' => $tableName,
            'schema'     => $sSchema,
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
        if (is_string($cols) && false !== strpos($cols, ',') && 
            false !== strpos($cols, '{') && 
            preg_match_all('/{(.+?)}/', $cols, $arrRes)) {
            $cols = str_replace(
                $arrRes[1][0],
                base64_encode($arrRes[1][0]),
                $cols
            );
        }

        $cols = Arr::normalize($cols);

        // 还原
        if (!empty($arrRes)) {
            foreach ($arrRes[1] as $tmp) {
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

        foreach ($cols as $alias => $mixCol) {
            if (is_string($mixCol)) {
                // 处理条件表达式
                if (is_string($mixCol) && 
                    false !== strpos($mixCol, ',') && 
                    false !== strpos($mixCol, '{') && 
                    preg_match_all('/{(.+?)}/', $mixCol, $arrResTwo)) {
                    $mixCol = str_replace(
                        $arrResTwo[1][0],
                        base64_encode($arrResTwo[1][0]),
                        $mixCol
                    );
                }
                $mixCol = Arr::normalize($mixCol);

                // 还原
                if (!empty($arrResTwo)) {
                    foreach ($arrResTwo[1] as $tmp) {
                        $mixCol[
                            array_search(
                                '{'.base64_encode($tmp).'}',
                                $mixCol,
                                true
                            )
                        ] = '{'.$tmp.'}';
                    }
                }

                // 将包含多个字段的字符串打散
                foreach (Arr::normalize($mixCol) as $sCol) {
                    $currentTableName = $tableName;

                    // 检查是不是 "字段名 AS 别名"这样的形式
                    if (preg_match('/^(.+)\s+'.'AS'.'\s+(.+)$/i', $sCol, $arrMatch)) {
                        $sCol = $arrMatch[1];
                        $alias = $arrMatch[2];
                    }

                    // 检查字段名是否包含表名称
                    if (preg_match('/(.+)\.(.+)/', $sCol, $arrMatch)) {
                        $currentTableName = $arrMatch[1];
                        $sCol = $arrMatch[2];
                    }

                    $this->options['columns'][] = [
                        $currentTableName,
                        $sCol,
                        is_string($alias) ? $alias : null,
                    ];
                }
            } else {
                $this->options['columns'][] = [
                    $tableName,
                    $mixCol,
                    is_string($alias) ? $alias : null,
                ];
            }
        }
    }

    /**
     * 添加一个集合查询.
     *
     * @param string $sType    类型
     * @param string $field 字段
     * @param string $alias   别名
     *
     * @return $this
     */
    protected function addAggregate($sType, $field, $alias)
    {
        $this->options['columns'] = [];
        $tableName = $this->getCurrentTable();

        // 表达式支持
        if (false !== strpos($field, '{') && 
            preg_match('/^{(.+?)}$/', $field, $arrRes)) {
            $field = $this->connect->qualifyExpression($arrRes[1], $tableName);
        } else {
            // 检查字段名是否包含表名称
            if (preg_match('/(.+)\.(.+)/', $field, $arrMatch)) {
                $tableName = $arrMatch[1];
                $field = $arrMatch[2];
            }

            if ('*' === $field) {
                $tableName = '';
            }

            $field = $this->connect->qualifyColumn($field, $tableName);
        }

        $field = "{$sType}(${field})";

        $this->options['aggregate'][] = [
            $sType,
            $field,
            $alias,
        ];

        $this->one();
        $this->arrQueryParam['as_default'] = true;

        return $this;
    }

    /**
     * 查询获得结果.
     *
     * @return mixed
     */
    protected function query()
    {
        $strSql = $this->makeSql();

        $args = [
            $strSql,
            $this->getBindParams(),
            $this->queryParams['master'],
            $this->queryParams['fetch_type']['fetch_type'],
            $this->queryParams['fetch_type']['fetch_argument'],
            $this->queryParams['fetch_type']['ctor_args'],
        ];

        // 只返回 SQL，不做任何实际操作
        if (true === $this->onlyMakeSql) {
            return $args;
        }

        $data = $this->connect->{'query'}(...$args);

        if ($this->queryParams['as_default']) {
            $this->queryDefault($data);
        } else {
            $this->queryClass($data);
        }

        return $data;
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

        foreach ($data as &$mixTemp) {
            $mixTemp = new $className((array) $mixTemp);
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
     * 原生 sql 执行方法.
     *
     * @param null|string $data
     *
     * @return mixed
     */
    protected function runNativeSql($data = null)
    {
        $nativeSql = $this->getNativeSql();

        // 空参数返回当前对象
        if (null === $data) {
            return $this;
        }
        if (is_string($data)) {
            // 验证参数
            $strSqlType = $this->connect->getSqlType($data);
            if ('procedure' === $strSqlType) {
                $strSqlType = 'select';
            }
            if ($strSqlType !== $nativeSql) {
                throw new Exception('Unsupported parameters.');
            }

            $args = func_get_args();

            // 只返回 SQL，不做任何实际操作
            if (true === $this->onlyMakeSql) {
                return $args;
            }

            return $this->connect->{'select' === $nativeSql ? 'query' : 'execute'}(...$args);
        }

        throw new Exception('Unsupported parameters.');
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
     * @param null|mixed $mixName
     *
     * @return array
     */
    protected function getBindParams($mixName = null)
    {
        if (null === $mixName) {
            return $this->bindParams;
        }

        return $this->bindParams[$mixName] ?? null;
    }

    /**
     * 判断是否有参数绑定支持
     *
     * @param mixed(int|string) $mixName
     *
     * @return bool
     */
    protected function isBindParams($mixName)
    {
        return isset($this->bindParams[$mixName]);
    }

    /**
     * 删除参数绑定支持
     *
     * @param mixed(int|string) $mixName
     *
     * @return bool
     */
    protected function deleteBindParams($mixName)
    {
        if (isset($this->bindParams[$mixName])) {
            unset($this->bindParams[$mixName]);
        }
    }

    /**
     * 分析绑定参数数据.
     *
     * @param array $data
     * @param array $bind
     * @param int   $questionMark
     * @param int   $intIndex
     */
    protected function getBindData($data, &$bind = [], &$questionMark = 0, $intIndex = 0)
    {
        $fields = $values = [];
        $tableName = $this->getCurrentTable();

        foreach ($data as $key => $value) {
            // 表达式支持
            if (false !== strpos($value, '{') && 
                preg_match('/^{(.+?)}$/', $value, $arrRes)) {
                $value = $this->connect->qualifyExpression($arrRes[1], $tableName);
            } else {
                $value = $this->connect->qualifyColumnValue($value, false);
            }

            // 字段
            if (0 === $intIndex) {
                $fields[] = $key;
            }

            if (0 === strpos($value, ':') || !empty($arrRes)) {
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

                if ($intIndex > 0) {
                    $key = $key.'_'.$intIndex;
                }

                $values[] = ':'.$key;

                $this->bind($key, $value, $this->connect->getBindParamType($value));
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
        if (is_array($this->currentTable)) { // 数组
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
     * @param string $sField
     * @param mixed  $value
     * @param string $strType
     *
     * @return mixed
     */
    protected function parseTime($sField, $value, $strType)
    {
        static $arrDate = null, $arrColumns = [];

        // 获取时间和字段信息
        if (null === $arrDate) {
            $arrDate = getdate();
        }
        $sField = str_replace('`', '', $sField);
        $table = $this->getCurrentTable();
        if (!preg_match('/\(.*\)/', $sField)) {
            if (preg_match('/(.+)\.(.+)/', $sField, $arrMatch)) {
                $table = $arrMatch[1];
                $sField = $arrMatch[2];
            }
        }
        if ('*' === $sField) {
            return '';
        }
        if (!isset($arrColumns[$table])) {
            $arrColumns[$table] = $this->connect->getTableColumnsCache($table)['list'];
        }

        // 支持类型
        switch ($strType) {
            case 'day':
                $value = mktime(0, 0, 0, $arrDate['mon'], (int) $value, $arrDate['year']);

                break;
            case 'month':
                $value = mktime(0, 0, 0, (int) $value, 1, $arrDate['year']);

                break;
            case 'year':
                $value = mktime(0, 0, 0, 1, 1, (int) $value);

                break;
            case 'date':
                $value = strtotime($value);
                if (false === $value) {
                    throw new Exception('Please enter a right time of strtotime.');
                }

                break;
            default:
                throw new Exception(sprintf('Unsupported time formatting type %s.', $strType));
                break;
        }

        // 自动格式化时间
        if (!empty($arrColumns[$table][$sField])) {
            $fieldType = $arrColumns[$table][$sField]['type'];
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
     * @param mixed $mixName
     *
     * @return string
     */
    protected function uniqueAlias($mixName)
    {
        if (empty($mixName)) {
            return '';
        }

        if (is_array($mixName)) { // 数组，返回最后一个元素
            $strAliasReturn = end($mixName);
        } else { // 字符串
            $nDot = strrpos($mixName, '.');
            $strAliasReturn = false === $nDot ? $mixName : substr($mixName, $nDot + 1);
        }
        for ($nI = 2; array_key_exists($strAliasReturn, $this->options['from']); $nI++) {
            $strAliasReturn = $mixName.'_'.(string) $nI;
        }

        return $strAliasReturn;
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
        $this->queryParams = static::$queryParamsDefault;
    }

    /**
     * 备份分页查询条件.
     */
    protected function backupPaginateArgs()
    {
        $this->backupPage = [];
        $this->backupPage['aggregate'] = $this->options['aggregate'];
        $this->backupPage['query_params'] = $this->queryParams;
        $this->backupPage['columns'] = $this->options['columns'];
    }

    /**
     * 恢复分页查询条件.
     */
    protected function restorePaginateArgs()
    {
        $this->options['aggregate'] = $this->backupPage['aggregate'];
        $this->queryParams = $this->backupPage['query_params'];
        $this->options['columns'] = $this->backupPage['columns'];
    }

    /**
     * 驼峰转下划线.
     *
     * @param string $strValue
     * @param string $separator
     *
     * @return string
     */
    protected function unCamelize($strValue, $separator = '_')
    {
        return strtolower(
            preg_replace(
                '/([a-z])([A-Z])/',
                '$1'.$separator.'$2',
                $strValue
            )
        );
    }
}
