<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.union",
 *     zh-CN:title="查询语言.union",
 *     path="database/query/union",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class UnionTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="union 联合查询基本用法",
     *     zh-CN:description="",
     *     zh-CN:note="参数支持字符串、子查询器以及它们构成的一维数组。",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION SELECT `test_query`.`tid` AS `id`,`test_query`.`name` AS `value` FROM `test_query` WHERE `test_query`.`first_name` = :test_query_first_name\nUNION SELECT id,value FROM test_query WHERE id > 3\nUNION SELECT `test_query`.`tid` AS `id`,`test_query`.`name` AS `value` FROM `test_query` WHERE `test_query`.`first_name` = :test_query_first_name_1",
                {
                    "test_query_first_name": [
                        "222"
                    ],
                    "test_query_first_name_1": [
                        "222"
                    ]
                },
                false
            ]
            eot;

        $union1 = $connect
            ->table('test_query', 'tid as id,name as value')
            ->where('first_name', '=', '222')
        ;
        $union2 = 'SELECT id,value FROM test_query WHERE id > 3';

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid AS id,tname as value')
                    ->union($union1)
                    ->union($union2)
                    ->union($union1)
                    ->findAll(true)
            )
        );

        $sql2 = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION SELECT `test_query`.`tid` AS `id`,`test_query`.`name` AS `value` FROM `test_query` WHERE `test_query`.`first_name` = :test_query_first_name_2\nUNION SELECT id,value FROM test_query WHERE id > 3\nUNION SELECT `test_query`.`tid` AS `id`,`test_query`.`name` AS `value` FROM `test_query` WHERE `test_query`.`first_name` = :test_query_first_name_3",
                {
                    "test_query_first_name_2": [
                        "222"
                    ],
                    "test_query_first_name_3": [
                        "222"
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql2,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->union([$union1, $union2, $union1])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="union 联合查询支持条件构造器自身为子查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testConditionSelfAsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION SELECT `test_query`.`tid` AS `id`,`test_query`.`name` AS `value` FROM `test_query` WHERE `test_query`.`first_name` = :test_query_first_name",
                {
                    "test_query_first_name": [
                        "222"
                    ]
                },
                false
            ]
            eot;

        $union1 = $connect
            ->table('test_query', 'tid as id,name as value')
            ->where('first_name', '=', '222')
            ->databaseCondition()
        ;

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid AS id,tname as value')
                    ->union($union1)
                    ->findAll(true)
            )
        );

        $sql2 = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION SELECT `test_query`.`tid` AS `id`,`test_query`.`name` AS `value` FROM `test_query` WHERE `test_query`.`first_name` = :test_query_first_name_1\nUNION SELECT `test_query`.`tid` AS `id`,`test_query`.`name` AS `value` FROM `test_query` WHERE `test_query`.`first_name` = :test_query_first_name_2",
                {
                    "test_query_first_name_1": [
                        "222"
                    ],
                    "test_query_first_name_2": [
                        "222"
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql2,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->union([$union1, $union1])
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="unionAll 联合查询不去重",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testUnionAll(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION ALL SELECT id,value FROM test_query WHERE id > 1",
                [],
                false
            ]
            eot;

        $union1 = 'SELECT id,value FROM test_query WHERE id > 1';

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->unionAll($union1)
                    ->findAll(true)
            )
        );
    }

    public function testUnionFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION SELECT id,value FROM test_query WHERE id > 2",
                [],
                false
            ]
            eot;

        $union1 = 'SELECT id,value FROM test_query WHERE id > 1';
        $union2 = 'SELECT id,value FROM test_query WHERE id > 2';

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->if($condition)
                    ->union($union1)
                    ->else()
                    ->union($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION SELECT id,value FROM test_query WHERE id > 1",
                [],
                false
            ]
            eot;

        $union1 = 'SELECT id,value FROM test_query WHERE id > 1';
        $union2 = 'SELECT id,value FROM test_query WHERE id > 2';

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->if($condition)
                    ->union($union1)
                    ->else()
                    ->union($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionAllFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION ALL SELECT id,value FROM test_query WHERE id > 2",
                [],
                false
            ]
            eot;

        $union1 = 'SELECT id,value FROM test_query WHERE id > 1';
        $union2 = 'SELECT id,value FROM test_query WHERE id > 2';

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->if($condition)
                    ->unionAll($union1)
                    ->else()
                    ->unionAll($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionAllFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` \nUNION ALL SELECT id,value FROM test_query WHERE id > 1",
                [],
                false
            ]
            eot;

        $union1 = 'SELECT id,value FROM test_query WHERE id > 1';
        $union2 = 'SELECT id,value FROM test_query WHERE id > 2';

        static::assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query', 'tid as id,tname as value')
                    ->if($condition)
                    ->unionAll($union1)
                    ->else()
                    ->unionAll($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionNotSupportType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid UNION type `NOT FOUND`.'
        );

        $connect = $this->createDatabaseConnectMock();
        $union1 = 'SELECT id,value FROM test2';

        $connect
            ->table('test_query', 'tid as id,tname as value')
            ->union($union1, 'NOT FOUND')
            ->findAll(true)
        ;
    }
}
