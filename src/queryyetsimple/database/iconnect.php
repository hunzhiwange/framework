<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\database;

/**
 * iconnect 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface iconnect
{

    /**
     * 返回 Pdo 查询连接
     *
     * @param mixed $mixMaster
     * @note boolean false (读服务器) true (写服务器)
     * @note 其它 去对应服务器连接ID 0 表示主服务器
     * @return mixed
     */
    public function getPdo($mixMaster = false);

    /**
     * 查询数据记录
     *
     * @param string $strSql sql 语句
     * @param array $arrBindParams sql 参数绑定
     * @param mixed $mixMaster
     * @param int $intFetchType
     * @param mixed $mixFetchArgument
     * @param array $arrCtorArgs
     * @return mixed
     */
    public function query($strSql, $arrBindParams = [], $mixMaster = false, $intFetchType = PDO::FETCH_OBJ, $mixFetchArgument = null, $arrCtorArgs = []);

    /**
     * 执行 sql 语句
     *
     * @param string $strSql sql 语句
     * @param array $arrBindParams sql 参数绑定
     * @return int
     */
    public function execute($strSql, $arrBindParams = []);

    /**
     * 执行数据库事务
     *
     * @param callable $calAction 事务回调
     * @return mixed
     */
    public function transaction(callable $calAction);

    /**
     * 启动事务
     *
     * @return void
     */
    public function beginTransaction();

    /**
     * 检查是否处于事务中
     *
     * @return boolean
     */
    public function inTransaction();

    /**
     * 用于非自动提交状态下面的查询提交
     *
     * @return void
     */
    public function commit();

    /**
     * 事务回滚
     *
     * @return void
     */
    public function rollBack();

    /**
     * 获取最后插入 ID 或者列
     *
     * @param string $strName 自增序列名
     * @return string
     */
    public function lastInsertId($strName = null);

    /**
     * 获取最近一次查询的 sql 语句
     *
     * @param bool $booWithBindParams 是否和绑定参数一起返回
     * @return string
     */
    public function getLastSql($booWithBindParams = false);

    /**
     * 获取最近一次绑定参数
     *
     * @return array
     */
    public function getBindParams();

    /**
     * 返回影响记录
     *
     * @return int
     */
    public function getNumRows();

    /**
     * 注册 SQL 监视器
     *
     * @param callable $calSqlListen
     * @return void
     */
    public function registerListen(callable $calSqlListen);

    /**
     * 释放 PDO 预处理查询
     *
     * @return void
     */
    public function freePDOStatement();

    /**
     * 关闭数据库连接
     *
     * @return void
     */
    public function closeDatabase();

    /**
     * sql 表达式格式化
     *
     * @param string $sSql
     * @param string $sTableName
     * @param array $arrMapping
     * @return string
     */
    public function qualifyExpression($sSql, $sTableName, array $arrMapping = null);

    /**
     * 表或者字段格式化（支持别名）
     *
     * @param string $sName
     * @param string $sAlias
     * @param string $sAs
     * @return string
     */
    public function qualifyTableOrColumn($sName, $sAlias = null, $sAs = null);

    /**
     * 字段格式化
     *
     * @param string $sKey
     * @param string $sTableName
     * @return string
     */
    public function qualifyColumn($sKey, $sTableName);

    /**
     * 字段值格式化
     *
     * @param boolean $booQuotationMark
     * @param mixed $mixValue
     * @return mixed
     */
    public function qualifyColumnValue($mixValue, $booQuotationMark = true);

    /**
     * 返回当前配置连接信息（方便其他组件调用设置为 public）
     *
     * @param string $strOptionName
     * @return array
     */
    public function getCurrentOption($strOptionName = null);

    /**
     * 分析 sql 类型数据
     *
     * @param string $strSql
     * @return string
     */
    public function getSqlType($strSql);

    /**
     * 分析绑定参数类型数据
     *
     * @param mixed $mixValue
     * @return string
     */
    public function getBindParamType($mixValue);

    /**
     * dsn 解析
     *
     * @param array $arrOption
     * @return string
     */
    public function parseDsn($arrOption);

    /**
     * 取得数据库表名列表
     *
     * @param string $sDbName
     * @param mixed $mixMaster
     * @return array
     */
    public function getTableNames($sDbName = null, $mixMaster = false);

    /**
     * 取得数据库表字段信息
     *
     * @param string $sTableName
     * @param mixed $mixMaster
     * @return array
     */
    public function getTableColumns($sTableName, $mixMaster = false);

    /**
     * sql 字段格式化
     *
     * @return string
     */
    public function identifierColumn($sName);

    /**
     * 分析 limit
     *
     * @param mixed $mixLimitcount
     * @param mixed $mixLimitoffset
     * @return string
     */
    public function parseLimitcount($mixLimitcount = null, $mixLimitoffset = null);
}
