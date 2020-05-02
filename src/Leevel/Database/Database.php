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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database;

use Closure;
use Exception;
use InvalidArgumentException;
use Leevel\Event\IDispatch;
use Leevel\Protocol\Pool\Connection;
use Leevel\Protocol\Pool\IConnection;
use PDO;
use PDOException;
use PDOStatement;
use Throwable;

/**
 * 数据库抽象层
 *
 * @method static \Leevel\Database\Condition databaseCondition()                                                                                查询对象.
 * @method static \Leevel\Database\IDatabase databaseConnect()                                                                                  返回数据库连接对象.
 * @method static \Leevel\Database\Select databaseSelect()                                                                                      返回查询对象.
 * @method static \Leevel\Database\Select sql(bool $flag = true)                                                                                指定返回 SQL 不做任何操作.
 * @method static \Leevel\Database\Select master(bool $master = false)                                                                          设置是否查询主服务器.
 * @method static \Leevel\Database\Select fetchArgs(int $fetchStyle, $fetchArgument = null, array $ctorArgs = [])                               设置查询参数.
 * @method static \Leevel\Database\Select asSome(?\Closure $asSome = null, array $args = [])                                                    设置以某种包装返会结果.
 * @method static \Leevel\Database\Select asCollection(bool $asCollection = true)                                                               设置是否以集合返回.
 * @method static mixed select($data = null, array $bind = [], bool $flag = false)                                                              原生 sql 查询数据 select.
 * @method static mixed insert($data, array $bind = [], bool $replace = false, bool $flag = false)                                              插入数据 insert (支持原生 SQL).
 * @method static mixed insertAll(array $data, array $bind = [], bool $replace = false, bool $flag = false)                                     批量插入数据 insertAll.
 * @method static mixed update($data, array $bind = [], bool $flag = false)                                                                     更新数据 update (支持原生 SQL).
 * @method static mixed updateColumn(string $column, $value, array $bind = [], bool $flag = false)                                              更新某个字段的值
 * @method static mixed updateIncrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                     字段递增.
 * @method static mixed updateDecrease(string $column, int $step = 1, array $bind = [], bool $flag = false)                                     字段减少.
 * @method static mixed delete(?string $data = null, array $bind = [], bool $flag = false)                                                      删除数据 delete (支持原生 SQL).
 * @method static mixed truncate(bool $flag = false)                                                                                            清空表重置自增 ID.
 * @method static mixed findOne(bool $flag = false)                                                                                             返回一条记录.
 * @method static mixed findAll(bool $flag = false)                                                                                             返回所有记录.
 * @method static mixed find(?int $num = null, bool $flag = false)                                                                              返回最后几条记录.
 * @method static mixed value(string $field, bool $flag = false)                                                                                返回一个字段的值
 * @method static array list($fieldValue, ?string $fieldKey = null, bool $flag = false)                                                         返回一列数据.
 * @method static void chunk(int $count, \Closure $chunk)                                                                                       数据分块处理.
 * @method static void each(int $count, \Closure $each)                                                                                         数据分块处理依次回调.
 * @method static mixed findCount(string $field = '*', string $alias = 'row_count', bool $flag = false)                                         总记录数.
 * @method static mixed findAvg(string $field, string $alias = 'avg_value', bool $flag = false)                                                 平均数.
 * @method static mixed findMax(string $field, string $alias = 'max_value', bool $flag = false)                                                 最大值.
 * @method static mixed findMin(string $field, string $alias = 'min_value', bool $flag = false)                                                 最小值.
 * @method static mixed findSum(string $field, string $alias = 'sum_value', bool $flag = false)                                                 合计.
 * @method static \Leevel\Database\Page page(int $currentPage, int $perPage = 10, bool $flag = false, string $column = '*', array $option = []) 分页查询.
 * @method static \Leevel\Database\Page pageMacro(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])                  创建一个无限数据的分页查询.
 * @method static \Leevel\Database\Page pagePrevNext(int $currentPage, int $perPage = 10, bool $flag = false, array $option = [])               创建一个只有上下页的分页查询.
 * @method static int pageCount(string $cols = '*')                                                                                             取得分页查询记录数量.
 * @method static string makeSql(bool $withLogicGroup = false)                                                                                  获得查询字符串.
 * @method static \Leevel\Database\Select forPage(int $page, int $perPage = 10)                                                                 根据分页设置条件.
 * @method static \Leevel\Database\Select time(string $type = 'date')                                                                           时间控制语句开始.
 * @method static \Leevel\Database\Select endTime()                                                                                             时间控制语句结束.
 * @method static \Leevel\Database\Select reset(?string $option = null)                                                                         重置查询条件.
 * @method static \Leevel\Database\Select prefix(string $prefix)                                                                                prefix 查询.
 * @method static \Leevel\Database\Select table($table, $cols = '*')                                                                            添加一个要查询的表及其要查询的字段.
 * @method static string getAlias()                                                                                                             获取表别名.
 * @method static \Leevel\Database\Select columns($cols = '*', ?string $table = null)                                                           添加字段.
 * @method static \Leevel\Database\Select setColumns($cols = '*', ?string $table = null)                                                        设置字段.
 * @method static string raw(string $raw)                                                                                                       原生查询.
 * @method static \Leevel\Database\Select where(...$cond)                                                                                       where 查询条件.
 * @method static \Leevel\Database\Select orWhere(...$cond)                                                                                     orWhere 查询条件.
 * @method static \Leevel\Database\Select whereRaw(string $raw)                                                                                 Where 原生查询.
 * @method static \Leevel\Database\Select orWhereRaw(string $raw)                                                                               Where 原生 OR 查询.
 * @method static \Leevel\Database\Select whereExists($exists)                                                                                  exists 方法支持
 * @method static \Leevel\Database\Select whereNotExists($exists)                                                                               not exists 方法支持
 * @method static \Leevel\Database\Select whereBetween(...$cond)                                                                                whereBetween 查询条件.
 * @method static \Leevel\Database\Select whereNotBetween(...$cond)                                                                             whereNotBetween 查询条件.
 * @method static \Leevel\Database\Select whereNull(...$cond)                                                                                   whereNull 查询条件.
 * @method static \Leevel\Database\Select whereNotNull(...$cond)                                                                                whereNotNull 查询条件.
 * @method static \Leevel\Database\Select whereIn(...$cond)                                                                                     whereIn 查询条件.
 * @method static \Leevel\Database\Select whereNotIn(...$cond)                                                                                  whereNotIn 查询条件.
 * @method static \Leevel\Database\Select whereLike(...$cond)                                                                                   whereLike 查询条件.
 * @method static \Leevel\Database\Select whereNotLike(...$cond)                                                                                whereNotLike 查询条件.
 * @method static \Leevel\Database\Select whereDate(...$cond)                                                                                   whereDate 查询条件.
 * @method static \Leevel\Database\Select whereDay(...$cond)                                                                                    whereDay 查询条件.
 * @method static \Leevel\Database\Select whereMonth(...$cond)                                                                                  whereMonth 查询条件.
 * @method static \Leevel\Database\Select whereYear(...$cond)                                                                                   whereYear 查询条件.
 * @method static \Leevel\Database\Select bind($names, $value = null, ?int $dataType = null)                                                    参数绑定支持
 * @method static \Leevel\Database\Select forceIndex($indexs, $type = 'FORCE')                                                                  index 强制索引（或者忽略索引）.
 * @method static \Leevel\Database\Select ignoreIndex($indexs)                                                                                  index 忽略索引.
 * @method static \Leevel\Database\Select join($table, $cols, ...$cond)                                                                         join 查询.
 * @method static \Leevel\Database\Select innerJoin($table, $cols, ...$cond)                                                                    innerJoin 查询.
 * @method static \Leevel\Database\Select leftJoin($table, $cols, ...$cond)                                                                     leftJoin 查询.
 * @method static \Leevel\Database\Select rightJoin($table, $cols, ...$cond)                                                                    rightJoin 查询.
 * @method static \Leevel\Database\Select fullJoin($table, $cols, ...$cond)                                                                     fullJoin 查询.
 * @method static \Leevel\Database\Select crossJoin($table, $cols, ...$cond)                                                                    crossJoin 查询.
 * @method static \Leevel\Database\Select naturalJoin($table, $cols, ...$cond)                                                                  naturalJoin 查询.
 * @method static \Leevel\Database\Select union($selects, string $type = 'UNION')                                                               添加一个 UNION 查询.
 * @method static \Leevel\Database\Select unionAll($selects)                                                                                    添加一个 UNION ALL 查询.
 * @method static \Leevel\Database\Select groupBy($expression)                                                                                  指定 GROUP BY 子句.
 * @method static \Leevel\Database\Select having(...$cond)                                                                                      添加一个 HAVING 条件.
 * @method static \Leevel\Database\Select orHaving(...$cond)                                                                                    orHaving 查询条件.
 * @method static \Leevel\Database\Select havingRaw(string $raw)                                                                                having 原生查询.
 * @method static \Leevel\Database\Select orHavingRaw(string $raw)                                                                              having 原生 OR 查询.
 * @method static \Leevel\Database\Select havingBetween(...$cond)                                                                               havingBetween 查询条件.
 * @method static \Leevel\Database\Select havingNotBetween(...$cond)                                                                            havingNotBetween 查询条件.
 * @method static \Leevel\Database\Select havingNull(...$cond)                                                                                  havingNull 查询条件.
 * @method static \Leevel\Database\Select havingNotNull(...$cond)                                                                               havingNotNull 查询条件.
 * @method static \Leevel\Database\Select havingIn(...$cond)                                                                                    havingIn 查询条件.
 * @method static \Leevel\Database\Select havingNotIn(...$cond)                                                                                 havingNotIn 查询条件.
 * @method static \Leevel\Database\Select havingLike(...$cond)                                                                                  havingLike 查询条件.
 * @method static \Leevel\Database\Select havingNotLike(...$cond)                                                                               havingNotLike 查询条件.
 * @method static \Leevel\Database\Select havingDate(...$cond)                                                                                  havingDate 查询条件.
 * @method static \Leevel\Database\Select havingDay(...$cond)                                                                                   havingDay 查询条件.
 * @method static \Leevel\Database\Select havingMonth(...$cond)                                                                                 havingMonth 查询条件.
 * @method static \Leevel\Database\Select havingYear(...$cond)                                                                                  havingYear 查询条件.
 * @method static \Leevel\Database\Select orderBy($expression, string $orderDefault = 'ASC')                                                    添加排序.
 * @method static \Leevel\Database\Select latest(string $field = 'create_at')                                                                   最近排序数据.
 * @method static \Leevel\Database\Select oldest(string $field = 'create_at')                                                                   最早排序数据.
 * @method static \Leevel\Database\Select distinct(bool $flag = true)                                                                           创建一个 SELECT DISTINCT 查询.
 * @method static \Leevel\Database\Select count(string $field = '*', string $alias = 'row_count')                                               总记录数.
 * @method static \Leevel\Database\Select avg(string $field, string $alias = 'avg_value')                                                       平均数.
 * @method static \Leevel\Database\Select max(string $field, string $alias = 'max_value')                                                       最大值.
 * @method static \Leevel\Database\Select min(string $field, string $alias = 'min_value')                                                       最小值.
 * @method static \Leevel\Database\Select sum(string $field, string $alias = 'sum_value')                                                       合计
 * @method static \Leevel\Database\Select one()                                                                                                 指示仅查询第一个符合条件的记录.
 * @method static \Leevel\Database\Select all()                                                                                                 指示查询所有符合条件的记录.
 * @method static \Leevel\Database\Select top(int $count = 30)                                                                                  查询几条记录.
 * @method static \Leevel\Database\Select limit(int $offset = 0, int $count = 0)                                                                limit 限制条数.
 * @method static \Leevel\Database\Select forUpdate(bool $flag = true)                                                                          是否构造一个 FOR UPDATE 查询.
 * @method static \Leevel\Database\Select setOption(string $name, $value)                                                                       设置查询参数.
 * @method static array getOption()                                                                                                             返回查询参数.
 * @method static array getBindParams()                                                                                                         返回参数绑定.                                                                                                        返回参数绑定.
 * @method static void resetBindParams()                                                                                                        重置参数绑定.
 * @method static void setBindParamsPrefix(string $bindParamsPrefix)                                                                            设置参数绑定前缀.
 */
