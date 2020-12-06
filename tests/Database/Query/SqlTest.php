<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.sql",
 *     zh-CN:title="查询语言.sql",
 *     path="database/query/sql",
 *     zh-CN:description="",
 * )
 */
class SqlTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` ORDER BY `test_query`.`create_at` DESC LIMIT 1",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->sql(true)
                    ->latest()
                    ->findOne()
            )
        );

        $sql = <<<'eot'
            [
                "delete from test where id = ?",
                [
                    22
                ],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql(true)
                    ->delete('delete from test where id = ?', [22]),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="例外 findOne 等也支持快捷",
     *     zh-CN:description="绝大多数都支持这个功能，例如 findAll,insertAll 等。",
     *     zh-CN:note="",
     * )
     */
    public function testFindOne(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` ORDER BY `test_query`.`create_at` DESC LIMIT 1",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->latest()
                    ->findOne(true),
                2
            )
        );
    }
}
