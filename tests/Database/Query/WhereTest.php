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
 *     title="Query lang.where",
 *     zh-CN:title="查询语言.where",
 *     path="database/query/where",
 *     description="",
 * )
 */
class WhereTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="where 查询条件",
     *     zh-CN:description="最基本的用法为字段 （表达式） 值。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        // 字段 （表达式） 值
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :__test_query__id",
                {
                    "__test_query__id": [
                        1,
                        1
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
                    ->where('id', '=', 1)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件默认为等于 `=`",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :__test_query__id",
                {
                    "__test_query__id": [
                        2,
                        1
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
                    ->where('id', 2)
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持多次调用",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse3(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :__test_query__id AND `test_query`.`name` > :__test_query__name AND `test_query`.`value` LIKE :__test_query__value",
                {
                    "__test_query__id": [
                        2,
                        1
                    ],
                    "__test_query__name": [
                        "'狗蛋'",
                        2
                    ],
                    "__test_query__value": [
                        "'小鸭子'",
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
                    ->where('id', 2)
                    ->where('name', '>', '狗蛋')
                    ->where('value', 'like', '小鸭子')
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持数组方式",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` LIKE :__test_query__name",
                {
                    "__test_query__name": [
                        "'技术'",
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
                    ->where(['name', 'like', '技术'])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持二维数组多个条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testMultiDimensionalArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` LIKE :__test_query__name AND `test_query`.`value` <> :__test_query__value",
                {
                    "__test_query__name": [
                        "'技术'",
                        2
                    ],
                    "__test_query__value": [
                        "'结局'",
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
                    ->where([
                        ['name', 'like', '技术'],
                        ['value', '<>', '结局'],
                    ])
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="orWhere 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testOrWhere(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` LIKE :__test_query__name OR `test_query`.`value` <> :__test_query__value",
                {
                    "__test_query__name": [
                        "'技术'",
                        2
                    ],
                    "__test_query__value": [
                        "'结局'",
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
                    ->where('name', 'like', '技术')
                    ->orWhere('value', '<>', '结局')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereBetween 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :__test_query__id__between0 AND :__test_query__id__between1",
                {
                    "__test_query__id__between0": [
                        1,
                        1
                    ],
                    "__test_query__id__between1": [
                        100,
                        1
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
                    ->whereBetween('id', [1, 100])
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :__test_query__id__between0 AND :__test_query__id__between1",
                {
                    "__test_query__id__between0": [
                        1,
                        1
                    ],
                    "__test_query__id__between1": [
                        10,
                        1
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
                    ->where('id', 'between', [1, 10])
                    ->findAll(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :__test_query__id__between0 AND :__test_query__id__between1 AND `test_query`.`name` BETWEEN :__test_query__name__between0 AND :__test_query__name__between1",
                {
                    "__test_query__id__between0": [
                        1,
                        1
                    ],
                    "__test_query__id__between1": [
                        100,
                        1
                    ],
                    "__test_query__name__between0": [
                        5,
                        1
                    ],
                    "__test_query__name__between1": [
                        22,
                        1
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
                    ->whereBetween([
                        ['id', [1, 100]],
                        ['name', [5, 22]],
                    ])
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereNotBetween 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereNotBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT BETWEEN :__test_query__id__notbetween0 AND :__test_query__id__notbetween1",
                {
                    "__test_query__id__notbetween0": [
                        1,
                        1
                    ],
                    "__test_query__id__notbetween1": [
                        10,
                        1
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
                    ->whereNotBetween('id', [1, 10])
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT BETWEEN :__test_query__id__notbetween0 AND :__test_query__id__notbetween1",
                {
                    "__test_query__id__notbetween0": [
                        1,
                        1
                    ],
                    "__test_query__id__notbetween1": [
                        10,
                        1
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
                    ->where('id', 'not between', [1, 10])
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereIn 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:__test_query__id__in0,:__test_query__id__in1)",
                {
                    "__test_query__id__in0": [
                        2,
                        1
                    ],
                    "__test_query__id__in1": [
                        50,
                        1
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
                    ->whereIn('id', [2, 50])
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:__test_query__id__in0,:__test_query__id__in1)",
                {
                    "__test_query__id__in0": [
                        "'1'",
                        2
                    ],
                    "__test_query__id__in1": [
                        "'10'",
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
                    ->where('id', 'in', '1,10')
                    ->findAll(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:__test_query__id__in0,:__test_query__id__in1)",
                {
                    "__test_query__id__in0": [
                        2,
                        1
                    ],
                    "__test_query__id__in1": [
                        50,
                        1
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
                    ->where('id', 'in', [2, 50])
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereNotIn 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereNotIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT IN (:__test_query__id__in0,:__test_query__id__in1)",
                {
                    "__test_query__id__in0": [
                        2,
                        1
                    ],
                    "__test_query__id__in1": [
                        50,
                        1
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
                    ->whereNotIn('id', [2, 50])
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT IN (:__test_query__id__in0,:__test_query__id__in1)",
                {
                    "__test_query__id__in0": [
                        "'1'",
                        2
                    ],
                    "__test_query__id__in1": [
                        "'10'",
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
                    ->where('id', 'not in', '1,10')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereNull 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NULL",
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
                    ->whereNull('id')
                    ->findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', 'null')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereNotNull 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereNotNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NOT NULL",
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
                    ->whereNotNull('id')
                    ->findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', 'not null')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件未指定值默认为 null",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereDefaultNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NULL",
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
                    ->where('id')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件指定值为 null",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereEqualNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NULL",
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
                    ->where('id', '=', null)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereLike 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` LIKE :__test_query__id",
                {
                    "__test_query__id": [
                        "'5'",
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
                    ->whereLike('id', '5')
                    ->findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', 'like', '5')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereNotLike 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereNotLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT LIKE :__test_query__id",
                {
                    "__test_query__id": [
                        "'5'",
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
                    ->whereNotLike('id', '5')
                    ->findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', 'not like', '5')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereExists 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereExists(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__exists__test_query_subsql__id)",
                {
                    "__test_query__exists__test_query_subsql__id": [
                        1,
                        1
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
                    ->whereExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 1);
                        }
                    )
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql`)",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $subSelect = $connect->table('test_query_subsql');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where([':exists' => $subSelect])
                    ->findAll(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (select *from test_query_subsql)",
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
                    ->where([':exists' => 'select *from test_query_subsql'])
                    ->findAll(true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__exists__test_query_subsql__id)",
                {
                    "__test_query__exists__test_query_subsql__id": [
                        1,
                        1
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
                    ->where(
                        [
                            ':exists' => function ($select) {
                                $select
                                    ->table('test_query_subsql')
                                    ->where('id', 1);
                            },
                        ]
                    )
                    ->findAll(true),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereNotExists 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereNotExists(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE NOT EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__notexists__test_query_subsql__id)",
                {
                    "__test_query__notexists__test_query_subsql__id": [
                        1,
                        1
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
                    ->whereNotExists(
                        function ($select) {
                            $select
                                ->table('test_query_subsql')
                                ->where('id', 1);
                        }
                    )
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持分组",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereGroup(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :__test_query__id OR (`test_query`.`votes` > :__test_query__votes AND `test_query`.`title` <> :__test_query__title)",
                {
                    "__test_query__votes": [
                        100,
                        1
                    ],
                    "__test_query__title": [
                        "'Admin'",
                        2
                    ],
                    "__test_query__id": [
                        5,
                        1
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
                    ->where('id', 5)
                    ->orWhere(function ($select) {
                        $select
                            ->where('votes', '>', 100)
                            ->where('title', '<>', 'Admin');
                    })
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :__test_query__id OR `test_query`.`name` = :__test_query__name AND (`test_query`.`votes` > :__test_query__votes OR `test_query`.`title` <> :__test_query__title)",
                {
                    "__test_query__votes": [
                        100,
                        1
                    ],
                    "__test_query__title": [
                        "'Admin'",
                        2
                    ],
                    "__test_query__id": [
                        5,
                        1
                    ],
                    "__test_query__name": [
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
                    ->where('id', 5)
                    ->orWhere('name', '小牛')
                    ->where(function ($select) {
                        $select
                            ->where('votes', '>', 100)
                            ->orWhere('title', '<>', 'Admin');
                    })
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持表达式",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testConditionalExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`post`,`test_query`.`value`,concat(\"tt_\",`test_query`.`id`) FROM `test_query` WHERE concat(\"hello_\",`test_query`.`posts`) = `test_query`.`id`",
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
                    ->table('test_query', 'post,value,{concat("tt_",[id])}')
                    ->where('{concat("hello_",[posts])}', '=', '{[id]}')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持二维数组的键值为字段",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testArrayKeyAsField(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :__test_query__id AND `test_query`.`name` IN (:__test_query__name__in0,:__test_query__name__in1,:__test_query__name__in2) AND `test_query`.`weidao` BETWEEN :__test_query__weidao__between0 AND :__test_query__weidao__between1 AND `test_query`.`value` IS NULL AND `test_query`.`remark` IS NOT NULL AND `test_query`.`goods` = :__test_query__goods AND `test_query`.`hello` = :__test_query__hello",
                {
                    "__test_query__id": [
                        "'故事'",
                        2
                    ],
                    "__test_query__name__in0": [
                        1,
                        1
                    ],
                    "__test_query__name__in1": [
                        2,
                        1
                    ],
                    "__test_query__name__in2": [
                        3,
                        1
                    ],
                    "__test_query__weidao__between0": [
                        "'40'",
                        2
                    ],
                    "__test_query__weidao__between1": [
                        "'100'",
                        2
                    ],
                    "__test_query__goods": [
                        "'东亚商品'",
                        2
                    ],
                    "__test_query__hello": [
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
                    ->where([
                        'id'     => ['=', '故事'],
                        'name'   => ['in', [1, 2, 3]],
                        'weidao' => ['between', '40,100'],
                        'value'  => 'null',
                        'remark' => ['not null'],
                        'goods'  => '东亚商品',
                        'hello'  => ['world'],
                    ])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持字符串语法 `:string`",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSupportString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` = 11 and `test_query`.`value` = 22 and concat(\"tt_\",`test_query`.`id`)",
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
                    ->where([':string' => '{[name] = 11 and [test_query.value] = 22 and concat("tt_",[id])}'])
                    ->findAll(true)
            )
        );
    }

    public function testSupportStringMustBeString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'String type only supports string,but array given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->where([':string' => []])
            ->findAll(true);
    }

    public function testSupportStringMustBeString2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'String type only supports string,but integer given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->where([':string' => 1])
            ->findAll(true);
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持分组语法 `:subor` 和 `suband` ",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSupportSubandSubor(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`hello` = :__test_query__hello OR (`test_query`.`id` LIKE :__test_query__subor__test_query__id)",
                {
                    "__test_query__subor__test_query__id": [
                        "'你好'",
                        2
                    ],
                    "__test_query__hello": [
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
                    ->where([
                        'hello'   => 'world',
                        ':subor'  => ['id', 'like', '你好'],
                    ])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持分组语法 `:subor` 和 `suband` 任意嵌套",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSupportSubandSuborMore(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`hello` = :__test_query__hello OR (`test_query`.`id` LIKE :__test_query__subor__test_query__id AND `test_query`.`value` = :__test_query__subor__test_query__value) AND (`test_query`.`id2` LIKE :__test_query__suband__test_query__id2 OR `test_query`.`value2` = :__test_query__suband__test_query__value2 OR (`test_query`.`child_one` > :__test_query__subor__test_query__child_one AND `test_query`.`child_two` LIKE :__test_query__subor__test_query__child_two))",
                {
                    "__test_query__subor__test_query__child_one": [
                        "'123'",
                        2
                    ],
                    "__test_query__subor__test_query__child_two": [
                        "'123'",
                        2
                    ],
                    "__test_query__suband__test_query__id2": [
                        "'你好2'",
                        2
                    ],
                    "__test_query__suband__test_query__value2": [
                        "'helloworld2'",
                        2
                    ],
                    "__test_query__subor__test_query__id": [
                        "'你好'",
                        2
                    ],
                    "__test_query__subor__test_query__value": [
                        "'helloworld'",
                        2
                    ],
                    "__test_query__hello": [
                        "'111'",
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
                    ->where([
                        'hello'   => '111',
                        ':subor'  => [
                            ['id', 'like', '你好'],
                            ['value', '=', 'helloworld'],
                        ],
                        ':suband' => [
                            ':logic' => 'or',
                            ['id2', 'like', '你好2'],
                            ['value2', '=', 'helloworld2'],
                            ':subor' => [
                                ['child_one', '>', '123'],
                                ['child_two', 'like', '123'],
                            ],
                        ],
                    ])
                    ->findAll(true),
                1
            )
        );
    }

    public function testWhereNotSupportMethod(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Select do not implement magic method `whereNotSupportMethod`.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->whereNotSupportMethod()
            ->findAll(true);
    }

    public function testCallWhereSugarFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` LIKE :__test_query__id",
                {
                    "__test_query__id": [
                        "'6'",
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
                    ->whereLike('id', '5')
                    ->else()
                    ->whereLike('id', '6')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testCallWhereSugarFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` LIKE :__test_query__id",
                {
                    "__test_query__id": [
                        "'5'",
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
                    ->whereLike('id', '5')
                    ->else()
                    ->whereLike('id', '6')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrWhereFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`value` <> :__test_query__value",
                {
                    "__test_query__value": [
                        "'bar'",
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
                    ->orWhere('value', '<>', 'foo')
                    ->else()
                    ->orWhere('value', '<>', 'bar')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrWhereFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`value` <> :__test_query__value",
                {
                    "__test_query__value": [
                        "'foo'",
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
                    ->orWhere('value', '<>', 'foo')
                    ->else()
                    ->orWhere('value', '<>', 'bar')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testWhereExistsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__exists__test_query_subsql__id)",
                {
                    "__test_query__exists__test_query_subsql__id": [
                        2,
                        1
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
                    ->whereExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testWhereExistsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__exists__test_query_subsql__id)",
                {
                    "__test_query__exists__test_query_subsql__id": [
                        2,
                        1
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
                    ->whereExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testWhereNotExistsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE NOT EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__notexists__test_query_subsql__id)",
                {
                    "__test_query__notexists__test_query_subsql__id": [
                        2,
                        1
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
                    ->whereNotExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereNotExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testWhereNotExistsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__exists__test_query_subsql__id)",
                {
                    "__test_query__exists__test_query_subsql__id": [
                        2,
                        1
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
                    ->whereExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereExists(
                        function ($select) {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件字段可以指定表",
     *     zh-CN:description="字段条件用法和 table 中的字段用法一致，详情可以查看《查询语言.table》。",
     *     note="",
     * )
     */
    public function testWhereFieldWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` = :__test_query__name",
                {
                    "__test_query__name": [
                        1,
                        1
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
                    ->where('test_query.name', '=', 1)
                    ->findAll(true)
            )
        );
    }

    public function testWhereBetweenValueNotAnArrayException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between param value must be an array which not less than two elements.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->whereBetween('id', 'foo')
            ->findAll(true);
    }

    public function testWhereBetweenValueNotAnArrayException2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between param value must be an array which not less than two elements.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->whereBetween('id', [1])
            ->findAll(true);
    }

    public function testWhereBetweenArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__id__test_query_subsql__id) AND :__test_query__id__between1",
                {
                    "__test_query__id__test_query_subsql__id": [
                        1,
                        1
                    ],
                    "__test_query__id__between1": [
                        100,
                        1
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
                    ->whereBetween('id', [function ($select) {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1);
                    }, 100])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="where 查询条件支持复杂的子查询",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereInArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__id__test_query_subsql__id),:__test_query__id__in1)",
                {
                    "__test_query__id__test_query_subsql__id": [
                        1,
                        1
                    ],
                    "__test_query__id__in1": [
                        100,
                        1
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
                    ->whereIn('id', [function ($select) {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1);
                    }, 100])
                    ->findAll(true)
            )
        );
    }

    public function testWhereBetweenArrayItemIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT 1) AND :__test_query__id__between1",
                {
                    "__test_query__id__between1": [
                        100,
                        1
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
                    ->whereBetween('id', ['(SELECT 1)', 100])
                    ->findAll(true)
            )
        );
    }

    public function testWhereInArrayItemIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT 1),:__test_query__id__in1)",
                {
                    "__test_query__id__in1": [
                        100,
                        1
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
                    ->whereIn('id', ['(SELECT 1)', 100])
                    ->findAll(true)
            )
        );
    }

    public function testWhereBetweenArrayItemIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1) AND :__test_query__id__between1",
                {
                    "__test_query__id__between1": [
                        100,
                        1
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $select = $connect
            ->table('test_query_subsql', 'id')
            ->one();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->whereBetween('id', [$select, 100])
                    ->findAll(true)
            )
        );
    }

    public function testWhereInArrayItemIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1),:__test_query__id__in1)",
                {
                    "__test_query__id__in1": [
                        100,
                        1
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $select = $connect
            ->table('test_query_subsql', 'id')
            ->one();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->whereIn('id', [$select, 100])
                    ->findAll(true)
            )
        );
    }

    public function testWhereBetweenArrayItemIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1) AND :__test_query__id__between1",
                {
                    "__test_query__id__between1": [
                        100,
                        1
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $condition = $connect
            ->table('test_query_subsql', 'id')
            ->one()
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->whereBetween('id', [$condition, 100])
                    ->findAll(true)
            )
        );
    }

    public function testWhereInArrayItemIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1),:__test_query__id__in1)",
                {
                    "__test_query__id__in1": [
                        100,
                        1
                    ]
                },
                false,
                null,
                null,
                []
            ]
            eot;

        $condition = $connect
            ->table('test_query_subsql', 'id')
            ->one()
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->whereIn('id', [$condition, 100])
                    ->findAll(true)
            )
        );
    }

    public function testWhereInIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql`)",
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
                    ->whereIn('id', function ($select) {
                        $select->table('test_query_subsql', 'id');
                    })
                    ->findAll(true)
            )
        );
    }

    public function testWhereInIsSubString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql`)",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $subSql = $connect
            ->table('test_query_subsql', 'id')
            ->makeSql(true);

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', $subSql)
                    ->findAll(true)
            )
        );
    }

    public function testWhereInIsSubIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql`)",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $subSql = $connect->table('test_query_subsql', 'id');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', $subSql)
                    ->findAll(true)
            )
        );
    }

    public function testWhereEqualIsSub(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :__test_query__id__test_query_subsql__id)",
                {
                    "__test_query__id__test_query_subsql__id": [
                        1,
                        1
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
                    ->where('id', '=', function ($select) {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1);
                    })
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="whereRaw 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWhereRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, id)",
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
                    ->whereRaw('FIND_IN_SET(1, id)')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="orWhereRaw 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testOrWhereRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, id) OR FIND_IN_SET(1, id)",
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
                    ->whereRaw('FIND_IN_SET(1, id)')
                    ->orWhereRaw('FIND_IN_SET(1, id)')
                    ->findAll(true)
            )
        );
    }

    public function testWhereRawFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, options_id)",
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
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testWhereRawFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, goods_id)",
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
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrWhereRawFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, options_id) OR FIND_IN_SET(1, goods_id)",
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
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->orWhereRaw('FIND_IN_SET(1, options_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->orWhereRaw('FIND_IN_SET(1, goods_id)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrWhereRawFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, goods_id) OR FIND_IN_SET(1, options_id)",
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
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->orWhereRaw('FIND_IN_SET(1, options_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->orWhereRaw('FIND_IN_SET(1, goods_id)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