abstract class Database implements IDatabase, IConnection
{
    use Connection {
        release as baseRelease;
    }

    /**
     * 所有数据库连接.
     *
     * @var array
     */
    protected array $connects = [];

    /**
     * 当前数据库连接.
     *
     * @var \PDO
     */
    protected ?PDO $connect = null;

    /**
     * PDO 预处理语句对象
     *
     * @var \PDOStatement
     */
    protected ?PDOStatement $pdoStatement = null;

    /**
     * 数据查询组件.
     *
     * @var \Leevel\Database\Select
     */
    protected ?Select $select = null;

    /**
     * 数据库连接参数.
     *
     * @var array
     */
    protected array $option = [];

    /**
     * sql 最后查询语句.
     *
     * @var string
     */
    protected ?string $sql = null;

    /**
     * sql 影响记录数量.
     *
     * @var int
     */
    protected int $numRows = 0;

    /**
     * 事务等级.
     *
     * @var int
     */
    protected int $transactionLevel = 0;

    /**
     * 是否开启部分事务.
     * 依赖数据库是否支持部分事务.
     *
     * @var bool
     */
    protected bool $transactionWithSavepoints = false;

    /**
     * 是否仅仅是事务回滚.
     *
     * @var bool
     */
    protected bool $isRollbackOnly = false;

