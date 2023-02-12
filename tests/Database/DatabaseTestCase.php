<?php

declare(strict_types=1);

namespace Tests\Database;

use Leevel\Database\Mysql;
use Tests\Database;
use Tests\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    use Database;

    protected function setUp(): void
    {
        $this->clearDatabaseTable();
        $this->metaWithDatabase();
    }

    protected function tearDown(): void
    {
        $this->clearDatabaseTable();
        $this->metaWithoutDatabase();
        $this->freeDatabaseConnects();
    }

    protected function varJson(array $data, ?int $id = null): string
    {
        $this->runSql($data);

        return parent::varJson($data, $id);
    }

    protected function varJsonSql(mixed $data, Mysql $connect, ?int $id = null): string
    {
        return $this->varJson($connect->getRealLastSql(), $id);
    }

    protected function runSql(array $data): void
    {
        // 排除掉明显异常的数据
        if (!isset($data[0])
            || \count($data) < 2
            || !\is_string($data[0])
            || !\is_array($data[1])) {
            return;
        }

        // 校验 SQL 语句的正确性，真实查询实际数据库
        if (6 === \count($data) && $this->isQuerySql($data[0])) {
            // MySQL 不支持 FULL JOIN，仅示例
            if (str_contains($data[0], 'FULL JOIN')) {
                return;
            }

            $connect = $this->createDatabaseConnect();
            $connect->query(...$data);
            static::assertSame(1, 1);
        } elseif (3 === \count($data) && $this->isExecuteSql($data[0])) {
            $connect = $this->createDatabaseConnect();
            $connect->execute(...$data);
            static::assertSame(1, 1);
        }
    }

    protected function isQuerySql(string $sql): bool
    {
        return str_contains($sql, 'SELECT');
    }

    protected function isExecuteSql(string $sql): bool
    {
        return str_contains($sql, 'INSERT')
              || str_contains($sql, 'REPLACE')
              || str_contains($sql, 'TRUNCATE')
              || str_contains($sql, 'DELETE')
              || str_contains($sql, 'UPDATE');
    }

    protected function clearDatabaseTable(): void
    {
        if (!method_exists($this, 'getDatabaseTable')) {
            return;
        }

        $this->truncateDatabase($this->getDatabaseTable());
    }
}
