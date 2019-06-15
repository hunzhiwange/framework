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
use Leevel\Event\IDispatch;
use PDO;
use PDOException;
use Throwable;

/**
 * 数据库连接抽象层
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.03.09
 *
 * @version 1.0
 */
abstract class Database
{
    use Proxy;

    /**
     * 所有数据库连接.
     *
     * @var array
     */
    protected $connects = [];

    /**
     * 当前数据库连接.
     *
     * @var array
     */
    protected $connect;

    /**
     * PDO 预处理语句对象
     *
     * @var PDOStatement
     */
    protected $pdoStatement;

    /**
     * 数据查询组件.
     *
     * @var \Leevel\Database\Select
     */
    protected $select;

    /**
     * 数据库连接参数.
     *
     * @var array
     */
    protected $option = [];

    /**
     * sql 最后查询语句.
     *
     * @var string
     */
    protected $sql;

    /**
     * sql 绑定参数.
     *
     * @var array
     */
    protected $bindParams = [];

    /**
     * sql 影响记录数量.
     *
     * @var int
     */
    protected $numRows = 0;

    /**
     * 事务等级.
     *
     * @var int
     */
    protected $transactionLevel = 0;

    /**
     * 是否开启部分事务.
     * 依赖数据库是否支持部分事务.
     *
     * @var bool
     */
    protected $transactionWithSavepoints = false;

    /**
     * 是否仅仅是事务回滚.
     *
     * @var bool
     */
    protected $isRollbackOnly = false;

    /**
     * 断线重连次数.
     *
     * @var int
     */
    protected $reconnectRetry = 0;

    /**
     * 事件处理器.
     *
     * @var \Leevel\Event\IDispatch
     */
    protected $dispatch;

    /**
     * 构造函数.
     *
     * @param array                        $option
     * @param null|\Leevel\Event\IDispatch $dispatch
     */
    public function __construct(array $option, ?IDispatch $dispatch = null)
    {
        $this->option = $option;
        $this->dispatch = $dispatch;
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
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        $this->initSelect();

        return $this->select->{$method}(...$args);
    }

    /**
     * 返回代理.
     *
     * @return \Leevel\Database\Select
     */
    public function proxy(): Select
    {
        $this->initSelect();

        return $this->select;
    }