    /**
     * 断线重连次数.
     *
     * @var int
     */
    protected int $reconnectRetry = 0;

    /**
     * 事件处理器.
     *
     * @var \Leevel\Event\IDispatch
     */
    protected ?IDispatch $dispatch = null;

    /**
     * 连接管理.
     *
     * @var \Leevel\Database\Manager
     */
    protected ?Manager $manager = null;

    /**
     * 构造函数.
     *
     * @param null|\Leevel\Database\Manager $manager
     */
    public function __construct(array $option, ?IDispatch $dispatch = null, ?Manager $manager = null)
    {
        $this->option = $option;
        $this->dispatch = $dispatch;
        $this->manager = $manager;
    }

    /**
     * 析构方法.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * call.
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        $this->initSelect();

        return $this->select->{$method}(...$args);
    }

    /**
     * 返回查询对象.
     *
     * @return \Leevel\Database\Select
     */
    public function databaseSelect(): Select
    {
        if (!$this->select) {
            $this->initSelect();
        }

        return $this->select;
    }

    /**
     * 返回 Pdo 查询连接.
     *
     * @param bool|int $master
     *                         - bool,false (读服务器),true (写服务器)
     *                         - int,其它去对应服务器连接 ID,0 表示主服务器
     *
     * @return mixed
     */
    public function pdo($master = false)
    {
        if (is_bool($master)) {
            if (false === $master) {
                return $this->readConnect();
            }

            return $this->writeConnect();
        }

        return $this->connects[$master] ?? null;
    }

