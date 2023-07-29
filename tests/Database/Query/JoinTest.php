<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.join",
 *     zh-CN:title="查询语言.join",
 *     path="database/query/join",
 *     zh-CN:description="
 * ## join 函数原型
 *
 * ``` php
 * join($table, $cols, ...$cond);
 * ```
 *
 *  - 其中 $table 和 $cols 与 《查询语言.table》中的用法一致。
 *  - $cond 与《查询语言.where》中的用法一致。
 * ",
 * )
 *
 * @internal
 */
final class JoinTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="join 基础用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query_subsql`.`name`,`test_query_subsql`.`value` FROM `test_query` INNER JOIN `test_query_subsql` ON `test_query_subsql`.`name` = :test_query_subsql_name",
                {
                    "test_query_subsql_name": [
                        "小牛"
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
                    ->join('test_query_subsql', 'name,value', 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testBaseUse2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query_subsql`.`name` as a FROM `test_query` INNER JOIN `test_query_subsql` ON `test_query_subsql`.`name` = :test_query_subsql_name",
                {
                    "test_query_subsql_name": [
                        "小牛"
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
                    ->join('test_query_subsql', Condition::raw('[name] as a'), 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` INNER JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->join(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件支持数组和表达式",
     *     zh-CN:description="实质上 where 支持语法特性都支持。",
     *     zh-CN:note="",
     * )
     */
    public function testWithConditionSupportArrayAndExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query_subsql`.`name`,`test_query_subsql`.`value` FROM `test_query` INNER JOIN `test_query_subsql` ON `test_query_subsql`.`hello` = :test_query_subsql_hello AND `test_query_subsql`.`test` > `test_query_subsql`.`name`",
                {
                    "test_query_subsql_hello": [
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
                    ->join('test_query_subsql', 'name,value', ['hello' => 'world', ['test', '>', Condition::raw('[name]')]])
                    ->findAll(),
                $connect,
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件支持闭包",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithConditionIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query_subsql`.`name`,`test_query_subsql`.`value` FROM `test_query` INNER JOIN `test_query_subsql` ON (`test_query_subsql`.`id` < :test_query_subsql_id AND `test_query_subsql`.`name` LIKE :test_query_subsql_name)",
                {
                    "test_query_subsql_id": [
                        5
                    ],
                    "test_query_subsql_name": [
                        "hello"
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
                    ->join('test_query_subsql', 'name,value', function ($select): void {
                        $select
                            ->where('id', '<', 5)
                            ->where('name', 'like', 'hello')
                        ;
                    })
                    ->findAll(),
                $connect,
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="innerJoin 查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInnerJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` INNER JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="leftJoin 查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testLeftJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` LEFT JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="rightJoin 查询",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testRightJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` RIGHT JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="crossJoin 查询",
     *     zh-CN:description="自然连接不用设置 on 条件。",
     *     zh-CN:note="",
     * )
     */
    public function testCrossJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` CROSS JOIN `test_query_subsql` `t`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="naturalJoin 查询",
     *     zh-CN:description="自然连接不用设置 on 条件。",
     *     zh-CN:note="",
     * )
     */
    public function testNaturalJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` NATURAL JOIN `test_query_subsql` `t`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->naturalJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query_subsql`.`name`,`test_query_subsql`.`value` FROM `test_query` INNER JOIN `test_query_subsql` ON `test_query_subsql`.`name` = :test_query_subsql_name",
                {
                    "test_query_subsql_name": [
                        "哥"
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
                    ->if($condition)
                    ->join('test_query_subsql', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('test_query_subsql', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`test_query_subsql`.`name`,`test_query_subsql`.`value` FROM `test_query` INNER JOIN `test_query_subsql` ON `test_query_subsql`.`name` = :test_query_subsql_name",
                {
                    "test_query_subsql_name": [
                        "小牛"
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
                    ->if($condition)
                    ->join('test_query_subsql', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('test_query_subsql', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testInnerJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` INNER JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "仔"
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
                    ->if($condition)
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testInnerJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` INNER JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->if($condition)
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testLeftJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` LEFT JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "仔"
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
                    ->if($condition)
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testLeftJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` LEFT JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->if($condition)
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testRightJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` RIGHT JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "仔"
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
                    ->if($condition)
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testRightJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` RIGHT JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->if($condition)
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testCrossJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` CROSS JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "仔"
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
                    ->if($condition)
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testCrossJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` CROSS JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "小牛"
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
                    ->if($condition)
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testNaturalJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` NATURAL JOIN `test_query_subsql` `t`",
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
                    ->naturalJoin(['t' => 'test_query'], ['name as nikename', 'tt' => 'value'])
                    ->else()
                    ->naturalJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testNaturalJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` NATURAL JOIN `test_query` `t`",
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
                    ->naturalJoin(['t' => 'test_query'], ['name as nikename', 'tt' => 'value'])
                    ->else()
                    ->naturalJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testInnerJsonAndUnionWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'JOIN queries cannot be used while using UNION queries.'
        );

        $connect = $this->createDatabaseConnectMock();
        $union = 'SELECT id,value FROM test2';

        $connect
            ->table('test_query', 'tid as id,tname as value')
            ->union($union)
            ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
            ->findAll()
        ;
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持查询对象",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInnerJoinWithTableIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT `b`.* FROM `test_query_subsql` `b`) b ON `b`.`name` = :b_name",
                {
                    "b_name": [
                        "小牛"
                    ]
                },
                false
            ]
            eot;

        $joinTable = $connect->table('test_query_subsql as b');

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持查询条件对象",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInnerJoinWithTableIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT `b`.* FROM `test_query_subsql` `b`) b ON `b`.`name` = :b_name",
                {
                    "b_name": [
                        "小牛"
                    ]
                },
                false
            ]
            eot;

        $joinTable = $connect
            ->table('test_query_subsql as b')
            ->databaseCondition()
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持闭包",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInnerJoinWithTableIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT `b`.* FROM `test_query_subsql` `b`) b ON `b`.`name` = :b_name",
                {
                    "b_name": [
                        "小牛"
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
                    ->innerJoin(function ($select): void {
                        $select->table('test_query_subsql as b');
                    }, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持数组别名",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInnerJoinWithTableIsArrayCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`foo`.`name` AS `nikename`,`foo`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT `b`.* FROM `test_query_subsql` `b`) foo ON `foo`.`name` = :foo_name",
                {
                    "foo_name": [
                        "小牛"
                    ]
                },
                false
            ]
            eot;

        $joinTable = $connect
            ->table('test_query_subsql as b')
            ->databaseCondition()
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->innerJoin(['foo' => $joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testInnerJoinWithTableIsArrayAndTheAliasKeyMustBeString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Alias must be string,but integer given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition()
        ;

        $connect
            ->table('test_query')
            ->innerJoin([$joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
            ->findAll()
        ;
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持表达式",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInnerJsonWithTableNameIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`a`.`name` AS `nikename`,`a`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT * FROM test_query_subsql) a ON `a`.`name` = `test_query`.`name`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->innerJoin('(SELECT * FROM test_query_subsql)', ['name as nikename', 'tt' => 'value'], 'name', '=', Condition::raw('[test_query.name]'))
                    ->findAll(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持表达式别名",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInnerJsonWithTableNameIsExpressionWithAsCustomAlias(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`bar`.`name` AS `nikename`,`bar`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT * FROM test_query_subsql) bar ON `bar`.`name` = `test_query`.`name`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->innerJoin('(SELECT * FROM test_query_subsql) as bar', ['name as nikename', 'tt' => 'value'], 'name', '=', Condition::raw('[test_query.name]'))
                    ->findAll(),
                $connect
            )
        );
    }
}
