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

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.join",
 *     zh-CN:title="查询语言.join",
 *     path="database/query/join",
 *     description="
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
 */
class JoinTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="join 基础用法",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->join('test_query_subsql', 'name,value', 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->join(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件支持数组和表达式",
     *     zh-CN:description="实质上 where 支持语法特性都支持。",
     *     note="",
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
                        "'world'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->join('test_query_subsql', 'name,value', ['hello' => 'world', ['test', '>', '{[name]}']])
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件支持闭包",
     *     zh-CN:description="",
     *     note="",
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
                        5,
                        1
                    ],
                    "test_query_subsql_name": [
                        "'hello'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->join('test_query_subsql', 'name,value', function ($select) {
                        $select
                            ->where('id', '<', 5)
                            ->where('name', 'like', 'hello');
                    })
                    ->findAll(true),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="innerJoin 查询",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="leftJoin 查询",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="rightJoin 查询",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="fullJoin 查询",
     *     zh-CN:description="",
     *     note="MySQL 不支持 FULL JOIN，仅示例。",
     * )
     */
    public function testFullJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` FULL JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->fullJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="crossJoin 查询",
     *     zh-CN:description="自然连接不用设置 on 条件。",
     *     note="",
     * )
     */
    public function testCrossJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` CROSS JOIN `test_query_subsql` `t`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="naturalJoin 查询",
     *     zh-CN:description="自然连接不用设置 on 条件。",
     *     note="",
     * )
     */
    public function testNaturalJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` NATURAL JOIN `test_query_subsql` `t`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->naturalJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(true)
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
                        "'哥'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->join('test_query_subsql', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('test_query_subsql', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(true)
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->join('test_query_subsql', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('test_query_subsql', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(true)
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
                        "'仔'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                        "'仔'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                        "'仔'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFullJsonFlow(): void
    {
        // MySQL 不支持 FULL JOIN，仅示例
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` FULL JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "'仔'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->fullJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->fullJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFullJsonFlow2(): void
    {
        // MySQL 不支持 FULL JOIN，仅示例
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test_query` FULL JOIN `test_query_subsql` `t` ON `t`.`name` = :t_name",
                {
                    "t_name": [
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->fullJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->fullJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                        "'仔'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
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
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->naturalJoin(['t' => 'test_query'], ['name as nikename', 'tt' => 'value'])
                    ->else()
                    ->naturalJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->fi()
                    ->findAll(true)
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
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->naturalJoin(['t' => 'test_query'], ['name as nikename', 'tt' => 'value'])
                    ->else()
                    ->naturalJoin(['t' => 'test_query_subsql'], ['name as nikename', 'tt' => 'value'])
                    ->fi()
                    ->findAll(true)
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
            ->findAll(true);
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持查询对象",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect->table('test_query_subsql as b');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持查询条件对象",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('test_query_subsql as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持闭包",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->innerJoin(function ($select) {
                        $select->table('test_query_subsql as b');
                    }, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持数组别名",
     *     zh-CN:description="",
     *     note="",
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
                        "'小牛'",
                        2
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('test_query_subsql as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->innerJoin(['foo' => $joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
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
            ->databaseCondition();

        $connect
            ->table('test_query')
            ->innerJoin([$joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
            ->findAll(true);
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持表达式",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJsonWithTableNameIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`a`.`name` AS `nikename`,`a`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT * FROM test_query_subsql) a ON `a`.`name` = `test_query`.`name`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->innerJoin('(SELECT * FROM test_query_subsql)', ['name as nikename', 'tt' => 'value'], 'name', '=', '{[test_query.name]}')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持表达式别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJsonWithTableNameIsExpressionWithAsCustomAlias(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.*,`bar`.`name` AS `nikename`,`bar`.`value` AS `tt` FROM `test_query` INNER JOIN (SELECT * FROM test_query_subsql) bar ON `bar`.`name` = `test_query`.`name`",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->innerJoin('(SELECT * FROM test_query_subsql) as bar', ['name as nikename', 'tt' => 'value'], 'name', '=', '{[test_query.name]}')
                    ->findAll(true)
            )
        );
    }
}
