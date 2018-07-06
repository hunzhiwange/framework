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

use Exception;
use Leevel\Collection\Collection;
use Leevel\Page\PageWithoutTotal;
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
    /**
     * 数据库连接.
     *
     * @var Leevel\Database\Connect
     */
    protected $connect;

    /**
     * 额外的查询扩展.
     *
     * @var object
     */
    protected $callSelect;

    /**
     * 查询条件.
     *
     * @var \Leevel\Database\Condition
     */
    protected $condition;

    /**
     * 分页查询条件备份.
     *
     * @var array
     */
    protected $backupPage = [];

    /**
     * 不查询直接返回 SQL.
     *
     * @var bool
     */
    protected $onlyMakeSql = false;

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
     * 查询类型.
     *
     * @var array
     */
    protected $queryParams = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Connect $connect
     */
    public function __construct($connect)
    {
        $this->connect = $connect;

        $this->condition = new Condition($connect);

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
        $condition = true;

        try {
            $this->condition->{$method}(...$args);

            return $this;
        } catch (ConditionNotFoundException $e) {
            $condition = false;
        }

        if (true === $condition) {
            return;
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

            return $this->top((int) ($method))->
            get();
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
     * 指定返回 SQL 不做任何操作.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return $this
     */
    public function sql($flag = true)
    {
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
     * @param string $acollection
     *
     * @return $this
     */
    public function asCollection($acollection = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->queryParams['as_collection'] = $acollection;
        $this->queryParams['as_default'] = false;

        return $this;
    }

    /**
     * 原生 sql 查询数据 select.
     *
     * @param null|callable|select|string $data
     * @param array                       $bind
     * @param bool                        $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function select($data = null, $bind = [], $flag = false)
    {
        if (!Type::these($data, [
            'string',
            'null',
            'callback',
        ]) && !($data instanceof self)) {
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

        return $this->safeSql($flag)->
        runNativeSql(...[
            'select',
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
     * @param bool         $flag    指示是否不做任何操作只返回 SQL
     *
     * @return null|array|int
     */
    public function insert($data, $bind = [], $replace = false, $flag = false)
    {
        return $this->safeSql($flag)->
        runNativeSql(
            ...$this->condition->

            insert($data, $bind, $replace)
        );
    }

    /**
     * 批量插入数据 insertAll.
     *
     * @param array $data
     * @param array $bind
     * @param bool  $replace
     * @param bool  $flag    指示是否不做任何操作只返回 SQL
     *
     * @return null|array|int
     */
    public function insertAll($data, $bind = [], $replace = false, $flag = false)
    {
        return $this->safeSql($flag)->
        runNativeSql(
            ...$this->condition->

            insertAll($data, $bind, $replace)
        );
    }

    /**
     * 更新数据 update (支持原生 sql).
     *
     * @param array|string $data
     * @param array        $bind
     * @param bool         $flag 指示是否不做任何操作只返回 SQL
     *
     * @return int 影响记录
     */
    public function update($data, $bind = [], $flag = false)
    {
        return $this->safeSql($flag)->
        runNativeSql(
            ...$this->condition->

            update($data, $bind)
        );
    }

    /**
     * 更新某个字段的值
     *
     * @param string $column
     * @param mixed  $value
     * @param array  $bind
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateColumn(string $column, $value, $bind = [], $flag = false)
    {
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
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateIncrease(string $column, $step = 1, $bind = [], $flag = false)
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
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateDecrease(string $column, $step = 1, $bind = [], $flag = false)
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
     * @param bool        $flag 指示是否不做任何操作只返回 SQL
     *
     * @return int 影响记录
     */
    public function delete($data = null, $bind = [], $flag = false)
    {
        return $this->safeSql($flag)->
        runNativeSql(
            ...$this->condition->

            delete($data, $bind)
        );
    }

    /**
     * 清空表重置自增 ID.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     */
    public function truncate($flag = false)
    {
        return $this->safeSql($flag)->
        runNativeSql(
            ...$this->condition->

            truncate()
        );
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
     * 返回一条记录.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function getOne($flag = false)
    {
        $this->condition->one();

        return $this->safeSql($flag)->
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
        $this->condition->getAll();

        return $this->safeSql($flag)->
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
            $this->condition->top($num);
        }

        return $this->safeSql($flag)->
        query();
    }

    /**
     * 返回一个字段的值
     *
     * @param string $field
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function value($field, $flag = false)
    {
        $this->condition->setColumns($field)->

        one();

        $result = $this->safeSql($flag)->

        asDefault()->

        query();

        if (true === $this->onlyMakeSql) {
            return $result;
        }

        return $result[$field] ?? null;
    }

    /**
     * 返回一个字段的值(别名).
     *
     * @param string $field
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
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
     * @param bool   $flag       指示是否不做任何操作只返回 SQL
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

        $this->condition->setColumns($fields);

        $tmps = $this->safeSql($flag)->

        asDefault()->

        getAll();

        if (true === $this->onlyMakeSql) {
            return $tmps;
        }

        // 解析结果
        $result = [];

        foreach ($tmps as $tmp) {
            if (1 === count($tmp)) {
                $result[] = reset($tmp);
            } else {
                $value = array_shift($tmp);
                $key = array_shift($tmp);
                $result[$key] = $value;
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
        $result = $this->forPage($page = 1, $count)->

        getAll();

        while (count($result) > 0) {
            if (false === call_user_func_array($calCallback, [
                $result,
                $page,
            ])) {
                return false;
            }

            $page++;

            $result = $this->forPage($page, $count)->

            getAll();
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
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function getCount($field = '*', $alias = 'row_count', $flag = false)
    {
        return $this->getAggregateResult('count', $field, $alias, $flag);
    }

    /**
     * 平均数.
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getAvg($field, $alias = 'avg_value', $flag = false)
    {
        return $this->getAggregateResult('avg', $field, $alias, $flag);
    }

    /**
     * 最大值
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getMax($field, $alias = 'max_value', $flag = false)
    {
        return $this->getAggregateResult('max', $field, $alias, $flag);
    }

    /**
     * 最小值
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getMin($field, $alias = 'min_value', $flag = false)
    {
        return $this->getAggregateResult('min', $field, $alias, $flag);
    }

    /**
     * 合计
     *
     * @param string $field
     * @param string $alias
     * @param bool   $flag  指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getSum($field, $alias = 'sum_value', $flag = false)
    {
        return $this->getAggregateResult('sum', $field, $alias, $flag);
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
     * 获得查询字符串.
     *
     * @param $withLogicGroup
     *
     * @return string
     */
    public function makeSql($withLogicGroup = false)
    {
        return $this->condition->
        makeSql($withLogicGroup);
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
     * 初始化查询条件.
     */
    protected function initOption()
    {
        $this->queryParams = static::$queryParamsDefault;
    }

    /**
     * 查询获得结果.
     *
     * @return mixed
     */
    protected function query()
    {
        $sql = $this->makeSql();

        $args = [
            $sql,
            $this->condition->getBindParamsAll(),
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
            if (!$this->condition->getLimitQuery()) {
                $data = null;
            }

            return;
        }

        // 返回一条记录
        if (!$this->condition->getLimitQuery()) {
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
            if (!$this->condition->getLimitQuery()) {
                $data = null;
            } else {
                if ($this->queryParams['as_collection']) {
                    $data = new Collection();
                }
            }

            return;
        }

        // 模型实体类不存在，直接以数组结果返回
        $className = $this->queryParams['as_class'];

        if ($className && !class_exists($className)) {
            $this->queryDefault($data);

            return;
        }

        foreach ($data as &$tmp) {
            $tmp = new $className((array) $tmp);
        }

        // 创建一个单独的对象
        if (!$this->condition->getLimitQuery()) {
            $data = reset($data) ?: null;
        } else {
            if ($this->queryParams['as_collection']) {
                $data = new Collection($data, [$className]);
            }
        }
    }

    /**
     * 获取聚合结果.
     *
     * @param string $method
     * @param string $field
     * @param string $alias
     * @param bool   $flag   指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    protected function getAggregateResult($method, $field, $alias, $flag = false)
    {
        $this->condition->{$method}($field, $alias);

        $result = (array) $this->safeSql($flag)->

        asDefault()->

        query();

        if (true === $this->onlyMakeSql) {
            return $result;
        }

        return $result[$alias];
    }

    /**
     * 原生 sql 执行方法.
     *
     * @param string      $method
     * @param null|string $data
     *
     * @return mixed
     */
    protected function runNativeSql(string $nativeType, $data = null)
    {
        // 空参数返回当前对象
        if (null === $data) {
            return $this;
        }

        if (is_string($data)) {
            // 验证参数
            $sqlType = $this->connect->getSqlType($data);

            if ('procedure' === $sqlType) {
                $sqlType = 'select';
            }

            if ($sqlType !== $nativeType) {
                throw new Exception('Unsupported parameters.');
            }

            $args = func_get_args();
            array_shift($args);

            // 只返回 SQL，不做任何实际操作
            if (true === $this->onlyMakeSql) {
                return $args;
            }

            return $this->connect->{
                'select' === $nativeType ? 'query' : 'execute'
            }(...$args);
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
     * @param string $value
     * @param string $separator
     *
     * @return string
     */
    protected function unCamelize($value, $separator = '_')
    {
        return strtolower(
            preg_replace(
                '/([a-z])([A-Z])/',
                '$1'.$separator.'$2',
                $value
            )
        );
    }
}
