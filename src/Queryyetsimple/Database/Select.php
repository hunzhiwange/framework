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
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.03.09
 *
 * @version 1.0
 *
 * @see http://qeephp.com
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
    public $strConditionLogic = 'and';

    /**
     * 数据库连接.
     *
     * @var Leevel\Database\Connect
     */
    protected $objConnect;

    /**
     * 绑定参数.
     *
     * @var array
     */
    protected $arrBindParams = [];

    /**
     * 连接参数.
     *
     * @var array
     */
    protected $arrOption = [];

    /**
     * 查询类型.
     *
     * @var array
     */
    protected $arrQueryParams = [];

    /**
     * 字段映射.
     *
     * @var array
     */
    protected $arrColumnsMapping = [];

    /**
     * 支持的聚合类型.
     *
     * @var array
     */
    protected static $arrAggregateTypes = [
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
    protected static $arrJoinTypes = [
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
    protected static $arrUnionTypes = [
        'UNION'     => 'UNION',
        'UNION ALL' => 'UNION ALL',
    ];

    /**
     * 支持的 index 类型.
     *
     * @var array
     */
    protected static $arrIndexTypes = [
        'FORCE'  => 'FORCE',
        'IGNORE' => 'IGNORE',
    ];

    /**
     * 连接参数.
     *
     * @var array
     */
    protected static $arrOptionDefault = [
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
    protected static $arrQueryParamsDefault = [
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
    protected $strNativeSql = 'select';

    /**
     * 条件逻辑类型.
     *
     * @var string
     */
    protected $strConditionType = 'where';

    /**
     * 当前表信息.
     *
     * @var string
     */
    protected $strCurrentTable = '';

    /**
     * 是否为表操作.
     *
     * @var bool
     */
    protected $booIsTable = false;

    /**
     * 不查询直接返回 SQL.
     *
     * @var bool
     */
    protected $booOnlyMakeSql = false;

    /**
     * 是否处于时间功能状态
     *
     * @var string
     */
    protected $strInTimeCondition;

    /**
     * 额外的查询扩展.
     *
     * @var object
     */
    protected $objCallSelect;

    /**
     * 分页查询条件备份.
     *
     * @var array
     */
    protected $arrBackupPage = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Connect $objConnect
     */
    public function __construct($objConnect)
    {
        $this->objConnect = $objConnect;
        $this->initOption();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        if ($this->placeholderTControl($method)) {
            return $this;
        }

        // 动态查询支持
        if (0 === strncasecmp($method, 'get', 3)) {
            $method = substr($method, 3);
            if (false !== strpos(strtolower($method), 'start')) { // support get10start3 etc.
                $arrValue = explode('start', strtolower($method));
                $nNum = (int) (array_shift($arrValue));
                $nOffset = (int) (array_shift($arrValue));

                return $this->limit($nOffset, $nNum)->get();
            }
            if (0 === strncasecmp($method, 'By', 2)) { // support getByName getByNameAndSex etc.
                $method = substr($method, 2);
                $arrKeys = explode('And', $method);
                if (count($arrKeys) !== count($arrArgs)) {
                    throw new Exception('Parameter quantity does not correspond.');
                }

                return $this->where(array_combine($arrKeys, $arrArgs))->getOne();
            }
            if (0 === strncasecmp($method, 'AllBy', 5)) { // support getAllByNameAndSex etc.
                $method = substr($method, 5);
                $arrKeys = explode('And', $method);
                if (count($arrKeys) !== count($arrArgs)) {
                    throw new Exception('Parameter quantity does not correspond.');
                }

                return $this->where(array_combine($arrKeys, $arrArgs))->getAll();
            }

            return $this->top((int) (substr($method, 3)));
        }

        // 查询组件
        if (!$this->objCallSelect) {
            throw new Exception(sprintf('Select do not implement magic method %s.', $method));
        }

        // 调用事件
        return $this->objCallSelect->{$method}(...$arrArgs);
    }

    /**
     * 返回数据库连接对象
     *
     * @return \Leevel\Database\Connect
     */
    public function databaseConnect()
    {
        return $this->objConnect;
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
     * @param object $objCallSelect
     *
     * @return $this
     */
    public function registerCallSelect($objCallSelect)
    {
        $this->objCallSelect = $objCallSelect;
        if (method_exists($this->objCallSelect, 'registerSelect')) {
            $this->objCallSelect->registerSelect($this);
        }

        return $this;
    }

    /**
     * 原生 sql 查询数据 select.
     *
     * @param null|callable|select|string $mixData
     * @param array                       $arrBind
     * @param bool                        $bFlag   指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function select($mixData = null, $arrBind = [], $bFlag = false)
    {
        if (!Type::these($mixData, [
            'string',
            'null',
            'callback',
        ]) && !$mixData instanceof self) {
            throw new Exception('Unsupported parameters.');
        }

        // 查询对象直接查询
        if ($mixData instanceof self) {
            return $mixData->select(null, $arrBind, $bFlag);
        }

        // 回调
        if (!is_string($mixData) && is_callable($mixData)) {
            call_user_func_array($mixData, [
                &$this,
            ]);
            $mixData = null;
        }

        // 调用查询
        if (null === $mixData) {
            return $this->get(null, $bFlag);
        }

        $this->sql($bFlag)->setNativeSql('select');

        return $this->{'runNativeSql'}([
            $mixData,
            $arrBind,
        ]);
    }

    /**
     * 插入数据 insert (支持原生 sql).
     *
     * @param array|string $mixData
     * @param array        $arrBind
     * @param bool         $booReplace
     * @param bool         $bFlag      指示是否不做任何操作只返回 SQL
     *
     * @return int 最后插入ID
     */
    public function insert($mixData, $arrBind = [], $booReplace = false, $bFlag = false)
    {
        if (!Type::these($mixData, [
            'string',
            'array',
        ])) {
            throw new Exception('Unsupported parameters.');
        }

        // 绑定参数
        $arrBind = array_merge($this->getBindParams(), $arrBind);

        // 构造数据插入
        if (is_array($mixData)) {
            $intQuestionMark = 0;
            $arrBindData = $this->getBindData($mixData, $arrBind, $intQuestionMark);
            $arrField = $arrBindData[0];
            $arrValue = $arrBindData[1];
            $sTableName = $this->getCurrentTable();

            foreach ($arrField as &$strField) {
                $strField = $this->qualifyOneColumn($strField, $sTableName);
            }

            // 构造 insert 语句
            if ($arrValue) {
                $arrSql = [];
                $arrSql[] = ($booReplace ? 'REPLACE' : 'INSERT').' INTO';
                $arrSql[] = $this->parseTable();
                $arrSql[] = '('.implode(',', $arrField).')';
                $arrSql[] = 'VALUES';
                $arrSql[] = '('.implode(',', $arrValue).')';
                $mixData = implode(' ', $arrSql);
                unset($arrBindData, $arrField, $arrValue, $arrSql);
            }
        }
        $arrBind = array_merge($this->getBindParams(), $arrBind);

        // 执行查询
        $this->sql($bFlag)->setNativeSql(false === $booReplace ? 'insert' : 'replace');

        return $this->{'runNativeSql'}([
            $mixData,
            $arrBind,
        ]);
    }

    /**
     * 批量插入数据 insertAll.
     *
     * @param array $arrData
     * @param array $arrBind
     * @param bool  $booReplace
     * @param bool  $bFlag      指示是否不做任何操作只返回 SQL
     *
     * @return int 最后插入ID
     */
    public function insertAll($arrData, $arrBind = [], $booReplace = false, $bFlag = false)
    {
        if (!is_array($arrData)) {
            throw new Exception('Unsupported parameters.');
        }

        // 绑定参数
        $arrBind = array_merge($this->getBindParams(), $arrBind);

        // 构造数据批量插入
        if (is_array($arrData)) {
            $arrDataResult = [];
            $intQuestionMark = 0;
            $sTableName = $this->getCurrentTable();
            foreach ($arrData as $intKey => $arrTemp) {
                if (!is_array($arrTemp)) {
                    continue;
                }
                $arrBindData = $this->getBindData($arrTemp, $arrBind, $intQuestionMark, $intKey);
                if (0 === $intKey) {
                    $arrField = $arrBindData[0];
                    foreach ($arrField as &$strField) {
                        $strField = $this->qualifyOneColumn($strField, $sTableName);
                    }
                }
                $arrValue = $arrBindData[1];
                if ($arrValue) {
                    $arrDataResult[] = '('.implode(',', $arrValue).')';
                }
            }

            // 构造 insertAll 语句
            if ($arrDataResult) {
                $arrSql = [];
                $arrSql[] = ($booReplace ? 'REPLACE' : 'INSERT').' INTO';
                $arrSql[] = $this->parseTable();
                $arrSql[] = '('.implode(',', $arrField).')';
                $arrSql[] = 'VALUES';
                $arrSql[] = implode(',', $arrDataResult);
                $mixData = implode(' ', $arrSql);
                unset($arrField, $arrValue, $arrSql, $arrDataResult);
            }
        }
        $arrBind = array_merge($this->getBindParams(), $arrBind);

        // 执行查询
        $this->sql($bFlag)->setNativeSql(false === $booReplace ? 'insert' : 'replace');

        return $this->{'runNativeSql'}([
            $mixData,
            $arrBind,
        ]);
    }

    /**
     * 更新数据 update (支持原生 sql).
     *
     * @param array|string $mixData
     * @param array        $arrBind
     * @param bool         $bFlag   指示是否不做任何操作只返回 SQL
     *
     * @return int 影响记录
     */
    public function update($mixData, $arrBind = [], $bFlag = false)
    {
        if (!Type::these($mixData, [
            'string',
            'array',
        ])) {
            throw new Exception('Unsupported parameters.');
        }

        // 绑定参数
        $arrBind = array_merge($this->getBindParams(), $arrBind);

        // 构造数据更新
        if (is_array($mixData)) {
            $intQuestionMark = 0;
            $arrBindData = $this->getBindData($mixData, $arrBind, $intQuestionMark);
            $arrField = $arrBindData[0];
            $arrValue = $arrBindData[1];
            $sTableName = $this->getCurrentTable();

            // SET 语句
            $arrSetData = [];
            foreach ($arrField as $intKey => $strField) {
                $strField = $this->qualifyOneColumn($strField, $sTableName);
                $arrSetData[] = $strField.' = '.$arrValue[$intKey];
            }

            // 构造 update 语句
            if ($arrValue) {
                $arrSql = [];
                $arrSql[] = 'UPDATE';
                $arrSql[] = ltrim($this->parseFrom(), 'FROM ');
                $arrSql[] = 'SET '.implode(',', $arrSetData);
                $arrSql[] = $this->parseWhere();
                $arrSql[] = $this->parseOrder();
                $arrSql[] = $this->parseLimitcount();
                $arrSql[] = $this->parseForUpdate();
                $mixData = implode(' ', $arrSql);
                unset($arrBindData, $arrField, $arrValue, $arrSetData, $arrSql);
            }
        }
        $arrBind = array_merge($this->getBindParams(), $arrBind);

        $this->sql($bFlag)->setNativeSql('update');

        return $this->{'runNativeSql'}([
            $mixData,
            $arrBind,
        ]);
    }

    /**
     * 更新某个字段的值
     *
     * @param string $strColumn
     * @param mixed  $mixValue
     * @param array  $arrBind
     * @param bool   $bFlag     指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateColumn($strColumn, $mixValue, $arrBind = [], $bFlag = false)
    {
        if (!is_string($strColumn)) {
            throw new Exception('Unsupported parameters.');
        }

        return $this->sql($bFlag)->update([
            $strColumn => $mixValue,
        ], $arrBind);
    }

    /**
     * 字段递增.
     *
     * @param string $strColumn
     * @param int    $intStep
     * @param array  $arrBind
     * @param bool   $bFlag     指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateIncrease($strColumn, $intStep = 1, $arrBind = [], $bFlag = false)
    {
        return $this->sql($bFlag)->updateColumn($strColumn, '{['.$strColumn.']+'.$intStep.'}', $arrBind);
    }

    /**
     * 字段减少.
     *
     * @param string $strColumn
     * @param int    $intStep
     * @param array  $arrBind
     * @param bool   $bFlag     指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function updateDecrease($strColumn, $intStep = 1, $arrBind = [], $bFlag = false)
    {
        return $this->sql($bFlag)->updateColumn($strColumn, '{['.$strColumn.']-'.$intStep.'}', $arrBind);
    }

    /**
     * 删除数据 delete (支持原生 sql).
     *
     * @param null|string $mixData
     * @param array       $arrBind
     * @param bool        $bFlag   指示是否不做任何操作只返回 SQL
     *
     * @return int 影响记录
     */
    public function delete($mixData = null, $arrBind = [], $bFlag = false)
    {
        if (!Type::these($mixData, [
            'string',
            'null',
        ])) {
            throw new Exception('Unsupported parameters.');
        }

        // 构造数据删除
        if (null === $mixData) {
            // 构造 delete 语句
            $arrSql = [];
            $arrSql[] = 'DELETE';
            if (empty($this->arrOption['using'])) { // join 方式关联删除
                $arrSql[] = $this->parseTable(true, true);
                $arrSql[] = $this->parseFrom();
            } else { // using 方式关联删除
                $arrSql[] = 'FROM '.$this->parseTable(true);
                $arrSql[] = $this->parseUsing(true);
            }
            $arrSql[] = $this->parseWhere();
            $arrSql[] = $this->parseOrder(true);
            $arrSql[] = $this->parseLimitcount(true, true);
            $mixData = implode(' ', $arrSql);
            unset($arrSql);
        }
        $arrBind = array_merge($this->getBindParams(), $arrBind);

        $this->sql($bFlag)->setNativeSql('delete');

        return $this->{'runNativeSql'}([
            $mixData,
            $arrBind,
        ]);
    }

    /**
     * 清空表重置自增 ID.
     *
     * @param bool $bFlag 指示是否不做任何操作只返回 SQL
     */
    public function truncate($bFlag = false)
    {
        // 构造 truncate 语句
        $arrSql = [];
        $arrSql[] = 'TRUNCATE TABLE';
        $arrSql[] = $this->parseTable(true);
        $arrSql = implode(' ', $arrSql);

        $this->sql($bFlag)->setNativeSql('statement');

        return $this->{'runNativeSql'}([
            $arrSql,
        ]);
    }

    /**
     * 声明 statement 运行一般 sql,无返回.
     *
     * @param string $strData
     * @param array  $arrBind
     * @param bool   $bFlag   指示是否不做任何操作只返回 SQL
     */
    public function statement(string $strData, $arrBind = [], $bFlag = false)
    {
        $this->sql($bFlag)->setNativeSql('statement');

        return $this->{'runNativeSql'}([
            $strData,
            $arrBind,
        ]);
    }

    /**
     * 返回一条记录.
     *
     * @param bool $bFlag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function getOne($bFlag = false)
    {
        return $this->sql($bFlag, true)->one()->query();
    }

    /**
     * 返回所有记录.
     *
     * @param bool $bFlag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function getAll($bFlag = false)
    {
        if ($this->arrOption['limitquery']) {
            return $this->sql($bFlag, true)->query();
        }

        return $this->sql($bFlag, true)->all()->query();
    }

    /**
     * 返回最后几条记录.
     *
     * @param mixed $nNum
     * @param bool  $bFlag 指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function get($nNum = null, $bFlag = false)
    {
        if (null !== $nNum) {
            return $this->sql($bFlag, true)->top($nNum)->query();
        }

        return $this->sql($bFlag, true)->query();
    }

    /**
     * 返回一个字段的值
     *
     * @param string $strField
     * @param bool   $bFlag    指示是否不做任何操作只返回 SQL
     *
     * @return mixed
     */
    public function value($strField, $bFlag = false)
    {
        $arrRow = $this->sql($bFlag, true)->asDefault()->setColumns($strField)->getOne();
        if (true === $bFlag) {
            return $arrRow;
        }

        return $arrRow[$strField] ?? null;
    }

    /**
     * 返回一列数据.
     *
     * @param mixed  $mixFieldValue
     * @param string $strFieldKey
     * @param bool   $bFlag         指示是否不做任何操作只返回 SQL
     *
     * @return array
     */
    public function lists($mixFieldValue, $strFieldKey = null, $bFlag = false)
    {
        // 纵然有弱水三千，我也只取一瓢 (第一个字段为值，第二个字段为键值，多余的字段丢弃)
        $arrField = [];
        if (is_array($mixFieldValue)) {
            $arrField = $mixFieldValue;
        } else {
            $arrField[] = $mixFieldValue;
        }
        if (is_string($strFieldKey)) {
            $arrField[] = $strFieldKey;
        }

        // 解析结果
        $arrResult = [];
        foreach ($this->sql($bFlag, true)->asDefault()->setColumns($arrField)->getAll() as $arrTemp) {
            if (true === $bFlag) {
                $arrResult[] = $arrTemp;

                continue;
            }

            $arrTemp = $arrTemp;
            if (1 === count($arrTemp)) {
                $arrResult[] = reset($arrTemp);
            } else {
                $mixValue = array_shift($arrTemp);
                $mixKey = array_shift($arrTemp);
                $arrResult[$mixKey] = $mixValue;
            }
        }

        return $arrResult;
    }

    /**
     * 数据分块处理.
     *
     * @param int      $intCount
     * @param callable $calCallback
     *
     * @return bool
     */
    public function chunk($intCount, callable $calCallback)
    {
        $mixResult = $this->forPage($intPage = 1, $intCount)->getAll();

        while (count($mixResult) > 0) {
            if (false === call_user_func_array($calCallback, [
                $mixResult,
                $intPage,
            ])) {
                return false;
            }
            $intPage++;
            $mixResult = $this->forPage($intPage, $intCount)->getAll();
        }

        return true;
    }

    /**
     * 数据分块处理依次回调.
     *
     * @param int      $intCount
     * @param callable $calCallback
     *
     * @return bool
     */
    public function each($intCount, callable $calCallback)
    {
        return $this->chunk($intCount, function ($mixResult, $intPage) use ($calCallback) {
            foreach ($mixResult as $intKey => $mixValue) {
                if (false === $calCallback($mixValue, $intKey, $intPage)) {
                    return false;
                }
            }
        });
    }

    /**
     * 总记录数.
     *
     * @param string $strField
     * @param string $sAlias
     * @param bool   $bFlag    指示是否不做任何操作只返回 SQL
     *
     * @return int
     */
    public function getCount($strField = '*', $sAlias = 'row_count', $bFlag = false)
    {
        $arrRow = (array) $this->sql($bFlag, true)->asDefault()->count($strField, $sAlias)->get();
        if (true === $bFlag) {
            return $arrRow;
        }

        return (int) ($arrRow[$sAlias]);
    }

    /**
     * 平均数.
     *
     * @param string $strField
     * @param string $sAlias
     * @param bool   $bFlag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getAvg($strField, $sAlias = 'avg_value', $bFlag = false)
    {
        $arrRow = (array) $this->sql($bFlag, true)->asDefault()->avg($strField, $sAlias)->get();
        if (true === $bFlag) {
            return $arrRow;
        }

        return (float) $arrRow[$sAlias];
    }

    /**
     * 最大值
     *
     * @param string $strField
     * @param string $sAlias
     * @param bool   $bFlag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getMax($strField, $sAlias = 'max_value', $bFlag = false)
    {
        $arrRow = (array) $this->sql($bFlag, true)->asDefault()->max($strField, $sAlias)->get();
        if (true === $bFlag) {
            return $arrRow;
        }

        return (float) $arrRow[$sAlias];
    }

    /**
     * 最小值
     *
     * @param string $strField
     * @param string $sAlias
     * @param bool   $bFlag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getMin($strField, $sAlias = 'min_value', $bFlag = false)
    {
        $arrRow = (array) $this->sql($bFlag, true)->asDefault()->min($strField, $sAlias)->get();
        if (true === $bFlag) {
            return $arrRow;
        }

        return (float) $arrRow[$sAlias];
    }

    /**
     * 合计
     *
     * @param string $strField
     * @param string $sAlias
     * @param bool   $bFlag    指示是否不做任何操作只返回 SQL
     *
     * @return number
     */
    public function getSum($strField, $sAlias = 'sum_value', $bFlag = false)
    {
        $arrRow = (array) $this->sql($bFlag, true)->asDefault()->sum($strField, $sAlias)->get();
        if (true === $bFlag) {
            return $arrRow;
        }

        return $arrRow[$sAlias];
    }

    /**
     * 分页查询.
     *
     * @param int   $intPerPage
     * @param mixed $mixCols
     * @param array $arrOption
     *
     * @return array
     */
    public function paginate($intPerPage = 10, $mixCols = '*', array $arrOption = [])
    {
        $objPage = new page_with_total($intPerPage, $this->getPaginateCount($mixCols), $arrOption);

        return [
            $objPage,
            $this->limit($objPage->getFirstRecord(), $intPerPage)->getAll(),
        ];
    }

    /**
     * 简单分页查询.
     *
     * @param int   $intPerPage
     * @param mixed $mixCols
     * @param array $arrOption
     *
     * @return array
     */
    public function simplePaginate($intPerPage = 10, $mixCols = '*', array $arrOption = [])
    {
        $objPage = new PageWithoutTotal($intPerPage, $arrOption);

        return [
            $objPage,
            $this->limit($objPage->getFirstRecord(), $intPerPage)->getAll(),
        ];
    }

    /**
     * 取得分页查询记录数量.
     *
     * @param mixed $mixCols
     *
     * @return int
     */
    public function getPaginateCount($mixCols = '*')
    {
        $this->backupPaginateArgs();
        $intCount = $this->getCount(is_array($mixCols) ? reset($mixCols) : $mixCols);
        $this->restorePaginateArgs();

        return $intCount;
    }

    /**
     * 根据分页设置条件.
     *
     * @param int $intPage
     * @param int $intPerPage
     *
     * @return $this
     */
    public function forPage($intPage, $intPerPage = 15)
    {
        return $this->limit(($intPage - 1) * $intPerPage, $intPerPage);
    }

    /**
     * 时间控制语句开始.
     */
    public function time()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();

        $this->setInTimeCondition(isset($arrArgs[0]) && in_array($arrArgs[0], [
            'date',
            'month',
            'year',
            'day',
        ], true) ? $arrArgs[0] : null);
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
     * @param bool $bFlag     指示是否不做任何操作只返回 SQL
     * @param bool $bQuickSql 如果快捷为 true,而原来的 $booOnlyMakeSql 为 true，则不做任何修改，只能通过手动方式修改
     *
     * @return $this
     */
    public function sql($bFlag = true, $bQuickSql = false)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        if (false === $bFlag && true === $bQuickSql && true === $this->booOnlyMakeSql) { // 优先级最高 $this->sql(true, false)
            return $this;
        }
        $this->booOnlyMakeSql = (bool) $bFlag;

        return $this;
    }

    /**
     * 设置是否查询主服务器.
     *
     * @param bool $booMaster
     *
     * @return $this
     */
    public function asMaster($booMaster = false)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->arrQueryParams['master'] = $booMaster;

        return $this;
    }

    /**
     * 设置查询结果类型.
     *
     * @param mixed $mixType
     * @param mixed $mixValue
     *
     * @return $this
     */
    public function asFetchType($mixType, $mixValue = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_array($mixType)) {
            $this->arrQueryParams['fetch_type'] = array_merge($this->arrQueryParams['fetch_type'], $mixType);
        } else {
            if (null === $mixValue) {
                $this->arrQueryParams['fetch_type']['fetch_type'] = $mixType;
            } else {
                $this->arrQueryParams['fetch_type'][$mixType] = $mixValue;
            }
        }

        return $this;
    }

    /**
     * 设置以类返会结果.
     *
     * @param string $sClassName
     *
     * @return $this
     */
    public function asClass($sClassName)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->arrQueryParams['as_class'] = $sClassName;
        $this->arrQueryParams['as_default'] = false;

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

        $this->arrQueryParams['as_class'] = null;
        $this->arrQueryParams['as_default'] = true;

        return $this;
    }

    /**
     * 设置是否以集合返回.
     *
     * @param string $bAsCollection
     *
     * @return $this
     */
    public function asCollection($bAsCollection = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->arrQueryParams['as_collection'] = $bAsCollection;
        $this->arrQueryParams['as_default'] = false;

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
        } elseif (array_key_exists($sOption, static::$arrOptionDefault)) {
            $this->arrOption[$sOption] = static::$arrOptionDefault[$sOption];
        }

        return $this;
    }

    /**
     * prefix 查询.
     *
     * @param array|string $mixPrefix
     *
     * @return $this
     */
    public function prefix($mixPrefix)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $mixPrefix = Arr::normalize($mixPrefix);

        foreach ($mixPrefix as $strValue) {
            $strValue = Arr::normalize($strValue);
            foreach ($strValue as $strTemp) {
                $strTemp = trim($strTemp);
                if (empty($strTemp)) {
                    continue;
                }
                $this->arrOption['prefix'][] = strtoupper($strTemp);
            }
        }

        return $this;
    }

    /**
     * 添加一个要查询的表及其要查询的字段.
     *
     * @param mixed        $mixTable
     * @param array|string $mixCols
     *
     * @return $this
     */
    public function table($mixTable, $mixCols = '*')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setIsTable(true);
        $this->addJoin('inner join', $mixTable, $mixCols);
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

        foreach ($mixName as $sAlias => $sTable) {
            // 字符串指定别名
            if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $sTable, $arrMatch)) {
                $sAlias = $arrMatch[2];
                $sTable = $arrMatch[1];
            }

            if (!is_string($sAlias)) {
                $sAlias = $sTable;
            }

            // 确定 table_name 和 schema
            $arrTemp = explode('.', $sTable);
            if (isset($arrTemp[1])) {
                $sSchema = $arrTemp[0];
                $sTableName = $arrTemp[1];
            } else {
                $sSchema = null;
                $sTableName = $sTable;
            }

            // 获得一个唯一的别名
            $sAlias = $this->uniqueAlias(empty($sAlias) ? $sTableName : $sAlias);

            $this->arrOption['using'][$sAlias] = [
                'table_name' => $sTable,
                'schema'     => $sSchema,
            ];
        }

        return $this;
    }

    /**
     * 添加字段.
     *
     * @param mixed  $mixCols
     * @param string $strTable
     *
     * @return $this
     */
    public function columns($mixCols = '*', $strTable = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (null === $strTable) {
            $strTable = $this->getCurrentTable();
        }
        $this->addCols($strTable, $mixCols);

        return $this;
    }

    /**
     * 设置字段.
     *
     * @param mixed  $mixCols
     * @param string $strTable
     *
     * @return $this
     */
    public function setColumns($mixCols = '*', $strTable = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (null === $strTable) {
            $strTable = $this->getCurrentTable();
        }

        $this->arrOption['columns'] = [];
        $this->addCols($strTable, $mixCols);

        return $this;
    }

    /**
     * 设置一个或多个字段的映射名，如果 $sMappingTo 为 null，则取消对指定字段的映射.
     *
     * @param array|string $mixName
     * @param null|string  $sMappingTo
     *
     * @return $this
     */
    public function columnsMapping($mixName, $sMappingTo = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_array($mixName)) {
            $this->arrColumnsMapping = array_merge($this->arrColumnsMapping, $mixName);
        } else {
            if (empty($sMappingTo)) {
                if (isset($this->arrColumnsMapping[$mixName])) {
                    unset($this->arrColumnsMapping[$mixName]);
                }
            } else {
                $this->arrColumnsMapping[$mixName] = $sMappingTo;
            }
        }

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
     * @param mixed $mixCond
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
     * @param mixed $mixValue
     * @param int   $intType
     *
     * @return $this
     */
    public function bind($mixName, $mixValue = null, $intType = PDO::PARAM_STR)
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
                $this->arrBindParams[$mixKey] = $item;
            }
        } else {
            if (!is_array($mixValue)) {
                $mixValue = [
                    $mixValue,
                    $intType,
                ];
            }
            $this->arrBindParams[$mixName] = $mixValue;
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
        if (!isset(static::$arrIndexTypes[$sType])) {
            throw new Exception(sprintf('Invalid Index type %s.', $sType));
        }
        $sType = strtoupper($sType);
        $mixIndex = Arr::normalize($mixIndex);
        foreach ($mixIndex as $strValue) {
            $strValue = Arr::normalize($strValue);
            foreach ($strValue as $strTemp) {
                $strTemp = trim($strTemp);
                if (empty($strTemp)) {
                    continue;
                }
                if (empty($this->arrOption['index'][$sType])) {
                    $this->arrOption['index'][$sType] = [];
                }
                $this->arrOption['index'][$sType][] = $strTemp;
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
     * @param mixed        $mixTable 同 table $mixTable
     * @param array|string $mixCols  同 table $mixCols
     * @param mixed        $mixCond  同 where $mixCond
     *
     * @return $this
     */
    public function join($mixTable, $mixCols, $mixCond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();
        array_unshift($arrArgs, 'inner join');

        return $this->{'addJoin'}(...$arrArgs);
    }

    /**
     * innerJoin 查询.
     *
     * @param mixed        $mixTable 同 table $mixTable
     * @param array|string $mixCols  同 table $mixCols
     * @param mixed        $mixCond  同 where $mixCond
     *
     * @return $this
     */
    public function innerJoin($mixTable, $mixCols, $mixCond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();
        array_unshift($arrArgs, 'inner join');

        return $this->{'addJoin'}(...$arrArgs);
    }

    /**
     * leftJoin 查询.
     *
     * @param mixed        $mixTable 同 table $mixTable
     * @param array|string $mixCols  同 table $mixCols
     * @param mixed        $mixCond  同 where $mixCond
     *
     * @return $this
     */
    public function leftJoin($mixTable, $mixCols, $mixCond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();
        array_unshift($arrArgs, 'left join');

        return $this->{'addJoin'}(...$arrArgs);
    }

    /**
     * rightJoin 查询.
     *
     * @param mixed        $mixTable 同 table $mixTable
     * @param array|string $mixCols  同 table $mixCols
     * @param mixed        $mixCond  同 where $mixCond
     *
     * @return $this
     */
    public function rightJoin($mixTable, $mixCols, $mixCond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();
        array_unshift($arrArgs, 'right join');

        return $this->{'addJoin'}(...$arrArgs);
    }

    /**
     * fullJoin 查询.
     *
     * @param mixed        $mixTable 同 table $mixTable
     * @param array|string $mixCols  同 table $mixCols
     * @param mixed        $mixCond  同 where $mixCond
     *
     * @return $this
     */
    public function fullJoin($mixTable, $mixCols, $mixCond)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();
        array_unshift($arrArgs, 'full join');

        return $this->{'addJoin'}(...$arrArgs);
    }

    /**
     * crossJoin 查询.
     *
     * @param mixed        $mixTable 同 table $mixTable
     * @param array|string $mixCols  同 table $mixCols
     *
     * @return $this
     */
    public function crossJoin($mixTable, $mixCols)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();
        array_unshift($arrArgs, 'cross join');

        return $this->{'addJoin'}(...$arrArgs);
    }

    /**
     * naturalJoin 查询.
     *
     * @param mixed        $mixTable 同 table $mixTable
     * @param array|string $mixCols  同 table $mixCols
     *
     * @return $this
     */
    public function naturalJoin($mixTable, $mixCols)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $arrArgs = func_get_args();
        array_unshift($arrArgs, 'natural join');

        return $this->{'addJoin'}(...$arrArgs);
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

        if (!isset(static::$arrUnionTypes[$sType])) {
            throw new Exception(sprintf('Invalid UNION type %s.', $sType));
        }

        if (!is_array($mixSelect)) {
            $mixSelect = (array) $mixSelect;
        }

        foreach ($mixSelect as $mixTemp) {
            $this->arrOption['union'][] = [
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
            foreach ($arrRes[1] as $strTemp) {
                $mixExpr[array_search('{'.base64_encode($strTemp).'}', $mixExpr, true)] = '{'.$strTemp.'}';
            }
        }

        $strTableName = $this->getCurrentTable();
        foreach ($mixExpr as $strValue) {
            // 处理条件表达式
            if (is_string($strValue) && false !== strpos($strValue, ',') && false !== strpos($strValue, '{') && preg_match_all('/{(.+?)}/', $strValue, $arrResTwo)) {
                $strValue = str_replace($arrResTwo[1][0], base64_encode($arrResTwo[1][0]), $strValue);
            }
            $strValue = Arr::normalize($strValue);
            // 还原
            if (!empty($arrResTwo)) {
                foreach ($arrResTwo[1] as $strTemp) {
                    $strValue[array_search('{'.base64_encode($strTemp).'}', $strValue, true)] = '{'.$strTemp.'}';
                }
            }

            foreach ($strValue as $strTemp) {
                $strTemp = trim($strTemp);
                if (empty($strTemp)) {
                    continue;
                }

                // 表达式支持
                $strTemp = $this->qualifyOneColumn($strTemp, $strTableName);
                $this->arrOption['group'][] = $strTemp;
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
            foreach ($arrRes[1] as $strTemp) {
                $mixExpr[array_search('{'.base64_encode($strTemp).'}', $mixExpr, true)] = '{'.$strTemp.'}';
            }
        }

        $strTableName = $this->getCurrentTable();
        foreach ($mixExpr as $strValue) {
            // 处理条件表达式
            if (is_string($strValue) && false !== strpos($strValue, ',') && false !== strpos($strValue, '{') && preg_match_all('/{(.+?)}/', $strValue, $arrResTwo)) {
                $strValue = str_replace($arrResTwo[1][0], base64_encode($arrResTwo[1][0]), $strValue);
            }
            $strValue = Arr::normalize($strValue);
            // 还原
            if (!empty($arrResTwo)) {
                foreach ($arrResTwo[1] as $strTemp) {
                    $strValue[array_search('{'.base64_encode($strTemp).'}', $strValue, true)] = '{'.$strTemp.'}';
                }
            }
            foreach ($strValue as $strTemp) {
                $strTemp = trim($strTemp);
                if (empty($strTemp)) {
                    continue;
                }

                // 表达式支持
                if (false !== strpos($strTemp, '{') && preg_match('/^{(.+?)}$/', $strTemp, $arrResThree)) {
                    $strTemp = $this->objConnect->qualifyExpression($arrResThree[1], $strTableName, $this->arrColumnsMapping);
                    if (preg_match('/(.*\W)('.'ASC'.'|'.'DESC'.')\b/si', $strTemp, $arrMatch)) {
                        $strTemp = trim($arrMatch[1]);
                        $sSort = strtoupper($arrMatch[2]);
                    } else {
                        $sSort = $sOrderDefault;
                    }
                    $this->arrOption['order'][] = $strTemp.' '.$sSort;
                } else {
                    $sCurrentTableName = $strTableName;
                    $sSort = $sOrderDefault;
                    if (preg_match('/(.*\W)('.'ASC'.'|'.'DESC'.')\b/si', $strTemp, $arrMatch)) {
                        $strTemp = trim($arrMatch[1]);
                        $sSort = strtoupper($arrMatch[2]);
                    }

                    if (!preg_match('/\(.*\)/', $strTemp)) {
                        if (preg_match('/(.+)\.(.+)/', $strTemp, $arrMatch)) {
                            $sCurrentTableName = $arrMatch[1];
                            $strTemp = $arrMatch[2];
                        }
                        if (isset($this->arrColumnsMapping[$strTemp])) {
                            $strTemp = $this->arrColumnsMapping[$strTemp];
                        }
                        $strTemp = $this->objConnect->qualifyTableOrColumn("{$sCurrentTableName}.{$strTemp}");
                    }
                    $this->arrOption['order'][] = $strTemp.' '.$sSort;
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
     * @param bool $bFlag 指示是否是一个 SELECT DISTINCT 查询（默认 true）
     *
     * @return $this
     */
    public function distinct($bFlag = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrOption['distinct'] = (bool) $bFlag;

        return $this;
    }

    /**
     * 总记录数.
     *
     * @param string $strField
     * @param string $sAlias
     *
     * @return $this
     */
    public function count($strField = '*', $sAlias = 'row_count')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->addAggregate('COUNT', $strField, $sAlias);
    }

    /**
     * 平均数.
     *
     * @param string $strField
     * @param string $sAlias
     *
     * @return $this
     */
    public function avg($strField, $sAlias = 'avg_value')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->addAggregate('AVG', $strField, $sAlias);
    }

    /**
     * 最大值
     *
     * @param string $strField
     * @param string $sAlias
     *
     * @return $this
     */
    public function max($strField, $sAlias = 'max_value')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->addAggregate('MAX', $strField, $sAlias);
    }

    /**
     * 最小值
     *
     * @param string $strField
     * @param string $sAlias
     *
     * @return $this
     */
    public function min($strField, $sAlias = 'min_value')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->addAggregate('MIN', $strField, $sAlias);
    }

    /**
     * 合计
     *
     * @param string $strField
     * @param string $sAlias
     *
     * @return $this
     */
    public function sum($strField, $sAlias = 'sum_value')
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->addAggregate('SUM', $strField, $sAlias);
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
        $this->arrOption['limitcount'] = 1;
        $this->arrOption['limitoffset'] = null;
        $this->arrOption['limitquery'] = false;

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
        $this->arrOption['limitcount'] = null;
        $this->arrOption['limitoffset'] = null;
        $this->arrOption['limitquery'] = true;

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
     * @param number $nOffset
     * @param number $nCount
     *
     * @return $this
     */
    public function limit($nOffset = 0, $nCount = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        if (null === $nCount) {
            return $this->top($nOffset);
        }
        $this->arrOption['limitcount'] = abs((int) $nCount);
        $this->arrOption['limitoffset'] = abs((int) $nOffset);
        $this->arrOption['limitquery'] = true;

        return $this;
    }

    /**
     * 是否构造一个 FOR UPDATE 查询.
     *
     * @param bool $bFlag
     *
     * @return $this
     */
    public function forUpdate($bFlag = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }
        $this->arrOption['forupdate'] = (bool) $bFlag;

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
        $arrSql = [
            'SELECT',
        ];

        foreach (array_keys($this->arrOption) as $sOption) {
            if ('from' === $sOption) {
                $arrSql['from'] = '';
            } elseif ('union' === $sOption) {
                continue;
            } else {
                $method = 'parse'.ucfirst($sOption);
                if (method_exists($this, $method)) {
                    $arrSql[$sOption] = $this->{$method}();
                }
            }
        }

        $arrSql['from'] = $this->parseFrom();
        foreach ($arrSql as $nOffset => $sOption) { // 删除空元素
            if ('' === trim($sOption)) {
                unset($arrSql[$nOffset]);
            }
        }

        $arrSql[] = $this->parseUnion();
        $sLastSql = trim(implode(' ', $arrSql));

        if (true === $booWithLogicGroup) {
            return static::LOGIC_GROUP_LEFT.$sLastSql.static::LOGIC_GROUP_RIGHT;
        }

        return $sLastSql;
    }

    /**
     * 解析 prefix 分析结果.
     *
     * @return string
     */
    protected function parsePrefix()
    {
        if (empty($this->arrOption['prefix'])) {
            return '';
        }

        return implode(' ', $this->arrOption['prefix']);
    }

    /**
     * 解析 distinct 分析结果.
     *
     * @return string
     */
    protected function parseDistinct()
    {
        if (!$this->arrOption['distinct']) {
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
        if (empty($this->arrOption['columns'])) {
            return '';
        }

        $arrColumns = [];
        foreach ($this->arrOption['columns'] as $arrEntry) {
            list($sTableName, $sCol, $sAlias) = $arrEntry;

            // 表达式支持
            if (false !== strpos($sCol, '{') && preg_match('/^{(.+?)}$/', $sCol, $arrRes)) {
                $arrColumns[] = $this->objConnect->qualifyExpression($arrRes[1], $sTableName, $this->arrColumnsMapping);
            } else {
                if (isset($this->arrColumnsMapping[$sCol])) {
                    $sCol = $this->arrColumnsMapping[$sCol];
                }
                if ('*' !== $sCol && $sAlias) {
                    $arrColumns[] = $this->objConnect->qualifyTableOrColumn("{$sTableName}.{$sCol}", $sAlias, 'AS');
                } else {
                    $arrColumns[] = $this->objConnect->qualifyTableOrColumn("{$sTableName}.{$sCol}");
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
        if (empty($this->arrOption['aggregate'])) {
            return '';
        }

        $arrColumns = [];
        foreach ($this->arrOption['aggregate'] as $arrAggregate) {
            list(, $sField, $sAlias) = $arrAggregate;
            if ($sAlias) {
                $arrColumns[] = $sField.' AS '.$sAlias;
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
        if (empty($this->arrOption['from'])) {
            return '';
        }

        $arrFrom = [];
        foreach ($this->arrOption['from'] as $sAlias => $arrTable) {
            $sTmp = '';
            // 如果不是第一个 FROM，则添加 JOIN
            if (!empty($arrFrom)) {
                $sTmp .= strtoupper($arrTable['join_type']).' ';
            }

            // 表名子表达式支持
            if (false !== strpos($arrTable['table_name'], '(')) {
                $sTmp .= $arrTable['table_name'].' '.$sAlias;
            } elseif ($sAlias === $arrTable['table_name']) {
                $sTmp .= $this->objConnect->qualifyTableOrColumn("{$arrTable['schema']}.{$arrTable['table_name']}");
            } else {
                $sTmp .= $this->objConnect->qualifyTableOrColumn("{$arrTable['schema']}.{$arrTable['table_name']}", $sAlias);
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
        if (empty($this->arrOption['from'])) {
            return '';
        }

        // 如果为删除,没有 join 则返回为空
        if (true === $booForDelete && 1 === count($this->arrOption['from'])) {
            return '';
        }

        foreach ($this->arrOption['from'] as $sAlias => $arrTable) {
            if ($sAlias === $arrTable['table_name']) {
                return $this->objConnect->qualifyTableOrColumn("{$arrTable['schema']}.{$arrTable['table_name']}");
            }
            if (true === $booOnlyAlias) {
                return $sAlias;
            }
            // 表名子表达式支持
            if (false !== strpos($arrTable['table_name'], '(')) {
                return $arrTable['table_name'].' '.$sAlias;
            }

            return $this->objConnect->qualifyTableOrColumn("{$arrTable['schema']}.{$arrTable['table_name']}", $sAlias);
            break;
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
        if (false === $booForDelete || empty($this->arrOption['using'])) {
            return '';
        }

        $arrUsing = [];
        $arrOptionUsing = $this->arrOption['using'];
        foreach ($this->arrOption['from'] as $sAlias => $arrTable) { // table 自动加入
            $arrOptionUsing[$sAlias] = $arrTable;

            break;
        }

        foreach ($arrOptionUsing as $sAlias => $arrTable) {
            if ($sAlias === $arrTable['table_name']) {
                $arrUsing[] = $this->objConnect->qualifyTableOrColumn("{$arrTable['schema']}.{$arrTable['table_name']}");
            } else {
                $arrUsing[] = $this->objConnect->qualifyTableOrColumn("{$arrTable['schema']}.{$arrTable['table_name']}", $sAlias);
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
            if (empty($this->arrOption['index'][$sType])) {
                continue;
            }
            $strIndex .= ($strIndex ? ' ' : '').$sType.' INDEX('.implode(',', $this->arrOption['index'][$sType]).')';
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
        if (empty($this->arrOption['where'])) {
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
        if (empty($this->arrOption['union'])) {
            return '';
        }

        $sSql = '';
        if ($this->arrOption['union']) {
            $nOptions = count($this->arrOption['union']);
            foreach ($this->arrOption['union'] as $nCnt => $arrUnion) {
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
        if (empty($this->arrOption['order'])) {
            return '';
        }
        // 删除存在 join, order 无效
        if (true === $booForDelete && (count($this->arrOption['from']) > 1 || !empty($this->arrOption['using']))) {
            return '';
        }

        return 'ORDER BY '.implode(',', array_unique($this->arrOption['order']));
    }

    /**
     * 解析 group 分析结果.
     *
     * @return string
     */
    protected function parseGroup()
    {
        if (empty($this->arrOption['group'])) {
            return '';
        }

        return 'GROUP BY '.implode(',', $this->arrOption['group']);
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
        if (empty($this->arrOption['having'])) {
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
        if (true === $booForDelete && (count($this->arrOption['from']) > 1 || !empty($this->arrOption['using']))) {
            return '';
        }

        if (true === $booNullLimitOffset) {
            $this->arrOption['limitoffset'] = null;
        }

        if (null === $this->arrOption['limitoffset'] && null === $this->arrOption['limitcount']) {
            return '';
        }

        if (method_exists($this->objConnect, 'parseLimitcount')) {
            return $this->objConnect->{'parseLimitcount'}([
                $this->arrOption['limitcount'],
                $this->arrOption['limitoffset'],
            ]);
        }

        throw new BadMethodCallException(sprintf('Connect method %s is not exits', 'parseLimitcount'));
    }

    /**
     * 解析 forupdate 分析结果.
     *
     * @return string
     */
    protected function parseForUpdate()
    {
        if (!$this->arrOption['forupdate']) {
            return '';
        }

        return 'FOR UPDATE';
    }

    /**
     * 解析 condition　条件（包括 where,having）.
     *
     * @param string $sCondType
     * @param bool   $booChild
     *
     * @return string
     */
    protected function analyseCondition($sCondType, $booChild = false)
    {
        if (!$this->arrOption[$sCondType]) {
            return '';
        }

        $arrSqlCond = [];
        $strTable = $this->getCurrentTable();
        foreach ($this->arrOption[$sCondType] as $sKey => $mixCond) {
            // 逻辑连接符
            if (in_array($mixCond, [
                static::LOGIC_AND,
                static::LOGIC_OR,
            ], true)) {
                $arrSqlCond[] = strtoupper($mixCond);

                continue;
            }

            // 特殊处理
            if (is_string($sKey)) {
                if (in_array($sKey, [
                    'string__',
                ], true)) {
                    $arrSqlCond[] = implode(' AND ', $mixCond);
                }
            } elseif (is_array($mixCond)) {
                // 表达式支持
                if (false !== strpos($mixCond[0], '{') && preg_match('/^{(.+?)}$/', $mixCond[0], $arrRes)) {
                    $mixCond[0] = $this->objConnect->qualifyExpression($arrRes[1], $strTable, $this->arrColumnsMapping);
                } else {
                    // 字段处理
                    if (false !== strpos($mixCond[0], ',')) {
                        $arrTemp = explode(',', $mixCond[0]);
                        $mixCond[0] = $arrTemp[1];
                        $strCurrentTable = $mixCond[0];
                    } else {
                        $strCurrentTable = $strTable;
                    }

                    if (isset($this->arrColumnsMapping[$mixCond[0]])) {
                        $mixCond[0] = $this->arrColumnsMapping[$mixCond[0]];
                    }

                    $mixCond[0] = $this->objConnect->qualifyColumn($mixCond[0], $strCurrentTable);
                }

                // 分析是否存在自动格式化时间标识
                $strFindTime = null;
                if (0 === strpos($mixCond[1], '@')) {
                    foreach ([
                        'date',
                        'month',
                        'day',
                        'year',
                    ] as $strTimeType) {
                        if (0 === stripos($mixCond[1], '@'.$strTimeType)) {
                            $strFindTime = $strTimeType;
                            $mixCond[1] = ltrim(substr($mixCond[1], strlen($strTimeType) + 1));

                            break;
                        }
                    }
                    if (null === $strFindTime) {
                        throw new Exception('You are trying to an unsupported time processing grammar.');
                    }
                }

                // 格式化字段值，支持数组
                if (isset($mixCond[2])) {
                    $booIsArray = true;
                    if (!is_array($mixCond[2])) {
                        $mixCond[2] = (array) $mixCond[2];
                        $booIsArray = false;
                    }

                    foreach ($mixCond[2] as &$strTemp) {
                        // 对象子表达式支持
                        if ($strTemp instanceof self) {
                            $strTemp = $strTemp->makeSql(true);
                        }

                        // 回调方法子表达式支持
                        elseif (!is_string($strTemp) && is_callable($strTemp)) {
                            $objSelect = new static($this->objConnect);
                            $objSelect->setCurrentTable($this->getCurrentTable());
                            $mixResultCallback = call_user_func_array($strTemp, [
                                &$objSelect,
                            ]);
                            if (null === $mixResultCallback) {
                                $strTemp = $objSelect->makeSql(true);
                            } else {
                                $strTemp = $mixResultCallback;
                            }
                        }

                        // 字符串子表达式支持
                        elseif (is_string($strTemp) && 0 === strpos($strTemp, '(')) {
                        }

                        // 表达式支持
                        elseif (is_string($strTemp) && false !== strpos($strTemp, '{') && preg_match('/^{(.+?)}$/', $strTemp, $arrRes)) {
                            $strTemp = $this->objConnect->qualifyExpression($arrRes[1], $strTable, $this->arrColumnsMapping);
                        } else {
                            // 自动格式化时间
                            if (null !== $strFindTime) {
                                $strTemp = $this->parseTime($mixCond[0], $strTemp, $strFindTime);
                            }
                            $strTemp = $this->objConnect->qualifyColumnValue($strTemp);
                        }
                    }

                    if (false === $booIsArray || (1 === count($mixCond[2]) && 0 === strpos(trim($mixCond[2][0]), '('))) {
                        $mixCond[2] = reset($mixCond[2]);
                    }
                }

                // 拼接结果
                if (in_array($mixCond[1], [
                    'null',
                    'not null',
                ], true)) {
                    $arrSqlCond[] = $mixCond[0].' IS '.strtoupper($mixCond[1]);
                } elseif (in_array($mixCond[1], [
                    'in',
                    'not in',
                ], true)) {
                    $arrSqlCond[] = $mixCond[0].' '.strtoupper($mixCond[1]).' '.(is_array($mixCond[2]) ? '('.implode(',', $mixCond[2]).')' : $mixCond[2]);
                } elseif (in_array($mixCond[1], [
                    'between',
                    'not between',
                ], true)) {
                    if (!is_array($mixCond[2]) || count($mixCond[2]) < 2) {
                        throw new Exception('The [not] between parameter value must be an array of not less than two elements.');
                    }
                    $arrSqlCond[] = $mixCond[0].' '.strtoupper($mixCond[1]).' '.$mixCond[2][0].' AND '.$mixCond[2][1];
                } elseif (is_scalar($mixCond[2])) {
                    $arrSqlCond[] = $mixCond[0].' '.strtoupper($mixCond[1]).' '.$mixCond[2];
                } elseif (null === $mixCond[2]) {
                    $arrSqlCond[] = $mixCond[0].' IS NULL';
                }
            }
        }

        // 剔除第一个逻辑符
        array_shift($arrSqlCond);

        return (false === $booChild ? strtoupper($sCondType).' ' : '').implode(' ', $arrSqlCond);
    }

    /**
     * 别名条件.
     *
     * @param string $strConditionType
     * @param mixed  $mixCond
     *
     * @return $this
     */
    protected function aliasCondition($strConditionType, $mixCond)
    {
        if (!is_array($mixCond)) {
            $arrArgs = func_get_args();
            $this->addConditions($arrArgs[1], $strConditionType, $arrArgs[2] ?? null);
        } else {
            foreach ($mixCond as $arrTemp) {
                $this->addConditions($arrTemp[0], $strConditionType, $arrTemp[1]);
            }
        }

        return $this;
    }

    /**
     * 别名类型和逻辑.
     *
     * @param string $strType
     * @param string $strLogic
     * @param mixed  $mixCond
     *
     * @return $this
     */
    protected function aliasTypeAndLogic($strType, $strLogic, $mixCond)
    {
        $this->setTypeAndLogic($strType, $strLogic);

        if (!is_string($mixCond) && is_callable($mixCond)) {
            $objSelect = new static($this->objConnect);
            $objSelect->setCurrentTable($this->getCurrentTable());
            $mixResultCallback = call_user_func_array($mixCond, [
                &$objSelect,
            ]);
            if (null === $mixResultCallback) {
                $strParseType = 'parse'.ucwords($strType);
                $strTemp = $objSelect->{$strParseType}(true);
            } else {
                $strTemp = $mixResultCallback;
            }
            $this->setConditionItem(static::LOGIC_GROUP_LEFT.$strTemp.static::LOGIC_GROUP_RIGHT, 'string__');

            return $this;
        }
        $arrArgs = func_get_args();
        array_shift($arrArgs);
        array_shift($arrArgs);

        return $this->{'addConditions'}(...$arrArgs);
    }

    /**
     * 组装条件.
     *
     * @return $this
     */
    protected function addConditions()
    {
        $arrArgs = func_get_args();
        $strTable = $this->getCurrentTable();

        // 整理多个参数到二维数组
        if (!is_array($arrArgs[0])) {
            $conditions = [
                $arrArgs,
            ];
        } else {
            // 一维数组统一成二维数组格式
            $booOneImension = false;

            foreach ($arrArgs[0] as $mixKey => $mixValue) {
                if (is_int($mixKey) && !is_array($mixValue)) {
                    $booOneImension = true;
                }

                break;
            }

            if (true === $booOneImension) {
                $conditions = [
                    $arrArgs[0],
                ];
            } else {
                $conditions = $arrArgs[0];
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
                    $arrTemp = $this->objConnect->qualifyExpression($arrRes[1], $strTable, $this->arrColumnsMapping);
                }
                $this->setConditionItem($arrTemp, 'string__');
            }

            // 子表达式
            elseif (is_string($strKey) && in_array($strKey, [
                'subor__',
                'suband__',
            ], true)) {
                $arrTypeAndLogic = $this->getTypeAndLogic();

                $objSelect = new static($this->objConnect);
                $objSelect->setCurrentTable($this->getCurrentTable());
                $objSelect->setTypeAndLogic($arrTypeAndLogic[0]);

                // 逻辑表达式
                if (isset($arrTemp['logic__'])) {
                    if (strtolower($arrTemp['logic__']) === static::LOGIC_OR) {
                        $objSelect->setTypeAndLogic(null, static::LOGIC_OR);
                    }
                    unset($arrTemp['logic__']);
                }

                $objSelect = $objSelect->addConditions(
                    $arrTemp
                );

                // 解析结果
                $strParseType = 'parse'.ucwords($arrTypeAndLogic[0]);
                $strOldLogic = $arrTypeAndLogic[1];
                $this->setTypeAndLogic(null, 'subor__' ? static::LOGIC_OR : static::LOGIC_AND);
                $this->setConditionItem(static::LOGIC_GROUP_LEFT.$objSelect->{$strParseType}(true).static::LOGIC_GROUP_RIGHT, 'string__');
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
                    $objSelect = new static($this->objConnect);
                    $objSelect->setCurrentTable($this->getCurrentTable());
                    $mixResultCallback = call_user_func_array($arrTemp, [
                        &$objSelect,
                    ]);
                    if (null === $mixResultCallback) {
                        $strTemp = $arrTemp = $objSelect->makeSql();
                    } else {
                        $strTemp = $mixResultCallback;
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
            if (empty($this->arrOption[$arrTypeAndLogic[0]][$strType])) {
                $this->arrOption[$arrTypeAndLogic[0]][] = $arrTypeAndLogic[1];
                $this->arrOption[$arrTypeAndLogic[0]][$strType] = [];
            }
            $this->arrOption[$arrTypeAndLogic[0]][$strType][] = $arrItem;
        } else {
            // 格式化时间
            if (($strInTimeCondition = $this->getInTimeCondition())) {
                $arrItem[1] = '@'.$strInTimeCondition.' '.$arrItem[1];
            }
            $this->arrOption[$arrTypeAndLogic[0]][] = $arrTypeAndLogic[1];
            $this->arrOption[$arrTypeAndLogic[0]][] = $arrItem;
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
            $this->strConditionType = $strType;
        }
        if (null !== $strLogic) {
            $this->strConditionLogic = $strLogic;
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
            $this->strConditionType,
            $this->strConditionLogic,
        ];
    }

    /**
     * 格式化一个字段.
     *
     * @param string $strField
     * @param string $sTableName
     *
     * @return string
     */
    protected function qualifyOneColumn($strField, $sTableName = null)
    {
        $strField = trim($strField);
        if (empty($strField)) {
            return '';
        }

        if (null === $sTableName) {
            $sTableName = $this->getCurrentTable();
        }

        if (false !== strpos($strField, '{') && preg_match('/^{(.+?)}$/', $strField, $arrRes)) {
            $strField = $this->objConnect->qualifyExpression($arrRes[1], $sTableName, $this->arrColumnsMapping);
        } elseif (!preg_match('/\(.*\)/', $strField)) {
            if (preg_match('/(.+)\.(.+)/', $strField, $arrMatch)) {
                $sCurrentTableName = $arrMatch[1];
                $strTemp = $arrMatch[2];
            } else {
                $sCurrentTableName = $sTableName;
            }
            if (isset($this->arrColumnsMapping[$strField])) {
                $strField = $this->arrColumnsMapping[$strField];
            }
            $strField = $this->objConnect->qualifyTableOrColumn("{$sCurrentTableName}.{$strField}");
        }

        return $strField;
    }

    /**
     * 连表 join 操作.
     *
     * @param string     $sJoinType
     * @param mixed      $mixName
     * @param mixed      $mixCols
     * @param null|array $arrCondArgs
     * @param null|mixed $mixCond
     *
     * @return $this
     */
    protected function addJoin($sJoinType, $mixName, $mixCols, $mixCond = null)
    {
        // 验证 join 类型
        if (!isset(static::$arrJoinTypes[$sJoinType])) {
            throw new Exception(sprintf('Invalid JOIN type %s.', $sJoinType));
        }

        // 不能在使用 UNION 查询的同时使用 JOIN 查询
        if (count($this->arrOption['union'])) {
            throw new Exception('JOIN queries cannot be used while using UNION queries.');
        }

        // 是否分析 schema，子表达式不支持
        $booParseSchema = true;

        // 没有指定表，获取默认表
        if (empty($mixName)) {
            $sTable = $this->getCurrentTable();
            $sAlias = '';
        }

        // $mixName 为数组配置
        elseif (is_array($mixName)) {
            foreach ($mixName as $sAlias => $sTable) {
                if (!is_string($sAlias)) {
                    $sAlias = '';
                }

                // 对象子表达式
                if ($sTable instanceof self) {
                    $sTable = $sTable->makeSql(true);
                    if (!$sAlias) {
                        $sAlias = static::DEFAULT_SUBEXPRESSION_ALIAS;
                    }
                    $booParseSchema = false;
                }

                // 回调方法子表达式
                elseif (!is_string($sTable) && is_callable($sTable)) {
                    $objSelect = new static($this->objConnect);
                    $objSelect->setCurrentTable($this->getCurrentTable());
                    $mixResultCallback = call_user_func_array($sTable, [
                        &$objSelect,
                    ]);
                    if (null === $mixResultCallback) {
                        $sTable = $objSelect->makeSql(true);
                    } else {
                        $sTable = $mixResultCallback;
                    }
                    if (!$sAlias) {
                        $sAlias = static::DEFAULT_SUBEXPRESSION_ALIAS;
                    }
                    $booParseSchema = false;
                }

                break;
            }
        }

        // 对象子表达式
        elseif ($mixName instanceof self) {
            $sTable = $mixName->makeSql(true);
            $sAlias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            $booParseSchema = false;
        }

        // 回调方法
        elseif (!is_string($mixName) && is_callable($mixName)) {
            $objSelect = new static($this->objConnect);
            $objSelect->setCurrentTable($this->getCurrentTable());
            $mixResultCallback = call_user_func_array($mixName, [
                &$objSelect,
            ]);
            if (null === $mixResultCallback) {
                $sTable = $objSelect->makeSql(true);
            } else {
                $sTable = $mixResultCallback;
            }
            $sAlias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            $booParseSchema = false;
        }

        // 字符串子表达式
        elseif (0 === strpos(trim($mixName), '(')) {
            if (false !== ($intAsPosition = strripos($mixName, 'as'))) {
                $sTable = trim(substr($mixName, 0, $intAsPosition - 1));
                $sAlias = trim(substr($mixName, $intAsPosition + 2));
            } else {
                $sTable = $mixName;
                $sAlias = static::DEFAULT_SUBEXPRESSION_ALIAS;
            }
            $booParseSchema = false;
        } else {
            // 字符串指定别名
            if (preg_match('/^(.+)\s+AS\s+(.+)$/i', $mixName, $arrMatch)) {
                $sTable = $arrMatch[1];
                $sAlias = $arrMatch[2];
            } else {
                $sTable = $mixName;
                $sAlias = '';
            }
        }

        // 确定 table_name 和 schema
        if (true === $booParseSchema) {
            $arrTemp = explode('.', $sTable);
            if (isset($arrTemp[1])) {
                $sSchema = $arrTemp[0];
                $sTableName = $arrTemp[1];
            } else {
                $sSchema = null;
                $sTableName = $sTable;
            }
        } else {
            $sSchema = null;
            $sTableName = $sTable;
        }

        // 获得一个唯一的别名
        $sAlias = $this->uniqueAlias(empty($sAlias) ? $sTableName : $sAlias);

        // 只有表操作才设置当前表
        if ($this->getIsTable()) {
            $this->setCurrentTable(($sSchema ? $sSchema.'.' : '').$sAlias);
        }

        // 查询条件
        $arrArgs = func_get_args();
        if (count($arrArgs) > 3) {
            for ($nI = 0; $nI <= 2; $nI++) {
                array_shift($arrArgs);
            }
            $objSelect = new static($this->objConnect);
            $objSelect->setCurrentTable($sAlias);
            call_user_func_array([
                $objSelect,
                'where',
            ], $arrArgs);
            $mixCond = $objSelect->parseWhere(true);
        }

        // 添加一个要查询的数据表
        $this->arrOption['from'][$sAlias] = [
            'join_type'  => $sJoinType,
            'table_name' => $sTableName,
            'schema'     => $sSchema,
            'join_cond'  => $mixCond,
        ];

        // 添加查询字段
        $this->addCols($sAlias, $mixCols);

        return $this;
    }

    /**
     * 添加字段.
     *
     * @param string $sTableName
     * @param mixed  $mixCols
     */
    protected function addCols($sTableName, $mixCols)
    {
        // 处理条件表达式
        if (is_string($mixCols) && false !== strpos($mixCols, ',') && false !== strpos($mixCols, '{') && preg_match_all('/{(.+?)}/', $mixCols, $arrRes)) {
            $mixCols = str_replace($arrRes[1][0], base64_encode($arrRes[1][0]), $mixCols);
        }
        $mixCols = Arr::normalize($mixCols);
        // 还原
        if (!empty($arrRes)) {
            foreach ($arrRes[1] as $strTemp) {
                $mixCols[array_search('{'.base64_encode($strTemp).'}', $mixCols, true)] = '{'.$strTemp.'}';
            }
        }

        if (null === $sTableName) {
            $sTableName = '';
        }

        // 没有字段则退出
        if (empty($mixCols)) {
            return;
        }

        foreach ($mixCols as $sAlias => $mixCol) {
            if (is_string($mixCol)) {
                // 处理条件表达式
                if (is_string($mixCol) && false !== strpos($mixCol, ',') && false !== strpos($mixCol, '{') && preg_match_all('/{(.+?)}/', $mixCol, $arrResTwo)) {
                    $mixCol = str_replace($arrResTwo[1][0], base64_encode($arrResTwo[1][0]), $mixCol);
                }
                $mixCol = Arr::normalize($mixCol);

                // 还原
                if (!empty($arrResTwo)) {
                    foreach ($arrResTwo[1] as $strTemp) {
                        $mixCol[array_search('{'.base64_encode($strTemp).'}', $mixCol, true)] = '{'.$strTemp.'}';
                    }
                }

                // 将包含多个字段的字符串打散
                foreach (Arr::normalize($mixCol) as $sCol) {
                    $strThisTableName = $sTableName;

                    // 检查是不是 "字段名 AS 别名"这样的形式
                    if (preg_match('/^(.+)\s+'.'AS'.'\s+(.+)$/i', $sCol, $arrMatch)) {
                        $sCol = $arrMatch[1];
                        $sAlias = $arrMatch[2];
                    }

                    // 检查字段名是否包含表名称
                    if (preg_match('/(.+)\.(.+)/', $sCol, $arrMatch)) {
                        $strThisTableName = $arrMatch[1];
                        $sCol = $arrMatch[2];
                    }

                    if (isset($this->arrColumnsMapping[$sCol])) {
                        $sCol = $this->arrColumnsMapping[$sCol];
                    }

                    $this->arrOption['columns'][] = [
                        $strThisTableName,
                        $sCol,
                        is_string($sAlias) ? $sAlias : null,
                    ];
                }
            } else {
                $this->arrOption['columns'][] = [
                    $sTableName,
                    $mixCol,
                    is_string($sAlias) ? $sAlias : null,
                ];
            }
        }
    }

    /**
     * 添加一个集合查询.
     *
     * @param string $sType    类型
     * @param string $strField 字段
     * @param string $sAlias   别名
     *
     * @return $this
     */
    protected function addAggregate($sType, $strField, $sAlias)
    {
        $this->arrOption['columns'] = [];
        $strTableName = $this->getCurrentTable();

        // 表达式支持
        if (false !== strpos($strField, '{') && preg_match('/^{(.+?)}$/', $strField, $arrRes)) {
            $strField = $this->objConnect->qualifyExpression($arrRes[1], $strTableName, $this->arrColumnsMapping);
        } else {
            if (preg_match('/(.+)\.(.+)/', $strField, $arrMatch)) { // 检查字段名是否包含表名称
                $strTableName = $arrMatch[1];
                $strField = $arrMatch[2];
            }
            if (isset($this->arrColumnsMapping[$strField])) {
                $strField = $this->arrColumnsMapping[$strField];
            }
            if ('*' === $strField) {
                $strTableName = '';
            }
            $strField = $this->objConnect->qualifyColumn($strField, $strTableName);
        }
        $strField = "{$sType}(${strField})";

        $this->arrOption['aggregate'][] = [
            $sType,
            $strField,
            $sAlias,
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

        $arrArgs = [
            $strSql,
            $this->getBindParams(),
            $this->arrQueryParams['master'],
            $this->arrQueryParams['fetch_type']['fetch_type'],
            $this->arrQueryParams['fetch_type']['fetch_argument'],
            $this->arrQueryParams['fetch_type']['ctor_args'],
        ];

        // 只返回 SQL，不做任何实际操作
        if (true === $this->booOnlyMakeSql) {
            return $arrArgs;
        }

        $arrData = $this->objConnect->{'query'}(...$arrArgs);

        if ($this->arrQueryParams['as_default']) {
            $this->queryDefault($arrData);
        } else {
            $this->queryClass($arrData);
        }

        return $arrData;
    }

    /**
     * 以数组返回结果.
     *
     * @param array $arrData
     */
    protected function queryDefault(&$arrData)
    {
        if (empty($arrData)) {
            if (!$this->arrOption['limitquery']) {
                $arrData = null;
            }

            return;
        }

        // 返回一条记录
        if (!$this->arrOption['limitquery']) {
            $arrData = reset($arrData) ?: null;
        }
    }

    /**
     * 以 class 返回结果.
     *
     * @param array $arrData
     */
    protected function queryClass(&$arrData)
    {
        if (empty($arrData)) {
            if (!$this->arrOption['limitquery']) {
                $arrData = null;
            } else {
                if ($this->arrQueryParams['as_collection']) {
                    $arrData = new Collection();
                }
            }

            return;
        }

        // 模型类不存在，直接以数组结果返回
        $sClassName = $this->arrQueryParams['as_class'];
        if ($sClassName && !class_exists($sClassName)) {
            $this->queryDefault($arrData);

            return;
        }

        foreach ($arrData as &$mixTemp) {
            $mixTemp = new $sClassName((array) $mixTemp);
        }

        // 创建一个单独的对象
        if (!$this->arrOption['limitquery']) {
            $arrData = reset($arrData) ?: null;
        } else {
            if ($this->arrQueryParams['as_collection']) {
                $arrData = new Collection($arrData, [$sClassName]);
            }
        }
    }

    /**
     * 原生 sql 执行方法.
     *
     * @param null|string $mixData
     *
     * @return mixed
     */
    protected function runNativeSql($mixData = null)
    {
        $strNativeSql = $this->getNativeSql();

        // 空参数返回当前对象
        if (null === $mixData) {
            return $this;
        }
        if (is_string($mixData)) {
            // 验证参数
            $strSqlType = $this->objConnect->getSqlType($mixData);
            if ('procedure' === $strSqlType) {
                $strSqlType = 'select';
            }
            if ($strSqlType !== $strNativeSql) {
                throw new Exception('Unsupported parameters.');
            }

            $arrArgs = func_get_args();

            // 只返回 SQL，不做任何实际操作
            if (true === $this->booOnlyMakeSql) {
                return $arrArgs;
            }

            return $this->objConnect->{'select' === $strNativeSql ? 'query' : 'execute'}(...$arrArgs);
        }

        throw new Exception('Unsupported parameters.');
    }

    /**
     * 设置原生 sql 类型.
     *
     * @param string $strNativeSql
     */
    protected function setNativeSql($strNativeSql)
    {
        $this->strNativeSql = $strNativeSql;
    }

    /**
     * 返回原生 sql 类型.
     *
     * @return string
     */
    protected function getNativeSql()
    {
        return $this->strNativeSql;
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
            return $this->arrBindParams;
        }

        return $this->arrBindParams[$mixName] ?? null;
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
        return isset($this->arrBindParams[$mixName]);
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
        if (isset($this->arrBindParams[$mixName])) {
            unset($this->arrBindParams[$mixName]);
        }
    }

    /**
     * 分析绑定参数数据.
     *
     * @param array $arrData
     * @param array $arrBind
     * @param int   $intQuestionMark
     * @param int   $intIndex
     */
    protected function getBindData($arrData, &$arrBind = [], &$intQuestionMark = 0, $intIndex = 0)
    {
        $arrField = $arrValue = [];
        $strTableName = $this->getCurrentTable();

        foreach ($arrData as $sKey => $mixValue) {
            // 表达式支持
            $arrRes = null;
            if (false !== strpos($mixValue, '{') && preg_match('/^{(.+?)}$/', $mixValue, $arrRes)) {
                $mixValue = $this->objConnect->qualifyExpression($arrRes[1], $strTableName, $this->arrColumnsMapping);
            } else {
                $mixValue = $this->objConnect->qualifyColumnValue($mixValue, false);
            }

            // 字段
            if (0 === $intIndex) {
                $arrField[] = $sKey;
            }

            if (0 === strpos($mixValue, ':') || !empty($arrRes)) {
                $arrValue[] = $mixValue;
            } else {
                // 转换 ? 占位符至 : 占位符
                if ('?' === $mixValue && isset($arrBind[$intQuestionMark])) {
                    $sKey = 'questionmark_'.$intQuestionMark;
                    $mixValue = $arrBind[$intQuestionMark];
                    unset($arrBind[$intQuestionMark]);
                    $this->deleteBindParams($intQuestionMark);
                    $intQuestionMark++;
                }

                if ($intIndex > 0) {
                    $sKey = $sKey.'_'.$intIndex;
                }
                $arrValue[] = ':'.$sKey;

                $this->bind($sKey, $mixValue, $this->objConnect->getBindParamType($mixValue));
            }
        }

        return [
            $arrField,
            $arrValue,
        ];
    }

    /**
     * 设置当前表名字.
     *
     * @param mixed $mixTable
     */
    protected function setCurrentTable($mixTable)
    {
        $this->strCurrentTable = $mixTable;
    }

    /**
     * 获取当前表名字.
     *
     * @return string
     */
    protected function getCurrentTable()
    {
        if (is_array($this->strCurrentTable)) { // 数组
            while ((list($sAlias) = each($this->strCurrentTable)) !== false) {
                return $this->strCurrentTable = $sAlias;
            }
        } else {
            return $this->strCurrentTable;
        }
    }

    /**
     * 设置是否为表操作.
     *
     * @param bool $booIsTable
     */
    protected function setIsTable($booIsTable = true)
    {
        $this->booIsTable = $booIsTable;
    }

    /**
     * 返回是否为表操作.
     *
     * @return bool
     */
    protected function getIsTable()
    {
        return $this->booIsTable;
    }

    /**
     * 解析时间信息.
     *
     * @param string $sField
     * @param mixed  $mixValue
     * @param string $strType
     *
     * @return mixed
     */
    protected function parseTime($sField, $mixValue, $strType)
    {
        static $arrDate = null, $arrColumns = [];

        // 获取时间和字段信息
        if (null === $arrDate) {
            $arrDate = getdate();
        }
        $sField = str_replace('`', '', $sField);
        $strTable = $this->getCurrentTable();
        if (!preg_match('/\(.*\)/', $sField)) {
            if (preg_match('/(.+)\.(.+)/', $sField, $arrMatch)) {
                $strTable = $arrMatch[1];
                $sField = $arrMatch[2];
            }
            if (isset($this->arrColumnsMapping[$sField])) {
                $sField = $this->arrColumnsMapping[$sField];
            }
        }
        if ('*' === $sField) {
            return '';
        }
        if (!isset($arrColumns[$strTable])) {
            $arrColumns[$strTable] = $this->objConnect->getTableColumnsCache($strTable)['list'];
        }

        // 支持类型
        switch ($strType) {
            case 'day':
                $mixValue = mktime(0, 0, 0, $arrDate['mon'], (int) $mixValue, $arrDate['year']);

                break;
            case 'month':
                $mixValue = mktime(0, 0, 0, (int) $mixValue, 1, $arrDate['year']);

                break;
            case 'year':
                $mixValue = mktime(0, 0, 0, 1, 1, (int) $mixValue);

                break;
            case 'date':
                $mixValue = strtotime($mixValue);
                if (false === $mixValue) {
                    throw new Exception('Please enter a right time of strtotime.');
                }

                break;
            default:
                throw new Exception(sprintf('Unsupported time formatting type %s.', $strType));
                break;
        }

        // 自动格式化时间
        if (!empty($arrColumns[$strTable][$sField])) {
            $strFieldType = $arrColumns[$strTable][$sField]['type'];
            if (in_array($strFieldType, [
                'datetime',
                'timestamp',
            ], true)) {
                $mixValue = date('Y-m-d H:i:s', $mixValue);
            } elseif ('date' === $strFieldType) {
                $mixValue = date('Y-m-d', $mixValue);
            } elseif ('time' === $strFieldType) {
                $mixValue = date('H:i:s', $mixValue);
            } elseif (0 === strpos($strFieldType, 'year')) {
                $mixValue = date('Y', $mixValue);
            }
        }

        return $mixValue;
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
        for ($nI = 2; array_key_exists($strAliasReturn, $this->arrOption['from']); $nI++) {
            $strAliasReturn = $mixName.'_'.(string) $nI;
        }

        return $strAliasReturn;
    }

    /**
     * 设置当前是否处于时间条件状态
     *
     * @param string $strInTimeCondition
     */
    protected function setInTimeCondition($strInTimeCondition = null)
    {
        $this->strInTimeCondition = $strInTimeCondition;
    }

    /**
     * 返回当前是否处于时间条件状态
     *
     * @return null|string
     */
    protected function getInTimeCondition()
    {
        return $this->strInTimeCondition;
    }

    /**
     * 初始化查询条件.
     */
    protected function initOption()
    {
        $this->arrOption = static::$arrOptionDefault;
        $this->arrQueryParams = static::$arrQueryParamsDefault;
    }

    /**
     * 备份分页查询条件.
     */
    protected function backupPaginateArgs()
    {
        $this->arrBackupPage = [];
        $this->arrBackupPage['aggregate'] = $this->arrOption['aggregate'];
        $this->arrBackupPage['query_params'] = $this->arrQueryParams;
        $this->arrBackupPage['columns'] = $this->arrOption['columns'];
    }

    /**
     * 恢复分页查询条件.
     */
    protected function restorePaginateArgs()
    {
        $this->arrOption['aggregate'] = $this->arrBackupPage['aggregate'];
        $this->arrQueryParams = $this->arrBackupPage['query_params'];
        $this->arrOption['columns'] = $this->arrBackupPage['columns'];
    }
}