    /**
     * 返回 Pdo 查询连接.
     *
     * @param bool|int $master
     *                         - bool false (读服务器) true (写服务器)
     *                         - int 其它去对应服务器连接ID 0 表示主服务器
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
     * @param null|int   $fetchStyle
     * @param null|mixed $fetchArgument
     * @param array      $ctorArgs
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function query(string $sql, array $bindParams = [], $master = false, ?int $fetchStyle = null, $fetchArgument = null, array $ctorArgs = [])
    {
        $this->initSelect();

        $this->setLastSql($sql, $bindParams);

        if (!in_array(($sqlType = $this->normalizeSqlType($sql)), [
            'select',
            'procedure',
        ], true)) {
            $e = 'The query method only allows select and procedure SQL statements.';

            throw new InvalidArgumentException($e);
        }

        $this->pdoStatement = $this->pdo($master)->prepare($sql);

        $this->bindParams($bindParams);

        try {
            $this->pdoStatement->execute();
            $this->reconnectRetry = 0;
        } catch (PDOException $e) {
            if ($this->needReconnect($e)) {
                $this->reconnectRetry++;
                $this->close();

                return self::query($sql, $bindParams, $master, $fetchStyle, $fetchArgument, $ctorArgs);
            }

            throw $e;
        }

        $this->numRows = $this->pdoStatement->rowCount();

        return $this->fetchResult($fetchStyle, $fetchArgument, $ctorArgs, 'procedure' === $sqlType);
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

        $this->setLastSql($sql, $bindParams);

        if (in_array(($sqlType = $this->normalizeSqlType($sql)), [
            'select',
            'procedure',
        ], true)) {
            $e = 'The query method not allows select and procedure SQL statements.';

            throw new InvalidArgumentException($e);
        }

        $this->pdoStatement = $this->pdo(true)->prepare($sql);

        $this->bindParams($bindParams);

        try {
            $this->pdoStatement->execute();
            $this->reconnectRetry = 0;
        } catch (PDOException $e) {
            if ($this->needReconnect($e)) {
                $this->reconnectRetry++;
                $this->close();

                return self::execute($sql, $bindParams);
            }

            $this->pdoException($e);
        }

        $this->numRows = $this->pdoStatement->rowCount();

        if (in_array($sqlType, [
            'insert',
            'replace',
        ], true)) {
            return $this->lastInsertId();
        }

        return $this->numRows;
    }

    /**
     * 执行数据库事务
     *
     * @param \Closure $action
     *
     * @return mixed
     */
    public function transaction(Closure $action)
    {
        $this->beginTransaction();

        try {
            $result = call_user_func_array($action, [
                $this,
            ]);

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
            } catch (Exception $e) { // @codeCoverageIgnore
                $this->transactionLevel--; // @codeCoverageIgnore

                throw $e; // @codeCoverageIgnore
            } // @codeCoverageIgnore
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->createSavepoint($this->getSavepointName());
        }
    }

    /**
     * 检查是否处于事务中.
     *
     * @return bool
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
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->releaseSavepoint($this->getSavepointName());
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
        } elseif ($this->transactionLevel > 1 && $this->hasSavepoints()) {
            $this->rollbackSavepoint($this->getSavepointName());
            $this->transactionLevel--;
        } else {
            $this->isRollbackOnly = true;
            $this->transactionLevel = max(0, $this->transactionLevel - 1);
        }
    }

    /**
     * 设置是否启用部分事务.
     *
     * @param bool $savepoints
     */
    public function setSavepoints(bool $savepoints): void
    {
        $this->transactionWithSavepoints = $savepoints;
    }

    /**
     * 获取最后插入 ID 或者列.
     *
     * @param null|string $name 自增序列名
     *
     * @return string
     */
    public function lastInsertId(?string $name = null): string
    {
        return $this->connect->lastInsertId($name);
    }

    /**
     * 获取最近一次查询的 sql 语句.
     *
     * @return array
     */
    public function lastSql(): array
    {
        return [$this->sql, $this->bindParams];
    }

    /**
     * 返回影响记录.
     *
     * @return int
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
        $this->pdoStatement = null;
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
     * sql 表达式格式化.
     *
     * @param string $sql
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeExpression(string $sql, string $tableName): string
    {
        preg_match_all('/\[[a-z][a-z0-9_\.]*\]|\[\*\]/i', $sql,
            $matches, PREG_OFFSET_CAPTURE);

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
     *
     * @param string      $name
     * @param null|string $alias
     * @param null|string $as
     *
     * @return string
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
     *
     * @param string $key
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeColumn(string $key, string $tableName): string
    {
        return $this->normalizeTableOrColumn("{$tableName}.{$key}");
    }

    /**
     * 字段值格式化.
     *
     * @param mixed $value
     * @param bool  $quotationMark
     *
     * @return mixed
     */
    public function normalizeColumnValue($value, bool $quotationMark = true)
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (null === $value) {
            return;
        }

        $value = trim($value);

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
     *
     * @param string $sql
     *
     * @return string
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
     *
     * @return int
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
     * 连接主服务器.
     *
     * @return \PDO
     */
    protected function writeConnect(): PDO
    {
        return $this->connect = $this->commonConnect($this->option['master'], IDatabase::MASTER, true);
    }

    /**
     * 连接读服务器.
     *
     * @return \PDO
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
     * @param array    $option
     * @param null|int $linkid
     * @param bool     $throwException
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
            $connect = $this->connects[$linkid] = new PDO(
                $this->parseDsn($option),
                $option['user'],
                $option['password'],
                $option['options']
            );

            $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $connect;
        } catch (PDOException $e) {
            if (false === $throwException) {
                return false;
            }

            throw $e;
        }
    }

    /**
     * pdo 参数绑定.
     *
     * @param array $bindParams
     */
    protected function bindParams(array $bindParams = []): void
    {
        foreach ($bindParams as $key => $val) {
            $key = is_numeric($key) ? $key + 1 : ':'.$key;

            if (is_array($val)) {
                $param = $val[1];
                $val = $val[0];
            } else {
                $param = PDO::PARAM_STR;
            }

            $this->pdoStatement->bindValue($key, $val, $param);
        }
    }

    /**
     * 获得数据集.
     *
     * @param null|int   $fetchStyle
     * @param null|mixed $fetchArgument
     * @param array      $ctorArgs
     * @param bool       $procedure
     *
     * @return array
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
     * @param null|int   $fetchStyle
     * @param null|mixed $fetchArgument
     * @param array      $ctorArgs
     *
     * @see http://php.net/manual/vote-note.php?id=123030&page=pdostatement.nextrowset&vote=down
     *
     * @return array
     */
    protected function fetchProcedureResult(?int $fetchStyle = null, $fetchArgument = null, array $ctorArgs = []): array
    {
        $result = [];

        do {
            try {
                $result[] = $tim = $this->fetchResult($fetchStyle, $fetchArgument, $ctorArgs);
            } catch (PDOException $e) {
            }
        } while ($this->pdoStatement->nextRowset());

        return $result;
    }

    /**
     * 设置 sql 绑定参数.
     *
     * @param string $sql
     * @param array  $bindParams
     */
    protected function setLastSql(string $sql, array $bindParams = []): void
    {
        $this->sql = $sql;
        $this->bindParams = $bindParams;

        $this->handleDispatch($sql, $bindParams);
    }

    /**
     * 事件派发.
     *
     * @param string $sql
     * @param array  $bindParams
     */
    protected function handleDispatch(string $sql, array $bindParams = []): void
    {
        if ($this->dispatch) {
            $this->dispatch->handle(IDatabase::SQL_EVENT, $sql, $bindParams);
        }
    }

    /**
     * 获取部分事务回滚点名字.
     *
     * @return string
     */
    protected function getSavepointName(): string
    {
        return 'trans'.$this->transactionLevel;
    }

    /**
     * 保存部分事务保存点.
     *
     * @param string $savepointName
     */
    protected function createSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'SAVEPOINT '.$savepointName);
        $this->pdo(true)->exec($sql);
    }

    /**
     * 回滚部分事务到保存点.
     *
     * @param string $savepointName
     */
    protected function rollbackSavepoint(string $savepointName)
    {
        $this->setLastSql($sql = 'ROLLBACK TO SAVEPOINT '.$savepointName);
        $this->pdo(true)->exec($sql);
    }

    /**
     * 清除前面定义的部分事务保存点.
     *
     * @param string $savepointName
     */
    protected function releaseSavepoint(string $savepointName): void
    {
        $this->setLastSql($sql = 'RELEASE SAVEPOINT '.$savepointName);
        $this->pdo(true)->exec($sql);
    }

    /**
     * 是否需要重连.
     *
     * @return bool
     */
    protected function needReconnect(PDOException $e): bool
    {
        // errorInfo[1] 表示某个驱动错误码，后期扩展需要优化
        // 可以在驱动重写这个方法
        return in_array($e->errorInfo[1], [2006, 2013], true) &&
            $this->reconnectRetry <= self::RECONNECT_MAX;
    }

    /**
     * PDO 异常处理.
     *
     * @param \PDOException $e
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
