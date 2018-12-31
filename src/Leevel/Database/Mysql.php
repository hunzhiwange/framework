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
    public function parseDsn(array $option): string
    {
        $dsn = [];

        foreach (['Base', 'Port', 'Socket', 'Charset'] as $method) {
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
    public function tableNames(string $dbName, $master = false): array
    {
        $sql = 'SHOW TABLES FROM '.$this->normalizeTableOrColumn($dbName);

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
    public function tableColumns(string $tableName, $master = false): array
    {
        $sql = 'SHOW FULL COLUMNS FROM '.
            $this->normalizeTableOrColumn($tableName);

        $result = [
            'list'           => [],
            'primary_key'    => null,
            'auto_increment' => null,
        ];

        if (($columns = $this->query($sql, [], $master, PDO::FETCH_ASSOC))) {
            foreach ($columns as $column) {
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

                $tmp['comment'] = $column['Comment'];

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
    public function identifierColumn($name): string
    {
        return '*' !== $name ? "`{$name}`" : '*';
    }

    /**
     * 分析 limit.
     *
     * @param null|int $limitCount
     * @param null|int $limitOffset
     *
     * @return string
     */
    public function limitCount(?int $limitCount = null, ?int $limitOffset = null): string
    {
        if (null !== $limitOffset) {
            $sql = 'LIMIT '.$limitOffset;

            if (null !== $limitCount) {
                $sql .= ','.$limitCount;
            } else {
                $sql .= ',999999999999';
            }

            return $sql;
        }

        if (null !== $limitCount) {
            return 'LIMIT '.$limitCount;
        }

        return '';
    }

    /**
     * 获取是否启用部分事务
     *
     * @return bool
     */
    public function hasSavepoints(): bool
    {
        return $this->transactionWithSavepoints;
    }

    /**
     * 基本.
     *
     * @param array $option
     *
     * @return string
     */
    protected function parseBase(array $option): string
    {
        return 'mysql:dbname='.$option['name'].';host='.$option['host'];
    }

    /**
     * 端口.
     *
     * @param array $option
     *
     * @return string
     */
    protected function parsePort(array $option): string
    {
        if (empty($option['port'])) {
            return '';
        }

        return ';port='.$option['port'];
    }

    /**
     * 用 unix socket 加速 php-fpm、mysql、redis 连接.
     *
     * @param array $option
     *
     * @return string
     */
    protected function parseSocket(array $option): string
    {
        if (empty($option['socket'])) {
            return '';
        }

        return ';unix_socket='.$option['socket'];
    }

    /**
     * 编码
     *
     * @param array $option
     *
     * @return string
     */
    protected function parseCharset(array $option): string
    {
        if (empty($option['charset'])) {
            return '';
        }

        return ';charset='.$option['charset'];
    }
}
