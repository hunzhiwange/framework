<?php

declare(strict_types=1);

namespace Leevel\Database;

/**
 * MySQL 数据库连接.
 */
class Mysql extends Database implements IDatabase
{
    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getTableNames(string $dbName, bool|int $master = false): array
    {
        $sql = 'SHOW TABLES FROM '.$dbName;
        $result = [];
        if (($tables = $this->query($sql, [], $master))) {
            foreach ($tables as $v) {
                $result[] = reset($v);
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getTableColumns(string $tableName, bool|int $master = false): array
    {
        $result = [
            'list'            => [],
            'primary_key'     => null,
            'auto_increment'  => null,
            'table_collation' => null,
            'table_comment'   => null,
        ];

        if (!$tableInfo = $this->parseTableInfo($tableName, $master)) {
            return $result;
        }

        $result = array_merge($result, $tableInfo);
        foreach ($this->parseTableColumn($tableName, $master) as $column) {
            $column = $this->normalizeTableColumn((array) $column);
            $result['list'][$column['field']] = $column;
            if ($column['auto_increment']) {
                $result['auto_increment'] = $column['field'];
            }
            if ($column['primary_key']) {
                $result['primary_key'][] = $column['field'];
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function identifierColumn(mixed $name): string
    {
        return '*' !== $name ? "`{$name}`" : '*';
    }

    /**
     * {@inheritDoc}
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
     * 整理字段信息.
     */
    protected function normalizeTableColumn(array $column): array
    {
        $data = [
            'field'     => $column['Field'],
            'type'      => $column['Type'],
            'collation' => $column['Collation'],
            'null'      => 'NO' !== $column['Null'],
            'key'       => $column['Key'],
        ];

        if (null !== $column['Default'] &&
            'null' !== strtolower($column['Default'])) {
            $data['default'] = $column['Default'];
        } else {
            $data['default'] = null;
        }

        $data['extra'] = $column['Extra'];
        $data['comment'] = $column['Comment'];
        $data['primary_key'] = 'pri' === strtolower($column['Key']);

        if (preg_match('/(.+)\((.+)\)/', $column['Type'], $matche)) {
            $data['type_name'] = $matche[1];
            $data['type_length'] = $matche[2];
        } else {
            $data['type_name'] = $column['Type'];
            $data['type_length'] = null;
        }

        $data['auto_increment'] = false !== strpos($column['Extra'], 'auto_increment');

        return $data;
    }

    /**
     * 分析数据库表字段信息.
     */
    protected function parseTableColumn(string $tableName, bool|int $master = false): array
    {
        $sql = 'SHOW FULL COLUMNS FROM `'.$tableName.'`';

        return $this->query($sql, [], $master) ?: [];
    }

    /**
     * 分析数据库表信息.
     */
    protected function parseTableInfo(string $tableName, bool|int $master = false): array
    {
        $sql = 'SELECT TABLE_COLLATION,TABLE_COMMENT FROM '.
            'information_schema.tables WHERE table_name=\''.$tableName.'\';';
        if (!$tableInfo = $this->query($sql, [], $master)) {
            return [];
        }

        return [
            'table_collation' => $tableInfo[0]->TABLE_COLLATION,
            'table_comment'   => $tableInfo[0]->TABLE_COMMENT,
        ];
    }

    /**
     * 基本.
     */
    protected function parseBase(array $option): string
    {
        return 'mysql:dbname='.$option['name'].';host='.$option['host'];
    }

    /**
     * 端口.
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
     */
    protected function parseSocket(array $option): string
    {
        if (empty($option['socket'])) {
            return '';
        }

        return ';unix_socket='.$option['socket'];
    }

    /**
     * 编码.
     */
    protected function parseCharset(array $option): string
    {
        if (empty($option['charset'])) {
            return '';
        }

        return ';charset='.$option['charset'];
    }
}
