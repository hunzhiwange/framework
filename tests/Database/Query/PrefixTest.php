<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.prefix",
 *     zh-CN:title="查询语言.prefix",
 *     path="database/query/prefix",
 *     zh-CN:description="",
 * )
 */
final class PrefixTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="prefix 基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_CALC_FOUND_ROWS `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->prefix('SQL_CALC_FOUND_ROWS')
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="prefix 示例用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testSqlNoCache(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_NO_CACHE `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->prefix('SQL_NO_CACHE')
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    public function testFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_CALC_FOUND_ROWS `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->prefix('SQL_NO_CACHE')
                    ->else()
                    ->prefix('SQL_CALC_FOUND_ROWS')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_NO_CACHE `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->prefix('SQL_NO_CACHE')
                    ->else()
                    ->prefix('SQL_CALC_FOUND_ROWS')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }
}
