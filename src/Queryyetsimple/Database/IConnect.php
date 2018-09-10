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

/**
 * IConnect 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.23
 *
 * @version 1.0
 */
interface IConnect
{
    /**
     * 返回 Pdo 查询连接.
     *
     * @param mixed $master
     * @note boolean false (读服务器) true (写服务器)
     * @note 其它 去对应服务器连接ID 0 表示主服务器
     *
     * @return mixed
     */
    public function getPdo($master = false);

    /**
     * 查询数据记录.
     *
     * @param string $sql           sql 语句
     * @param array  $bindParams    sql 参数绑定
     * @param mixed  $master
     * @param int    $fetchType
     * @param mixed  $fetchArgument
     * @param array  $ctorArgs
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
     * @return int
     */
    public function execute(string $sql, array $bindParams = []);

    /**
     * 执行数据库事务
     *
     * @param callable $action 事务回调
     *
     * @return mixed
     */
    public function transaction(callable $action);

    /**
     * 启动事务
     */
    public function beginTransaction();

    /**
     * 检查是否处于事务中.
     *
     * @return bool
     */
    public function inTransaction();

    /**
     * 用于非自动提交状态下面的查询提交.
     */
    public function commit();

    /**
     * 事务回滚.
     */
    public function rollBack();

    /**
     * 获取最后插入 ID 或者列.
     *
     * @param string $name 自增序列名
     *
     * @return string
     */
    public function lastInsertId(?string $name = null);

    /**
     * 获取最近一次查询的 sql 语句.
     *
     * @param bool $withBindParams 是否和绑定参数一起返回
     *
     * @return string
     */
    public function getLastSql(bool $withBindParams = false);

    /**
     * 获取最近一次绑定参数.
     *
     * @return array
     */
    public function getBindParams();

    /**
     * 返回影响记录.
     *
     * @return int
     */
    public function getNumRows();

    /**
     * 注册 SQL 监视器.
     *
     * @param callable $sqlListen
     */
    public function registerListen(callable $sqlListen);

    /**
     * 释放 PDO 预处理查询.
     */
    public function freePDOStatement();

    /**
     * 关闭数据库连接.
     */
    public function closeDatabase();

    /**
     * 取得数据库表字段信息缓存.
     *
     * @param string $tableName
     * @param mixed  $master
     *
     * @return array
     */
    public function getTableColumnsCache(string $tableName, $master = false);

    /**
     * sql 表达式格式化.
     *
     * @param string $sql
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeExpression(string $sql, string $tableName);

    /**
     * 表或者字段格式化（支持别名）.
     *
     * @param string $name
     * @param string $alias
     * @param string $as
     *
     * @return string
     */
    public function normalizeTableOrColumn(string $name, ?string $alias = null, string $as = null);

    /**
     * 字段格式化.
     *
     * @param string $key
     * @param string $tableName
     *
     * @return string
     */
    public function normalizeColumn(string $key, string $tableName);

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
    public function normalizeSqlType(string $sql);

    /**
     * 分析绑定参数类型数据.
     *
     * @param mixed $value
     *
     * @return string
     */
    public function normalizeBindParamType($value);

    /**
     * 返回当前配置连接信息（方便其他组件调用设置为 public）.
     *
     * @param string $optionName
     *
     * @return array
     */
    public function getCurrentOption(?string $optionName = null);

    /**
     * dsn 解析.
     *
     * @param array $option
     *
     * @return string
     */
    public function parseDsn(array $option);

    /**
     * 取得数据库表名列表.
     *
     * @param string $dbName
     * @param mixed  $master
     *
     * @return array
     */
    public function getTableNames(?string $dbName = null, $master = false);

    /**
     * 取得数据库表字段信息.
     *
     * @param string $tableName
     * @param mixed  $master
     *
     * @return array
     */
    public function getTableColumns(string $tableName, $master = false);

    /**
     * sql 字段格式化.
     *
     * @param mixed $name
     *
     * @return string
     */
    public function identifierColumn($name);

    /**
     * 分析 limit.
     *
     * @param null|int $limitcount
     * @param null|int $limitoffset
     *
     * @return string
     */
    public function parseLimitcount(?int $limitcount = null, ?int $limitoffset = null);
}