    /**
     * 查询数据记录.
     *
     * @param string     $sql           sql 语句
     * @param array      $bindParams    sql 参数绑定
     * @param bool|int   $master
     * @param null|mixed $fetchArgument
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function query(string $sql, array $bindParams = [], $master = false, ?int $fetchStyle = null, $fetchArgument = null, array $ctorArgs = [])
    {
        $this->initSelect();

        if (!in_array($sqlType = $this->normalizeSqlType($sql), ['select', 'procedure'], true)) {
            $e = 'The query method only allows select and procedure SQL statements.';

            throw new InvalidArgumentException($e);
        }

        if (true === $this->runSql($sql, $bindParams, $master)) {
            return self::query($sql, $bindParams, $master, $fetchStyle, $fetchArgument, $ctorArgs);
        }

        $result = $this->fetchResult($fetchStyle, $fetchArgument, $ctorArgs, 'procedure' === $sqlType);
        $this->release();

        return $result;
    }

    /**
     * 执行 sql 语句.
     *
     * @param string $sql        sql 语句
     * @param array  $bindParams sql 参数绑定
     *
     * @throws \InvalidArgumentException
     *
     * @return int|string
     */
    public function execute(string $sql, array $bindParams = [])
    {
        $this->initSelect();

        if (in_array($sqlType = $this->normalizeSqlType($sql), ['select', 'procedure'], true)) {
            $e = 'The query method not allows select and procedure SQL statements.';

            throw new InvalidArgumentException($e);
        }

        if (true === $this->runSql($sql, $bindParams, true)) {
            return self::execute($sql, $bindParams);
        }

        $this->release();
        if (in_array($sqlType, ['insert', 'replace'], true)) {
            return (int) $this->lastInsertId();
        }

        return $this->numRows;
    }

