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

use Closure;
use InvalidArgumentException;
use Leevel\Collection\Collection;
use Leevel\Page\IPage;
use Leevel\Page\Page;
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
        'fetch_args' => [
            'fetch_style'     => null,
            'fetch_argument'  => null,
            'ctor_args'       => [],
        ],

        // 查询主服务器
        'master' => false,

        // 每一项记录以对象返回
        'as_class' => null,

        // 对象附加参数
        'class_args' => [],

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
        try {
            $this->condition->{$method}(...$args);

            return $this;
        } catch (ConditionNotFoundException $e) {
        }

        // 动态查询支持
        if (0 === strncasecmp($method, 'find', 4)) {
            $method = substr($method, 4);

            // support find10start3 etc.
            if (false !== strpos(strtolower($method), 'start')) {
                $values = explode('start', strtolower($method));
                $num = (int) (array_shift($values));
                $offset = (int) (array_shift($values));

                return $this->limit($offset, $num)->
                find();
            }

            // support findByName findByNameAndSex etc.
            // support findAllByNameAndSex etc.
            if (0 === strncasecmp($method, 'By', 2) ||
                0 === strncasecmp($method, 'AllBy', 5)) {
                $method = substr($method, ($isOne = 0 === strncasecmp($method, 'By', 2)) ? 2 : 5);

                $isKeep = false;

                if ('_' === substr($method, -1)) {
                    $isKeep = true;
                    $method = substr($method, 0, -1);
                }

                $keys = explode('And', $method);

                if (count($keys) !== count($args)) {
                    throw new InvalidArgumentException(
                        'Parameters of findBy or findAllBy was not matched.'
                    );
                }

                if (!$isKeep) {
                    $keys = array_map(function ($item) {
                        return $this->unCamelize($item);
                    }, $keys);
                }

                return $this->where(
                    array_combine($keys, $args)
                )->{'find'.($isOne ? 'One' : 'All')}();
            }

            return $this->top((int) ($method))->
            find();
        }

        throw new InvalidArgumentException(
            sprintf(
                'Select do not implement magic method %s.',
                $method
            )
        );
    }

    /**
     * 查询对象
     *
     * @return \Leevel\Database\Condition
     */
    public function databaseCondition(): Condition
    {
        return $this->condition;
    }

    /**
     * 返回数据库连接对象
     *
     * @return \Leevel\Database\Connect
     */
    public function databaseConnect(): Connect
    {
        return $this->connect;
    }

    /**
     * 占位符返回本对象
     *
     * @return $this
     */
    public function selfDatabaseSelect()
    {
        return $this;
    }

    /**
     * 指定返回 SQL 不做任何操作.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return $this
     */
    public function sql(bool $flag = true)
    {
        $this->onlyMakeSql = $flag;

        return $this;
    }

    /**
     * 设置是否查询主服务器.
     *
     * @param bool $master
     *
     * @return $this
     */
    public function master(bool $master = false)
    {
        $this->queryParams['master'] = $master;

        return $this;
    }

    /**
     * 设置查询参数.
     *
     * @param int   $fetchStyle
     * @param mixed $fetchArgument
     * @param array $ctorArgs
     *
     * @return $this
     */
    public function fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = [])
    {
        $this->queryParams['fetch_args']['fetch_style'] = $fetchStyle;

        if ($fetchArgument) {
            $this->queryParams['fetch_args']['fetch_argument'] = $fetchArgument;
        }

        $this->queryParams['fetch_args']['ctor_args'] = $ctorArgs;

        return $this;
    }

    /**
     * 设置以类返会结果.
     *
     * @param string $className
     * @param array  $args
     *
     * @return $this
     */
    public function asClass(string $className, array $args = [])
    {
        $this->queryParams['as_class'] = $className;
        $this->queryParams['class_args'] = $args;
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
        $this->queryParams['as_class'] = null;
        $this->queryParams['as_default'] = true;

        return $this;
    }

    /**
     * 设置是否以集合返回.
     *
     * @param bool $acollection
     *
     * @return $this
     */
    public function asCollection(bool $acollection = true)
    {
        $this->queryParams['as_collection'] = $acollection;

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
    public function select($data = null, array $bind = [], bool $flag = false)
    {
        // 查询对象直接查询
        if ($data instanceof self) {
            return $data->find(null, $this->onlyMakeSql);
        }

        // 回调
        if ($data instanceof Closure) {
            call_user_func_array($data, [
                $this,
            ]);
            $data = null;
        }

        // 调用查询
        if (null === $data) {
            return $this->find(null, $flag);
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
    public function insert($data, array $bind = [], bool $replace = false, bool $flag = false)
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
    public function insertAll($data, array $bind = [], bool $replace = false, bool $flag = false)
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
    public function update($data, array $bind = [], bool $flag = false)
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
    public function updateColumn(string $column, $value, array $bind = [], bool $flag = false)
    {
        return $this->update([$column => $value], $bind, $flag);
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
    public function updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)
    {
        return $this->updateColumn($column, '{['.$column.']+'.$step.'}', $bind, $flag);
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
    public function updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)
    {
        return $this->updateColumn($column, '{['.$column.']-'.$step.'}', $bind, $flag);
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
    public function delete($data = null, array $bind = [], bool $flag = false)
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
    public function truncate(bool $flag = false)
    {
        return $this->safeSql($flag)->
        runNativeSql(
            ...$this->condition->

            truncate()
        );
    }

    /**
     * 返回一条记录.
     *
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function findOne(bool $flag = false)
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
    public function findAll(bool $flag = false)
    {
        $this->condition->all();

        return $this->safeSql($flag)->
        query();
    }

    /**
     * 返回最后几条记录.
     *
     * @param int  $num
     * @param bool $flag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function find(?int $num = null, bool $flag = false)
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
    public function value(string $field, bool $flag = false)
    {
        $this->condition->setColumns($field)->

        one();

        $result = (array) $this->safeSql($flag)->

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
    public function pull(string $field, bool $flag = false)
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
    public function list($fieldValue, ?string $fieldKey = null, bool $flag = false)
    {
        // 纵然有弱水三千，我也只取一瓢 (第一个字段为值，第二个字段为键值，多余的字段丢弃)
        $fields = [];

        if (is_array($fieldValue)) {
            $fields = $fieldValue;
        } else {
            $fields[] = $fieldValue;
        }

        if ($fieldKey) {
            $fields[] = $fieldKey;
        }

        $this->condition->setColumns($fields);

        $tmps = $this->safeSql($flag)->

        asDefault()->

        findAll();

        if (true === $this->onlyMakeSql) {
            return $tmps;
        }

        // 解析结果
        $result = [];

        foreach ($tmps as $tmp) {
            $tmp = (array) $tmp;

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
     * @param \Closure $chunk
     */
    public function chunk(int $count, Closure $chunk)
    {
        $result = $this->forPage($page = 1, $count)->

        findAll();

        while (count($result) > 0) {
            if (false === $chunk($result, $page)) {
                break;
            }

            $page++;

            $result = $this->forPage($page, $count)->

            findAll();
        }
    }

    /**
     * 数据分块处理依次回调.
     *
     * @param int     $count
     * @param Closure $each
     *
     * @return bool
     */
    public function each(int $count, Closure $each)
    {
        return $this->chunk($count, function ($result, $page) use ($each) {
            foreach ($result as $key => $value) {
                if (false === $each($value, $key, $page)) {
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
     * @return array|int
     */
    public function findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)
    {
        $result = $this->findAggregateResult('count', $field, $alias, $flag);

        if (!is_array($result)) {
            $result = (int) $result;
        }

        return $result;
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
    public function findAvg(string $field, string $alias = 'avg_value', bool $flag = false)
    {
        return $this->findAggregateResult('avg', $field, $alias, $flag);
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
    public function findMax(string $field, string $alias = 'max_value', bool $flag = false)
    {
        return $this->findAggregateResult('max', $field, $alias, $flag);
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
    public function findMin(string $field, string $alias = 'min_value', bool $flag = false)
    {
        return $this->findAggregateResult('min', $field, $alias, $flag);
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
    public function findSum(string $field, string $alias = 'sum_value', bool $flag = false)
    {
        return $this->findAggregateResult('sum', $field, $alias, $flag);
    }

    /**
     * 分页查询.
     *
     * @param int    $currentPage
     * @param int    $perPage
     * @param bool   $flag
     * @param bool   $withTotal
     * @param string $column
     *
     * @return array
     */
    public function page(int $currentPage, int $perPage = 10, bool $flag = false, bool $withTotal = true, string $column = '*'): array
    {
        $from = ($currentPage - 1) * $perPage;

        return [
            [
                'per_page'     => $perPage,
                'current_page' => $currentPage,
                'total_record' => $withTotal ? $this->pageCount($column) : null,
                'from'         => $from,
            ],
            $this->limit($from, $perPage)->

            findAll($flag),
        ];
    }

    /**
     * 分页查询.
     * 可以渲染 HTML.
     *
     * @param int    $currentPage
     * @param int    $perPage
     * @param bool   $flag
     * @param string $column
     * @param array  $option
     *
     * @return array
     */
    public function pageHtml(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []): array
    {
        $page = new Page($currentPage, $perPage, $this->pageCount($column), $option);

        return [
            $page,
            $this->limit($page->getFromRecord(), $perPage)->

            findAll($flag),
        ];
    }

    /**
     * 创建一个无限数据的分页查询.
     *
     * @param int   $currentPage
     * @param int   $perPage
     * @param bool  $flag
     * @param array $option
     *
     * @return array
     */
    public function pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): array
    {
        $page = new Page($currentPage, $perPage, IPage::MACRO, $option);

        return [
            $page,
            $this->limit($page->getFromRecord(), $perPage)->

            findAll($flag),
        ];
    }

    /**
     * 创建一个只有上下页的分页查询.
     *
     * @param int   $currentPage
     * @param int   $perPage
     * @param bool  $flag
     * @param array $option
     *
     * @return array
     */
    public function pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = []): array
    {
        $page = new Page($currentPage, $perPage, null, $option);

        return [
            $page,
            $this->limit($page->getFromRecord(), $perPage)->

            findAll($flag),
        ];
    }

    /**
     * 取得分页查询记录数量.
     *
     * @param string $cols
     *
     * @return int
     */
    public function pageCount(string $cols = '*'): int
    {
        $this->backupPageArgs();

        $count = $this->findCount($cols);

        $this->restorePageArgs();

        return $count;
    }

    /**
     * 获得查询字符串.
     *
     * @param $withLogicGroup
     *
     * @return string
     */
    public function makeSql(bool $withLogicGroup = false)
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
    protected function safeSql(bool $flag = true)
    {
        if (true === $this->onlyMakeSql) {
            return $this;
        }

        $this->onlyMakeSql = $flag;

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
            $this->condition->getBindParams(),
            $this->queryParams['master'],
            $this->queryParams['fetch_args']['fetch_style'],
            $this->queryParams['fetch_args']['fetch_argument'],
            $this->queryParams['fetch_args']['ctor_args'],
        ];

        // 只返回 SQL，不做任何实际操作
        if (true === $this->onlyMakeSql) {
            return $args;
        }

        $data = $this->connect->query(...$args);

        if ($this->queryParams['as_default']) {
            $data = $this->queryDefault($data);
        } else {
            $data = $this->queryClass($data);
        }

        return $data;
    }

    /**
     * 以默认返回结果.
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function queryDefault(array $data)
    {
        if (!$this->condition->getOption()['limitQuery']) {
            $data = reset($data) ?: [];
        }

        return $this->queryParams['as_collection'] ? new Collection($data) : $data;
    }

    /**
     * 以 class 返回结果.
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function queryClass(array $data)
    {
        $className = $this->queryParams['as_class'];

        if (!class_exists($className)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The class of query `%s` was not found.',
                    $className
                )
            );
        }

        foreach ($data as $key => $tmp) {
            $data[$key] = new $className((array) $tmp, ...$this->queryParams['class_args']);
        }

        if (!$this->condition->getOption()['limitQuery']) {
            $data = reset($data) ?: new $className([], ...$this->queryParams['class_args']);
        } elseif ($this->queryParams['as_collection']) {
            $data = new Collection($data, [$className]);
        }

        return $data;
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
    protected function findAggregateResult($method, $field, $alias, bool $flag = false)
    {
        $this->condition->{$method}($field, $alias);

        $result = $this->safeSql($flag)->

        asDefault()->

        query();

        if (true === $this->onlyMakeSql) {
            return $result;
        }

        return is_object($result) ? $result->{$alias} : $result[$alias];
    }

    /**
     * 原生 sql 执行方法.
     *
     * @param string $nativeType
     * @param string $data
     * @param array  $bindParams
     *
     * @return mixed
     */
    protected function runNativeSql(string $nativeType, string $data, array $bindParams = [])
    {
        $sqlType = $this->connect->normalizeSqlType($data);

        if ('procedure' === $sqlType) {
            $sqlType = 'select';
        }

        if ($sqlType !== $nativeType) {
            throw new InvalidArgumentException(
                sprintf('The SQL type `%s` must be consistent with the provided `%s`.', $sqlType, $nativeType)
            );
        }

        $args = [$data, $bindParams];

        // 只返回 SQL，不做任何实际操作
        if (true === $this->onlyMakeSql) {
            return $args;
        }

        return $this->connect->{
            'select' === $nativeType ? 'query' : 'execute'
        }(...$args);
    }

    /**
     * 备份分页查询条件.
     */
    protected function backupPageArgs()
    {
        $this->backupPage = [];
        $this->backupPage['query_params'] = $this->queryParams;
        $this->backupPage['aggregate'] = $this->condition->getOption()['aggregate'];
        $this->backupPage['columns'] = $this->condition->getOption()['columns'];
    }

    /**
     * 恢复分页查询条件.
     */
    protected function restorePageArgs()
    {
        $this->queryParams = $this->backupPage['query_params'];
        $this->condition->setOption('aggregate', $this->backupPage['aggregate']);
        $this->condition->setOption('columns', $this->backupPage['columns']);
    }

    /**
     * 驼峰转下划线.
     *
     * @param string $value
     * @param string $separator
     *
     * @return string
     */
    protected function unCamelize(string $value, string $separator = '_'): string
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
