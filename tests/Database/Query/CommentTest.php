<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.comment',
    'zh-CN:title' => '查询语言.comment',
    'path' => 'database/query/comment',
])]
/**
 * @internal
 */
final class CommentTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'comment 基础用法',
    ])]
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->comment('FORCE_MASTER')
                    ->where('id', '=', 5)
                    ->findAll(),
                $connect
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

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->comment('FORCE_MASTER')
                    ->else()
                    ->comment('FORCE_SLAVE')
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
                "\/*FORCE_MASTER*\/ SELECT `test_query`.* FROM `test_query`",
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
                    ->comment('FORCE_MASTER')
                    ->else()
                    ->comment('FORCE_SLAVE')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }
}
