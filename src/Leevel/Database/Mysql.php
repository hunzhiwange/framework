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
        if ($tables = $this->query($sql, [], $master)) {
            foreach ((array) $tables as $v) {
                $result[] = current((array) $v);
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
            'list' => [],
            'primary_key' => null,
            'auto_increment' => null,
            'table_collation' => null,
            'table_comment' => null,
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
    public function getUniqueIndex(string $tableName, bool|int $master = false): array
    {
        $sql = 'SELECT
    TABLE_NAME,
    INDEX_NAME,
       INDEX_COMMENT,
    GROUP_CONCAT(DISTINCT COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS COLUMNS
FROM
    INFORMATION_SCHEMA.STATISTICS
WHERE
    TABLE_NAME = \''.$tableName.'\'
    AND NON_UNIQUE = 0
GROUP BY
    TABLE_NAME,
    INDEX_NAME,
    INDEX_COMMENT';

        $data = (array) $this->query($sql, [], $master) ?: [];
        if (!$data) {
            return [];
        }

        // 转换为指定格式的数组
        $uniqueIndexes = [];
        foreach ($data as $row) {
            $row = (array) $row;
            $indexName = $row['INDEX_NAME'];
            $columns = explode(',', (string) $row['COLUMNS']);
            $comment = $row['INDEX_COMMENT'] ?? '';
            if ('PRIMARY' === $indexName) {
                $comment = 'ID';
            }

            $uniqueIndexes[$indexName] = [
                'field' => $columns,
                'comment' => $comment,
            ];
        }

        return $uniqueIndexes;
    }

    /**
     * {@inheritDoc}
     */
    public function identifierColumn(string $name): string
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
            'field' => $column['Field'],
            'type' => $column['Type'],
            'collation' => $column['Collation'],
            'null' => 'NO' !== $column['Null'],
            'key' => $column['Key'],
        ];

        if (null !== $column['Default']
            && 'null' !== strtolower($column['Default'])) {
            // MySQL8和5.7 CURRENT_TIMESTAMP
            // MariaDB 10 current_timestamp()
            // 这里处理为一致
            if ('current_timestamp()' === $column['Default']) {
                $column['Default'] = 'CURRENT_TIMESTAMP';
            }
            $data['default'] = $column['Default'];
        } else {
            $data['default'] = null;
        }

        // MySQL8 和 MySQL5.7 这里返回值不一致，5.7 存在，8.0 不存在
        // 将这里修改为一致
        $data['extra'] = 'DEFAULT_GENERATED' === $column['Extra'] ? '' : $column['Extra'];
        $data['comment'] = $column['Comment'];
        $data['primary_key'] = 'pri' === strtolower($column['Key']);

        if (preg_match('/(.+)\((.+)\)/', $column['Type'], $matches)) {
            $data['type'] = $matches[1];
            // 从 MySQL8.0.17 版本开始，TINYINT, SMALLINT, MEDIUMINT, INT, and BIGINT 类型的显示宽度将失效
            if (\in_array($matches[1], ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'], true)) {
                $matches[2] = null;
            } elseif (ctype_digit($matches[2])) {
                $matches[2] = (int) $matches[2];
            }
            $data['type_extra'] = $matches[2];
            $data['type_length'] = (int) $data['type_extra'];
        } else {
            $data['type_extra'] = null;
            $data['type_length'] = 0;
        }

        $data['auto_increment'] = str_contains($column['Extra'], 'auto_increment');

        return $data;
    }

    /**
     * 分析数据库表字段信息.
     */
    protected function parseTableColumn(string $tableName, bool|int $master = false): array
    {
        $sql = 'SHOW FULL COLUMNS FROM `'.$tableName.'`';

        return (array) $this->query($sql, [], $master) ?: [];
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
            'table_comment' => $tableInfo[0]->TABLE_COMMENT,
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
