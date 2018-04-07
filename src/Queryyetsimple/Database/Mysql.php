<?php
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

use PDO;

/**
 * mysql 数据库连接
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.03.09
 * @version 1.0
 */
class Mysql extends Connect implements IConnect
{

    /**
     * dsn 解析
     *
     * @param array $arrOption
     * @return string
     */
    public function parseDsn($arrOption)
    {
        $arrDsn = [];

        foreach ([
            'Base',
            'Port',
            'Socket',
            'Charset'
        ] as $strMethod) {
            $arrDsn[] = $this->{'get' . $strMethod}($arrOption);
        }

        return implode('', $arrDsn);
    }

    /**
     * 取得数据库表名列表
     *
     * @param string $sDbName
     * @param mixed $mixMaster
     * @return array
     */
    public function getTableNames($sDbName = null, $mixMaster = false)
    {
        // 确定数据库
        if ($sDbName === null) {
            $sDbName = $this->getCurrentOption('name');
        }
        
        $strSql = 'SHOW TABLES FROM ' . $this->qualifyTableOrColumn($sDbName);
        $arrResult = [];
        if (($arrTables = $this->query($strSql, [], $mixMaster, PDO::FETCH_ASSOC))) {
            foreach ($arrTables as $arrTable) {
                $arrResult[] = reset($arrTable);
            }
        }
        unset($arrTables, $strSql);

        return $arrResult;
    }

    /**
     * 取得数据库表字段信息
     *
     * @param string $sTableName
     * @param mixed $mixMaster
     * @return array
     */
    public function getTableColumns($sTableName, $mixMaster = false)
    {
        $strSql = 'SHOW FULL COLUMNS FROM ' . $this->qualifyTableOrColumn($sTableName);
        $arrResult = [
            'list' => [],
            'primary_key' => null,
            'auto_increment' => null
        ];

        if (($arrColumns = $this->query($strSql, [], $mixMaster, PDO::FETCH_ASSOC))) {
            foreach ($arrColumns as $arrColumn) {
                // 处理字段
                $arrTemp = [];
                $arrTemp['name'] = $arrColumn['Field'];
                if (preg_match('/(.+)\((.+)\)/', $arrColumn['Type'], $arrMatch)) {
                    $arrTemp['type'] = $arrMatch[1];
                    $arrTemp['length'] = $arrMatch[1];
                } else {
                    $arrTemp['type'] = $arrColumn['Type'];
                    $arrTemp['length'] = null;
                }
                $arrTemp['primary_key'] = strtolower($arrColumn['Key']) == 'pri';
                $arrTemp['auto_increment'] = strpos($arrColumn['Extra'], 'auto_increment') !== false;
                if (! is_null($arrColumn['Default']) && strtolower($arrColumn['Default']) != 'null') {
                    $arrTemp['default'] = $arrColumn['Default'];
                } else {
                    $arrTemp['default'] = null;
                }

                // 返回结果
                $arrResult['list'][$arrTemp['name']] = $arrTemp;
                if ($arrTemp['auto_increment']) {
                    $arrResult['auto_increment'] = $arrTemp['name'];
                }
                if ($arrTemp['primary_key']) {
                    if (! is_array($arrResult['primary_key'])) {
                        $arrResult['primary_key'] = [];
                    }
                    $arrResult['primary_key'][] = $arrTemp['name'];
                }
            }
        }
        unset($arrColumns, $strSql);
        return $arrResult;
    }

    /**
     * sql 字段格式化
     *
     * @return string
     */
    public function identifierColumn($sName)
    {
        return $sName != '*' ? "`{$sName}`" : '*';
    }

    /**
     * 分析 limit
     *
     * @param mixed $mixLimitcount
     * @param mixed $mixLimitoffset
     * @return string
     */
    public function parseLimitcount($mixLimitcount = null, $mixLimitoffset = null)
    {
        if (! is_null($mixLimitoffset)) {
            $sSql = 'LIMIT ' . ( int ) $mixLimitoffset;
            if (! is_null($mixLimitcount)) {
                $sSql .= ',' . ( int ) $mixLimitcount;
            } else {
                $sSql .= ',999999999999';
            }

            return $sSql;
        } elseif (! is_null($mixLimitcount)) {
            return 'LIMIT ' . ( int ) $mixLimitcount;
        }
    }

    /**
     * 基本
     *
     * @param array $arrOption
     * @return string
     */
    protected function getBase($arrOption)
    {
        return 'mysql:dbname=' . $arrOption['name'] . ';host=' . $arrOption['host'];
    }

    /**
     * 端口
     *
     * @param array $arrOption
     * @return string
     */
    protected function getPort($arrOption)
    {
        if (! empty($arrOption['port'])) {
            return ';port=' . $arrOption['port'];
        }
    }

    /**
     * 用 unix socket 加速 php-fpm、mysql、redis 连接
     *
     * @param array $arrOption
     * @return string
     */
    protected function getSocket($arrOption)
    {
        if (! empty($arrOption['socket'])) {
            return ';unix_socket=' . $arrOption['socket'];
        }
    }

    /**
     * 编码
     *
     * @param array $arrOption
     * @return string
     */
    protected function getCharset($arrOption)
    {
        if (! empty($arrOption['charset'])) {
            return ';charset=' . $arrOption['charset'];
        }
    }
}
