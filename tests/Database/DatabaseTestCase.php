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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database;

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

    protected function runSql(array $data): void
    {
        // 排除掉明显异常的数据
        if (!isset($data[0]) ||
            count($data) < 2 ||
            !is_string($data[0]) ||
            !is_array($data[1])) {
            return;
        }

        // 校验 SQL 语句的正确性，真实查询实际数据库
        if (6 === count($data) && $this->isQuerySql($data[0])) {
            // MySQL 不支持 FULL JOIN，仅示例
            if (false !== strpos($data[0], 'FULL JOIN')) {
                return;
            }

            $connect = $this->createDatabaseConnect();
            $connect->query(...$data);
            $this->assertSame(1, 1);
        } elseif (2 === count($data) && $this->isExecuteSql($data[0])) {
            $connect = $this->createDatabaseConnect();
            $connect->execute(...$data);
            $this->assertSame(1, 1);
        }
    }

    protected function isQuerySql(string $sql): bool
    {
        return false !== strpos($sql, 'SELECT');
    }

    protected function isExecuteSql(string $sql): bool
    {
        return false !== strpos($sql, 'INSERT') ||
            false !== strpos($sql, 'REPLACE') ||
            false !== strpos($sql, 'TRUNCATE') ||
            false !== strpos($sql, 'DELETE') ||
            false !== strpos($sql, 'UPDATE');
    }

    protected function clearDatabaseTable(): void
    {
        if (!method_exists($this, 'getDatabaseTable')) {
            return;
        }

        $this->truncateDatabase($this->getDatabaseTable());
    }
}