    /**
     * 执行数据库事务
     *
     * @return mixed
     */
    public function transaction(Closure $action)
    {
        $this->beginTransaction();

        try {
            $result = $action($this);
            $this->commit();

            return $result;
        } catch (Throwable $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * 启动事务.
     */
    public function beginTransaction(): void
    {
        $this->transactionLevel++;

        if (1 === $this->transactionLevel) {
            try { // @codeCoverageIgnore
                $this->pdo(true)->beginTransaction();
                if ($this->manager) {
                    $this->manager->setTransactionConnection($this);
                }
                // @codeCoverageIgnoreStart
            } catch (Exception $e) {
                $this->transactionLevel--;

                throw $e;
            }
            // @codeCoverageIgnoreEnd
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->createSavepoint($this->getSavepointName()); // @codeCoverageIgnore
        }
    }

    /**
     * 检查是否处于事务中.
     */
    public function inTransaction(): bool
    {
        return $this->pdo(true)->inTransaction();
    }

    /**
     * 用于非自动提交状态下面的查询提交.
     *
     * @throws \InvalidArgumentException
     */
    public function commit(): void
    {
        if (0 === $this->transactionLevel) {
            $e = 'There was no active transaction.';

            throw new InvalidArgumentException($e);
        }

        if ($this->isRollbackOnly) {
            $e = 'Commit failed for rollback only.';

            throw new InvalidArgumentException($e);
        }

        if (1 === $this->transactionLevel) {
            $this->pdo(true)->commit();
            if ($this->manager) {
                $this->manager->removeTransactionConnection();
                $this->release();
            }
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->releaseSavepoint($this->getSavepointName()); // @codeCoverageIgnore
        }

        $this->transactionLevel = max(0, $this->transactionLevel - 1);
    }

    /**
     * 事务回滚.
     *
     * @throws \InvalidArgumentException
     */
    public function rollBack(): void
    {
        if (0 === $this->transactionLevel) {
            $e = 'There was no active transaction.';

            throw new InvalidArgumentException($e);
        }

        if (1 === $this->transactionLevel) {
            $this->transactionLevel = 0;
            $this->pdo(true)->rollBack();
            $this->isRollbackOnly = false;
            if ($this->manager) {
                $this->manager->removeTransactionConnection();
                $this->release();
            }
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            // @codeCoverageIgnoreStart
            $this->rollbackSavepoint($this->getSavepointName());
            $this->transactionLevel--;
        // @codeCoverageIgnoreEnd
        } else {
            $this->isRollbackOnly = true;
            $this->transactionLevel = max(0, $this->transactionLevel - 1);
        }
    }

    /**
     * 设置是否启用部分事务.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    public function setSavepoints(bool $savepoints): void
    {
        $this->transactionWithSavepoints = $savepoints;
    }

    /**
     * 获取是否启用部分事务.
     */
    public function hasSavepoints(): bool
    {
        return $this->transactionWithSavepoints;
    }

    /**
     * 获取最后插入 ID 或者列.
     *
     * @param null|string $name 自增序列名
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->connect->lastInsertId($name);
    }

    /**
     * 获取最近一次查询的 sql 语句.
     */
    public function getLastSql(): ?string
    {
        return $this->sql;
    }

    /**
     * 返回影响记录.
     */
    public function numRows(): int
    {
        return $this->numRows;
    }

    /**
     * 关闭数据库.
     */
    public function close(): void
    {
        $this->freePDOStatement();
        $this->closeConnects();
    }

    /**
     * 释放 PDO 预处理查询.
     */
    public function freePDOStatement(): void
    {
        // Fix errors
        // Error while sending STMT_CLOSE packet. PID=32336
        // PHP Fatal error:  Uncaught Error while sending STMT_CLOSE packet. PID=32336
        try {
            $this->pdoStatement = null;
        } catch (Throwable $e) { // @codeCoverageIgnore
        }
    }

    /**
     * 关闭数据库连接.
     */
    public function closeConnects(): void
    {
        $this->connects = [];
        $this->connect = null;
    }

    /**
     * 归还连接池.
     */
    public function release(): void
    {
        if (!$this->manager) {
            return;
        }

        if (!$this->manager->inTransactionConnection()) {
            $this->baseRelease();
        }
    }

    /**
     * sql 表达式格式化.
     */
    public function normalizeExpression(string $sql, string $tableName): string
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
    public function normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null): string
    {
        $name = str_replace('`', '', $name);
        if (false === strpos($name, '.')) {
            $name = $this->identifierColumn($name);
        } else {
            $tmp = explode('.', $name);
            foreach ($tmp as $offset => $name) {
                if (empty($name)) {
                    unset($tmp[$offset]);
                } else {
                    $tmp[$offset] = $this->identifierColumn($name);
                }
            }
            $name = implode('.', $tmp);
        }

        if ($alias) {
            return "{$name} ".($as ? $as.' ' : '').$this->identifierColumn($alias);
        }

        return $name;
    }

    /**
     * 字段格式化.
     */
    public function normalizeColumn(string $key, string $tableName): string
    {
        return $this->normalizeTableOrColumn("{$tableName}.{$key}");
    }

    /**
     * 字段值格式化.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function normalizeColumnValue($value, bool $quotationMark = true)
    {
        if (!is_string($value)) {
            return $value;
        }

        // 问号占位符
        if ('[?]' === $value) {
            return '?';
        }

        // [:id] 占位符
        if (preg_match('/^\[:[a-z][a-z0-9_\-\.]*\]$/i', $value, $matches)) {
            return trim($matches[0], '[]');
        }

        if (true === $quotationMark) {
            return "'".addslashes($value)."'";
        }

        return $value;
    }

    /**
     * 分析 sql 类型数据.
     */
    public function normalizeSqlType(string $sql): string
    {
        $sql = trim($sql);

        foreach ([
            'select', 'show', 'call', 'exec',
            'delete', 'insert', 'replace', 'update',
        ] as $value) {
            if (0 === stripos($sql, $value)) {
                if ('show' === $value) {
                    $value = 'select';
                } elseif (in_array($value, ['call', 'exec'], true)) {
                    $value = 'procedure';
                }

                return $value;
            }
        }

        return 'statement';
    }

    /**
     * 分析绑定参数类型数据.
     *
     * @see http://php.net/manual/en/pdo.constants.php
     *
     * @param mixed $value
     */
    public function normalizeBindParamType($value): int
    {
        switch (true) {
            case is_int($value):
                return PDO::PARAM_INT;
            case is_bool($value):
                return PDO::PARAM_BOOL;
            case null === $value:
                return PDO::PARAM_NULL;
            case is_string($value):
                return PDO::PARAM_STR;
            default:
                return PDO::PARAM_STMT;
        }
    }

    /**
     * 从 PDO 预处理语句中获取原始 SQL 查询字符串.
     *
     * - This method borrows heavily from the pdo-debug package and is part of the pdo-debug package.
     *
     * @see https://github.com/panique/pdo-debug/blob/master/pdo-debug.php
     * @see https://stackoverflow.com/questions/210564/getting-raw-sql-query-string-from-pdo-prepared-statements
     */
    public static function getRawSql(string $sql, array $bindParams): string
    {
        $keys = $values = [];

        /*
         * Get longest keys first, sot the regex replacement doesn't
         * cut markers (ex : replace ":username" with "'joe'name"
         * if we have a param name :user )
         */
        $isNamedMarkers = false;
        if (count($bindParams) && is_string(key($bindParams))) {
            uksort($bindParams, function (string $k1, string $k2): bool {
                return strlen($k2) - strlen($k1) > 0;
            });
            $isNamedMarkers = true;
        }

        foreach ($bindParams as $key => $value) {
            list($value, $dataType) = $value;
            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (is_string($key)) {
                $keys[] = '/:'.ltrim($key, ':').'/';
            } else {
                $keys[] = '/[?]/';
            }

            switch ($dataType) {
                case PDO::PARAM_INT:
                    $values[] = (string) $value;

                    break;
                case PDO::PARAM_BOOL:
                    $values[] = (string) $value;

                    break;
                case PDO::PARAM_NULL:
                    $values[] = 'NULL';

                    break;
                case PDO::PARAM_STR:
                    $values[] = "'".addslashes((string) $value)."'";

                    break;
                default:
                    if (is_string($value)) {
                        $values[] = "'".addslashes($value)."'";
                    } elseif (is_int($value)) {
                        $values[] = (string) $value;
                    } elseif (is_float($value)) {
                        $values[] = (string) $value;
                    } elseif (is_array($value)) {
                        $values[] = implode(',', $value);
                    } elseif (null === $value) {
                        $values[] = 'NULL';
                    }
            }
        }

        if ($isNamedMarkers) {
            return preg_replace($keys, $values, $sql);
        }

        return preg_replace($keys, $values, $sql, 1, $count);
    }

    /**
     * 执行 SQL.
     *
     * - 记录 SQL 日志
     * - 支持重连
     *
     * @param bool|int $master
     */
    protected function runSql(string $sql, array $bindParams = [], $master = false): bool
    {
        try {
            $bindParams = $this->normalizeBindParams($bindParams);
            $rawSql = ' ('.static::getRawSql($sql, ($bindParams)).')';
            $this->pdoStatement = $this->pdo($master)->prepare($sql);
            $this->bindParams($bindParams);
            $this->pdoStatement->execute();
            $this->setLastSql($this->normalizeLastSql($this->pdoStatement).$rawSql);
            $this->reconnectRetry = 0;
        } catch (PDOException $e) {
            if ($this->needReconnect($e)) {
                $this->reconnectRetry++;
                $this->close();

                return true;
            }

            if ($this->pdoStatement) {
                $sql = $this->normalizeLastSql($this->pdoStatement);
            } else {
                $sql = $this->normalizeErrorLastSql($sql, $bindParams);
            }
            $this->setLastSql($sql.$rawSql, true);
            $this->pdoException($e);
        }

        $this->numRows = $this->pdoStatement->rowCount();

        return false;
    }

    /**
     * 整理当前执行 SQL.
     */
    protected function normalizeLastSql(PDOStatement $pdoStatement): string
    {
        ob_start();
        $pdoStatement->debugDumpParams();
        $sql = trim(ob_get_contents(), PHP_EOL.' ');
        $sql = str_replace(PHP_EOL, ' | ', $sql);
        ob_end_clean();

        return $sql;
    }

    /**
     * 整理当前错误执行 SQL.
     */
    protected function normalizeErrorLastSql(string $sql, array $bindParams): string
    {
        return $sql.' | '.json_encode($bindParams, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 整理 SQL 日志分类.
     */
    protected function normalizeSqlLogCategory(string $sql, bool $failed = false): string
    {
        return '[SQL'.(true === $failed ? ':FAILED' : '').'] '.$sql;
    }

    /**
     * 连接主服务器.
     */
    protected function writeConnect(): PDO
    {
        return $this->connect = $this->commonConnect($this->option['master'], IDatabase::MASTER, true);
    }

    /**
     * 连接读服务器.
     */
    protected function readConnect(): PDO
    {
        if (false === $this->option['distributed'] || empty($this->option['slave'])) {
            return $this->writeConnect();
        }

        if (count($this->connects) <= 1) {
            foreach ($this->option['slave'] as $read) {
                $this->commonConnect($read, null);
            }

            if (0 === count($this->connects)) {
                return $this->writeConnect();
            }
        }

        $connects = $this->connects;
        if (true === $this->option['separate'] && isset($connects[IDatabase::MASTER])) {
            unset($connects[IDatabase::MASTER]);
        }

        if (!$connects) {
            return $this->writeConnect();
        }

        $connects = array_values($connects);
        if (1 === count($connects)) {
            return $connects[0];
        }

        return $this->connect = $connects[floor(mt_rand(0, count($connects) - 1))];
    }

    /**
     * 连接数据库.
     *
     * @return mixed
     */
    protected function commonConnect(array $option = [], ?int $linkid = null, bool $throwException = false)
    {
        if (null === $linkid) {
            $linkid = count($this->connects);
        }

        if (!empty($this->connects[$linkid])) {
            return $this->connects[$linkid];
        }

        try {
            $connect = new PDO(
                $this->parseDsn($option),
                $option['user'],
                $option['password'],
                $option['options']
            );
            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->connects[$linkid] = $connect;
        } catch (PDOException $e) {
            if (false === $throwException) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * 整理 PDO 参数绑定.
     */
    protected function normalizeBindParams(array $bindParams = []): array
    {
        $result = [];
        foreach ($bindParams as $key => $val) {
            $key = is_int($key) || ctype_digit($key) ? (int) $key + 1 : ':'.$key;

            if (is_array($val) && array_key_exists(0, $val) && array_key_exists(1, $val)) {
                $dataType = (int) $val[1];
                $val = $val[0];
            } else {
                $dataType = $this->normalizeBindParamType($val);
            }

            $result[$key] = [$val, $dataType];
        }

        return $result;
    }

    /**
     * PDO 参数绑定.
     */
    protected function bindParams(array $bindParams = []): void
    {
        foreach ($bindParams as $key => $val) {
            $this->pdoStatement->bindValue($key, $val[0], PDO::PARAM_STMT !== $val[1] ? $val[1] : PDO::PARAM_STR);
        }
    }

    /**
     * 获得数据集.
     *
     * @param null|mixed $fetchArgument
     */
    protected function fetchResult(?int $fetchStyle = null, $fetchArgument = null, array $ctorArgs = [], bool $procedure = false): array
    {
        if ($procedure) {
            return $this->fetchProcedureResult($fetchStyle, $fetchArgument, $ctorArgs);
        }

        if (null === $fetchStyle) {
            $fetchStyle = PDO::FETCH_OBJ;
        }

        $args = [$fetchStyle];
        if ($fetchArgument) {
            $args[] = $fetchArgument;
            if ($ctorArgs) {
                $args[] = $ctorArgs;
            }
        }

        return $this->pdoStatement->fetchAll(...$args);
    }

    /**
     * 获得数据集.
     *
     * @param null|mixed $fetchArgument
     *
     * @see http://php.net/manual/vote-note.php?id=123030&page=pdostatement.nextrowset&vote=down
     */
    protected function fetchProcedureResult(?int $fetchStyle = null, $fetchArgument = null, array $ctorArgs = []): array
    {
        $result = [];

        do {
            try {
                $result[] = $this->fetchResult($fetchStyle, $fetchArgument, $ctorArgs);
            } catch (PDOException $e) { // @codeCoverageIgnore
            }
        } while ($this->pdoStatement->nextRowset());

        return $result;
    }

    /**
     * 设置最后执行 SQL.
     */
    protected function setLastSql(string $sql, bool $failed = false): void
    {
        $this->sql = ($failed ? '[FAILED] ' : '').$sql;
        if ($this->dispatch) {
            $this->dispatch->handle(
                IDatabase::SQL_EVENT,
                $this->normalizeSqlLogCategory($this->sql, $failed),
            );
        }
    }

    /**
     * 获取部分事务回滚点名字.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function getSavepointName(): string
    {
        return 'trans'.$this->transactionLevel;
    }

    /**
     * 保存部分事务保存点.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function createSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'SAVEPOINT '.$savepointName);
        $this->pdo(true)->exec($sql);
    }

    /**
     * 回滚部分事务到保存点.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function rollbackSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'ROLLBACK TO SAVEPOINT '.$savepointName);
        $this->pdo(true)->exec($sql);
    }

    /**
     * 清除前面定义的部分事务保存点.
     *
     * - Travis CI 无法通过测试忽略
     *
     * @codeCoverageIgnore
     */
    protected function releaseSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'RELEASE SAVEPOINT '.$savepointName);
        $this->pdo(true)->exec($sql);
    }

    /**
     * 是否需要重连.
     */
    protected function needReconnect(PDOException $e): bool
    {
        if (!$e->errorInfo || !isset($e->errorInfo[1])) {
            return false;
        }

        // errorInfo[1] 表示某个驱动错误码，后期扩展需要优化
        // 可以在驱动重写这个方法
        return in_array($e->errorInfo[1], [2006, 2013], true) &&
            $this->reconnectRetry <= self::RECONNECT_MAX;
    }

    /**
     * PDO 异常处理.
     *
     * @throws \Leevel\Database\ReplaceException
     */
    protected function pdoException(PDOException $e): void
    {
        $message = $e->getMessage();

        // 模拟数据库 replace
        if ('23000' === $e->getCode() &&
            false !== strpos($message, 'Duplicate entry')) {
            throw new ReplaceException($message);
        }

        throw $e;
    }

    /**
     * 初始化查询组件.
     */
    protected function initSelect(): void
    {
        $this->select = new Select($this);
    }
}
