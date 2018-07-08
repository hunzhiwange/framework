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

use PDO;

/**
 * mysql 数据库连接.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.03.09
 *
 * @version 1.0
 */
class Mysql extends Connect implements IConnect
{
    /**
     * dsn 解析.
     *
     * @param array $option
     *
     * @return string
     */
    public function parseDsn(array $option)
    {
        $dsn = [];

        foreach ([
            'Base',
            'Port',
            'Socket',
            'Charset',
        ] as $method) {
            $dsn[] = $this->{'parse'.$method}($option);
        }

        return implode('', $dsn);
    }

    /**
     * 取得数据库表名列表.
     *
     * @param string $dbName
     * @param mixed  $master
     *
     * @return array
     */
    public function getTableNames(?string $dbName = null, $master = false)
    {
        // 确定数据库
        if (null === $dbName) {
            $dbName = $this->getCurrentOption('name');
        }

        $sql = 'SHOW TABLES FROM '.$this->qualifyTableOrColumn($dbName);

        $result = [];

        if (($tables = $this->query($sql, [], $master, PDO::FETCH_ASSOC))) {
            foreach ($tables as $v) {
                $result[] = reset($v);
            }
        }

        unset($tables, $sql);

        return $result;
    }

    /**
     * 取得数据库表字段信息.
     *
     * @param string $tableName
     * @param mixed  $master
     *
     * @return array
     */
    public function getTableColumns(string $tableName, $master = false)
    {
        $sql = 'SHOW FULL COLUMNS FROM '.
            $this->qualifyTableOrColumn($tableName);

        $result = [
            'list'           => [],
            'primary_key'    => null,
            'auto_increment' => null,
        ];

        if (($columns = $this->query($sql, [], $master, PDO::FETCH_ASSOC))) {
            foreach ($columns as $column) {
                // 处理字段
                $tmp = [];
                $tmp['name'] = $column['Field'];

                if (preg_match('/(.+)\((.+)\)/', $column['Type'], $matche)) {
                    $tmp['type'] = $matche[1];
                    $tmp['length'] = $matche[1];
                } else {
                    $tmp['type'] = $column['Type'];
                    $tmp['length'] = null;
                }

                $tmp['primary_key'] = 'pri' === strtolower($column['Key']);
                $tmp['auto_increment'] = false !== strpos($column['Extra'], 'auto_increment');

                if (null !== $column['Default'] &&
                    'null' !== strtolower($column['Default'])) {
                    $tmp['default'] = $column['Default'];
                } else {
                    $tmp['default'] = null;
                }

                // 返回结果
                $result['list'][$tmp['name']] = $tmp;

                if ($tmp['auto_increment']) {
                    $result['auto_increment'] = $tmp['name'];
                }

                if ($tmp['primary_key']) {
                    if (!is_array($result['primary_key'])) {
                        $result['primary_key'] = [];
                    }

                    $result['primary_key'][] = $tmp['name'];
                }
            }
        }

        unset($columns, $sql);

        return $result;
    }

    /**
     * sql 字段格式化.
     *
     * @param mixed $name
     *
     * @return string
     */
    public function identifierColumn($name)
    {
        return '*' !== $name ? "`{$name}`" : '*';
    }

    /**
     * 分析 limit.
     *
     * @param null|int $limitcount
     * @param null|int $limitoffset
     *
     * @return string
     */
    public function parseLimitcount(?int $limitcount = null, ?int $limitoffset = null)
    {
        if (null !== $limitoffset) {
            $sql = 'LIMIT '.(int) $limitoffset;

            if (null !== $limitcount) {
                $sql .= ','.(int) $limitcount;
            } else {
                $sql .= ',999999999999';
            }

            return $sql;
        }

        if (null !== $limitcount) {
            return 'LIMIT '.(int) $limitcount;
        }
    }

    /**
     * 基本.
     *
     * @param array $option
     *
     * @return string
     */
    protected function parseBase(array $option)
    {
        return 'mysql:dbname='.$option['name'].
            ';host='.$option['host'];
    }

    /**
     * 端口.
     *
     * @param array $option
     *
     * @return string
     */
    protected function parsePort(array $option)
    {
        if (!empty($option['port'])) {
            return ';port='.$option['port'];
        }
    }

    /**
     * 用 unix socket 加速 php-fpm、mysql、redis 连接.
     *
     * @param array $option
     *
     * @return string
     */
    protected function parseSocket(array $option)
    {
        if (!empty($option['socket'])) {
            return ';unix_socket='.$option['socket'];
        }
    }

    /**
     * 编码
     *
     * @param array $option
     *
     * @return string
     */
    protected function parseCharset(array $option)
    {
        if (!empty($option['charset'])) {
            return ';charset='.$option['charset'];
        }
    }
}
