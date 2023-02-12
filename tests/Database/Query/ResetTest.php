<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.reset",
 *     zh-CN:title="查询语言.reset",
 *     path="database/query/reset",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class ResetTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="重置所有",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`new` = :test_query_subsql_new",
                {
                    "test_query_subsql_new": [
                        "world"
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
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->reset()
                    ->table('test_query_subsql')
                    ->where('new', '=', 'world')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="重置某一项",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testResetItem(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name`,`test_query`.`id` FROM `test_query` WHERE `test_query`.`new` LIKE :test_query_new",
                {
                    "test_query_new": [
                        "new"
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
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->setColumns('name,id')
                    ->reset('where')
                    ->where('new', 'like', 'new')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    public function testResetFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name`,`test_query`.`id` FROM `test_query` WHERE `test_query`.`id` = :test_query_id AND `test_query`.`name` LIKE :test_query_name AND `test_query`.`foo` LIKE :test_query_foo",
                {
                    "test_query_id": [
                        5
                    ],
                    "test_query_name": [
                        "me"
                    ],
                    "test_query_foo": [
                        "bar"
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
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->setColumns('name,id')
                    ->if($condition)
                    ->reset()
                    ->table('foo')
                    ->else()
                    ->where('foo', 'like', 'bar')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testResetFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query_subsql`.* FROM `test_query_subsql`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->setColumns('name,id')
                    ->if($condition)
                    ->reset()
                    ->table('test_query_subsql')
                    ->else()
                    ->where('foo', 'like', 'bar')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }
}
