<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.comment",
 *     zh-CN:title="查询语言.comment",
 *     path="database/query/comment",
 *     zh-CN:description="",
 * )
 */
class CommentTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="comment 基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "\/*FORCE_MASTER*\/ SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        5
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->comment('FORCE_MASTER')
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }

    public function testFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "\/*FORCE_SLAVE*\/ SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->comment('FORCE_MASTER')
                    ->else()
                    ->comment('FORCE_SLAVE')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "\/*FORCE_MASTER*\/ SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->comment('FORCE_MASTER')
                    ->else()
                    ->comment('FORCE_SLAVE')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
