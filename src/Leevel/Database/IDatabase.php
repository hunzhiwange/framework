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
use PDO;

/**
 * IDatabase 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 *
 * @see \Leevel\Database\Proxy\IDatabase 请保持接口设计的一致性
 */
interface IDatabase
{
    /**
     * 断线重连尝试次数.
     *
     * @var int
     */
    const RECONNECT_MAX = 3;

    /**
     * 主服务 PDO 标识.
     *
     * @var int
     */
    const MASTER = 999999999;

    /**
     * SQL 日志事件.
     *
     * @var string
     */
    const SQL_EVENT = 'database.sql';

    /**
     * 返回 Pdo 查询连接.
     *
     * @param bool|int $master
     *                         - bool false (读服务器) true (写服务器)
     *                         - int 其它去对应服务器连接ID 0 表示主服务器
     *
     * @return mixed
     */
    public function pdo($master = false);

    /**
     * 查询数据记录.
     *
     * @param string     $sql           sql 语句
     * @param array      $bindParams    sql 参数绑定
     * @param bool|int   $master
     * @param int        $fetchType
     * @param null|mixed $fetchArgument
     * @param array      $ctorArgs
     *
     * @return mixed
     */
    public function query(string $sql, array $bindParams = [], $master = false, int $fetchType = PDO::FETCH_OBJ, $fetchArgument = null, array $ctorArgs = []);

    /**
     * 执行 sql 语句.
     *
     * @param string $sql        sql 语句
     * @param array  $bindParams sql 参数绑定
     *
     * @return int|string
     */
    public function execute(string $sql, array $bindParams = []);

    /**
     * 执行数据库事务
     *
     * @param \Closure $action 事务回调
     *
     * @return mixed
     */
    public function transaction(Closure $action);

    /**
     * 启动事务.
     */
    public function beginTransaction(): void;

    /**
     * 检查是否处于事务中.
     *
     * @return bool
     */
    public function inTransaction(): bool;

    /**
     * 用于非自动提交状态下面的查询提交.
     */
    public function commit(): void;

    /**
     * 事务回滚.
     */
    public function rollBack(): void;

    /**
     * 获取最后插入 ID 或者列.
     *
     * @param null|string $name 自增序列名
     *
     * @return string
     */
    public function lastInsertId(?string $name = null): string;

    /**
     * 获取最近一次查询的 sql 语句.
     *
     * @return array
     */
    public function lastSql(): array;

    /**
     * 返回影响记录.
     *
     * @return int
     */
    public function numRows(): int;

    /**
     * 关闭数据库.
     */
    public function close(): void;

    /**
     * 释放 PDO 预处理查询.
     */
    public function freePDOStatement(): void;

    /**
     * 关闭数据库连接.
     */
    public function closeConnects(): void;

    /**
     * sql 表达式格式化.
     *
     * @param string $sql
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeExpression(string $sql, string $tableName): string;

    /**
     * 表或者字段格式化（支持别名）.
     *
     * @param string      $name
     * @param null|string $alias
     * @param null|string $as
     *
     * @return string
     */
    public function normalizeTableOrColumn(string $name, ?string $alias = null, ?string $as = null): string;

    /**
     * 字段格式化.
     *
     * @param string $key
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeColumn(string $key, string $tableName): string;

    /**
     * 字段值格式化.
     *
     * @param mixed $value
     * @param bool  $quotationMark
     *
     * @return mixed
     */
    public function normalizeColumnValue($value, bool $quotationMark = true);

    /**
     * 分析 sql 类型数据.
     *
     * @param string $sql
     *
     * @return string
     */
    public function normalizeSqlType(string $sql): string;

    /**
     * 分析绑定参数类型数据.
     *
     * @param mixed $value
     *
     * @return int
     */
    public function normalizeBindParamType($value): int;

    /**
     * dsn 解析.
     *
     * @param array $option
     *
     * @return string
     */
    public function parseDsn(array $option): string;

    /**
     * 取得数据库表名列表.
     *
     * @param string   $dbName
     * @param bool|int $master
     *
     * @return array
     */
    public function tableNames(string $dbName, $master = false): array;

    /**
     * 取得数据库表字段信息.
     *
     * @param string   $tableName
     * @param bool|int $master
     *
     * @return array
     */
    public function tableColumns(string $tableName, $master = false): array;

    /**
     * sql 字段格式化.
     *
     * @param mixed $name
     *
     * @return string
     */
    public function identifierColumn($name): string;

    /**
     * 分析 limit.
     *
     * @param null|int $limitCount
     * @param null|int $limitOffset
     *
     * @return string
     */
    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string;
}
