<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.where',
    'zh-CN:title' => '查询语言.where',
    'path' => 'database/query/where',
])]
final class WhereTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'where 查询条件',
        'zh-CN:description' => <<<'EOT'
最基本的用法为字段 （表达式） 值。
EOT,
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        // 字段 （表达式） 值
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        1
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
                    ->where('id', '=', 1)
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件默认为等于 `=`',
    ])]
    public function testBaseUse2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        2
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
                    ->where('id', 2)
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持多次调用',
    ])]
    public function testBaseUse3(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id AND `test_query`.`name` > :test_query_name AND `test_query`.`value` LIKE :test_query_value",
                {
                    "test_query_id": [
                        2
                    ],
                    "test_query_name": [
                        "狗蛋"
                    ],
                    "test_query_value": [
                        "小鸭子"
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
                    ->where('id', 2)
                    ->where('name', '>', '狗蛋')
                    ->where('value', 'like', '小鸭子')
                    ->findAll(),
                $connect,
                2
            )
        );
    }

    public function testBaseUse4(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id",
                {
                    "test_query_id": [
                        1.6
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
                    ->where('id', 1.6)
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持数组方式',
    ])]
    public function testArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` LIKE :test_query_name",
                {
                    "test_query_name": [
                        "技术"
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
                    ->where(['name', 'like', '技术'])
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持二维数组多个条件',
    ])]
    public function testMultiDimensionalArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` LIKE :test_query_name AND `test_query`.`value` <> :test_query_value",
                {
                    "test_query_name": [
                        "技术"
                    ],
                    "test_query_value": [
                        "结局"
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
                    ->where([
                        ['name', 'like', '技术'],
                        ['value', '<>', '结局'],
                    ])
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'orWhere 查询条件',
    ])]
    public function testOrWhere(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` LIKE :test_query_name OR `test_query`.`value` <> :test_query_value",
                {
                    "test_query_name": [
                        "技术"
                    ],
                    "test_query_value": [
                        "结局"
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
                    ->where('name', 'like', '技术')
                    ->orWhere('value', '<>', '结局')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereBetween 查询条件',
    ])]
    public function testWhereBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :test_query_id_between0 AND :test_query_id_between1",
                {
                    "test_query_id_between0": [
                        1
                    ],
                    "test_query_id_between1": [
                        100
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
                    ->whereBetween('id', [1, 100])
                    ->findAll(),
                $connect
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :test_query_id_between0 AND :test_query_id_between1",
                {
                    "test_query_id_between0": [
                        1
                    ],
                    "test_query_id_between1": [
                        10
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
                    ->where('id', 'between', [1, 10])
                    ->findAll(),
                $connect,
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :test_query_id_between0 AND :test_query_id_between1 AND `test_query`.`name` BETWEEN :test_query_name_between0 AND :test_query_name_between1",
                {
                    "test_query_id_between0": [
                        1
                    ],
                    "test_query_id_between1": [
                        100
                    ],
                    "test_query_name_between0": [
                        5
                    ],
                    "test_query_name_between1": [
                        22
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
                    ->whereBetween([
                        ['id', [1, 100]],
                        ['name', [5, 22]],
                    ])
                    ->findAll(),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereNotBetween 查询条件',
    ])]
    public function testWhereNotBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT BETWEEN :test_query_id_notbetween0 AND :test_query_id_notbetween1",
                {
                    "test_query_id_notbetween0": [
                        1
                    ],
                    "test_query_id_notbetween1": [
                        10
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
                    ->whereNotBetween('id', [1, 10])
                    ->findAll(),
                $connect
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT BETWEEN :test_query_id_notbetween0 AND :test_query_id_notbetween1",
                {
                    "test_query_id_notbetween0": [
                        1
                    ],
                    "test_query_id_notbetween1": [
                        10
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
                    ->where('id', 'not between', [1, 10])
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereIn 查询条件',
    ])]
    public function testWhereIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0,:test_query_id_in1)",
                {
                    "test_query_id_in0": [
                        2
                    ],
                    "test_query_id_in1": [
                        50
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
                    ->whereIn('id', [2, 50])
                    ->findAll(),
                $connect
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0,:test_query_id_in1)",
                {
                    "test_query_id_in0": [
                        "1"
                    ],
                    "test_query_id_in1": [
                        "10"
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
                    ->where('id', 'in', '1,10')
                    ->findAll(),
                $connect,
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0,:test_query_id_in1)",
                {
                    "test_query_id_in0": [
                        2
                    ],
                    "test_query_id_in1": [
                        50
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
                    ->where('id', 'in', [2, 50])
                    ->findAll(),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereNotIn 查询条件',
    ])]
    public function testWhereNotIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT IN (:test_query_id_in0,:test_query_id_in1)",
                {
                    "test_query_id_in0": [
                        2
                    ],
                    "test_query_id_in1": [
                        50
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
                    ->whereNotIn('id', [2, 50])
                    ->findAll(),
                $connect
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT IN (:test_query_id_in0,:test_query_id_in1)",
                {
                    "test_query_id_in0": [
                        "1"
                    ],
                    "test_query_id_in1": [
                        "10"
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
                    ->where('id', 'not in', '1,10')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereNull 查询条件',
    ])]
    public function testWhereNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NULL",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereNull('id')
                    ->findAll(),
                $connect
            )
        );

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'null')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereNotNull 查询条件',
    ])]
    public function testWhereNotNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NOT NULL",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereNotNull('id')
                    ->findAll(),
                $connect
            )
        );

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'not null')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件未指定值默认为 null',
    ])]
    public function testWhereDefaultNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NULL",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件指定值为 null',
    ])]
    public function testWhereEqualNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IS NULL",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', '=', null)
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereLike 查询条件',
    ])]
    public function testWhereLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` LIKE :test_query_id",
                {
                    "test_query_id": [
                        "5"
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
                    ->whereLike('id', '5')
                    ->findAll(),
                $connect
            )
        );

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'like', '5')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereNotLike 查询条件',
    ])]
    public function testWhereNotLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` NOT LIKE :test_query_id",
                {
                    "test_query_id": [
                        "5"
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
                    ->whereNotLike('id', '5')
                    ->findAll(),
                $connect
            )
        );

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'not like', '5')
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereExists 查询条件',
    ])]
    public function testWhereExists(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_exists_test_query_subsql_id)",
                {
                    "test_query_exists_test_query_subsql_id": [
                        1
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
                    ->whereExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 1);
                        }
                    )
                    ->findAll(),
                $connect
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql`)",
                [],
                false
            ]
            eot;

        $subSelect = $connect->table('test_query_subsql');

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where([':exists' => $subSelect])
                    ->findAll(),
                $connect,
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (select *from test_query_subsql)",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where([':exists' => 'select *from test_query_subsql'])
                    ->findAll(),
                $connect,
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_exists_test_query_subsql_id)",
                {
                    "test_query_exists_test_query_subsql_id": [
                        1
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
                    ->where(
                        [
                            ':exists' => function ($select): void {
                                $select
                                    ->table('test_query_subsql')
                                    ->where('id', 1)
                                ;
                            },
                        ]
                    )
                    ->findAll(),
                $connect,
                3
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereNotExists 查询条件',
    ])]
    public function testWhereNotExists(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE NOT EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_notexists_test_query_subsql_id)",
                {
                    "test_query_notexists_test_query_subsql_id": [
                        1
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
                    ->whereNotExists(
                        function ($select): void {
                            $select
                                ->table('test_query_subsql')
                                ->where('id', 1)
                            ;
                        }
                    )
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持分组',
    ])]
    public function testWhereGroup(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :sub1_test_query_id OR (`test_query`.`votes` > :test_query_votes AND `test_query`.`title` <> :test_query_title)",
                {
                    "test_query_votes": [
                        100
                    ],
                    "test_query_title": [
                        "Admin"
                    ],
                    "sub1_test_query_id": [
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
                    ->where('id', 5)
                    ->orWhere(function ($select): void {
                        $select
                            ->where('votes', '>', 100)
                            ->where('title', '<>', 'Admin')
                        ;
                    })
                    ->findAll(),
                $connect
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :sub1_test_query_id OR `test_query`.`name` = :sub1_test_query_name AND (`test_query`.`votes` > :test_query_votes OR `test_query`.`title` <> :test_query_title)",
                {
                    "test_query_votes": [
                        100
                    ],
                    "test_query_title": [
                        "Admin"
                    ],
                    "sub1_test_query_id": [
                        5
                    ],
                    "sub1_test_query_name": [
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
                    ->where('id', 5)
                    ->orWhere('name', '小牛')
                    ->where(function ($select): void {
                        $select
                            ->where('votes', '>', 100)
                            ->orWhere('title', '<>', 'Admin')
                        ;
                    })
                    ->findAll(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持表达式',
    ])]
    public function testConditionalExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`post`,`test_query`.`value`,concat(\"tt_\",`test_query`.`id`) FROM `test_query` WHERE concat(\"hello_\",`test_query`.`posts`) = `test_query`.`id`",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query', 'post,value,'.Condition::raw('concat("tt_",[id])'))
                    ->where(Condition::raw('concat("hello_",[posts])'), '=', Condition::raw('[id]'))
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持二维数组的键值为字段',
    ])]
    public function testArrayKeyAsField(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :test_query_id AND `test_query`.`name` IN (:test_query_name_in0,:test_query_name_in1,:test_query_name_in2) AND `test_query`.`weidao` BETWEEN :test_query_weidao_between0 AND :test_query_weidao_between1 AND `test_query`.`value` IS NULL AND `test_query`.`remark` IS NOT NULL AND `test_query`.`goods` = :test_query_goods AND `test_query`.`hello` = :test_query_hello",
                {
                    "test_query_id": [
                        "故事"
                    ],
                    "test_query_name_in0": [
                        1
                    ],
                    "test_query_name_in1": [
                        2
                    ],
                    "test_query_name_in2": [
                        3
                    ],
                    "test_query_weidao_between0": [
                        "40"
                    ],
                    "test_query_weidao_between1": [
                        "100"
                    ],
                    "test_query_goods": [
                        "东亚商品"
                    ],
                    "test_query_hello": [
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
                    ->where([
                        'id' => ['=', '故事'],
                        'name' => ['in', [1, 2, 3]],
                        'weidao' => ['between', '40,100'],
                        'value' => 'null',
                        'remark' => ['not null'],
                        'goods' => '东亚商品',
                        'hello' => ['world'],
                    ])
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持字符串语法 `:string`',
    ])]
    public function testSupportString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` = 11 and `test_query`.`value` = 22 and concat(\"tt_\",`test_query`.`id`)",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where([':string' => Condition::raw('[name] = 11 and [test_query.value] = 22 and concat("tt_",[id])')])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testSupportStringMustBeString(): void
    {
        $this->expectException(\TypeError::class);

        $connect = $this->createDatabaseConnectMock();
        $connect
            ->table('test_query')
            ->where([':string' => []])
            ->findAll()
        ;
    }

    public function testSupportStringMustBeString2(): void
    {
        $this->expectException(\TypeError::class);

        $connect = $this->createDatabaseConnectMock();
        $connect
            ->table('test_query')
            ->where([':string' => true])
            ->findAll()
        ;
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持分组语法 `:subor` 和 `suband`',
    ])]
    public function testSupportSubandSubor(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`hello` = :sub1_test_query_hello OR (`test_query`.`id` LIKE :test_query_subor_test_query_id)",
                {
                    "test_query_subor_test_query_id": [
                        "你好"
                    ],
                    "sub1_test_query_hello": [
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
                    ->where([
                        'hello' => 'world',
                        ':subor' => ['id', 'like', '你好'],
                    ])
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持分组语法 `:subor` 和 `suband` 任意嵌套',
    ])]
    public function testSupportSubandSuborMore(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`hello` = :sub2_test_query_hello OR (`test_query`.`id` LIKE :test_query_subor_test_query_id AND `test_query`.`value` = :test_query_subor_test_query_value) AND (`test_query`.`id2` LIKE :sub2_sub1_test_query_suband_test_query_id2 OR `test_query`.`value2` = :sub2_sub1_test_query_suband_test_query_value2 OR (`test_query`.`child_one` > :sub1_sub1_test_query_subor_test_query_child_one AND `test_query`.`child_two` LIKE :sub1_sub1_test_query_subor_test_query_child_two))",
    {
        "sub1_sub1_test_query_subor_test_query_child_one": [
            "123"
        ],
        "sub1_sub1_test_query_subor_test_query_child_two": [
            "123"
        ],
        "sub2_sub1_test_query_suband_test_query_id2": [
            "你好2"
        ],
        "sub2_sub1_test_query_suband_test_query_value2": [
            "helloworld2"
        ],
        "test_query_subor_test_query_id": [
            "你好"
        ],
        "test_query_subor_test_query_value": [
            "helloworld"
        ],
        "sub2_test_query_hello": [
            "111"
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
                    ->where([
                        'hello' => '111',
                        ':subor' => [
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
                    ->findAll(),
                $connect,
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
            ->findAll()
        ;
    }

    public function testCallWhereSugarFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` LIKE :test_query_id",
                {
                    "test_query_id": [
                        "6"
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
                    ->whereLike('id', '5')
                    ->else()
                    ->whereLike('id', '6')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testCallWhereSugarFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` LIKE :test_query_id",
                {
                    "test_query_id": [
                        "5"
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
                    ->whereLike('id', '5')
                    ->else()
                    ->whereLike('id', '6')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testOrWhereFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`value` <> :test_query_value",
                {
                    "test_query_value": [
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
                    ->if($condition)
                    ->orWhere('value', '<>', 'foo')
                    ->else()
                    ->orWhere('value', '<>', 'bar')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testOrWhereFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`value` <> :test_query_value",
                {
                    "test_query_value": [
                        "foo"
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
                    ->orWhere('value', '<>', 'foo')
                    ->else()
                    ->orWhere('value', '<>', 'bar')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereExistsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_exists_test_query_subsql_id)",
                {
                    "test_query_exists_test_query_subsql_id": [
                        2
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
                    ->whereExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereExistsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_exists_test_query_subsql_id)",
                {
                    "test_query_exists_test_query_subsql_id": [
                        2
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
                    ->whereExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereNotExistsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE NOT EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_notexists_test_query_subsql_id)",
                {
                    "test_query_notexists_test_query_subsql_id": [
                        2
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
                    ->whereNotExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereNotExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereNotExistsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE EXISTS (SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_exists_test_query_subsql_id)",
                {
                    "test_query_exists_test_query_subsql_id": [
                        2
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
                    ->whereExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->else()
                    ->whereExists(
                        function ($select): void {
                            $select->table('test_query_subsql')->where('id', 2);
                        }
                    )
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件字段可以指定表',
        'zh-CN:description' => <<<'EOT'
字段条件用法和 table 中的字段用法一致，详情可以查看《查询语言.table》。
EOT,
    ])]
    public function testWhereFieldWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` = :test_query_name",
                {
                    "test_query_name": [
                        1
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
                    ->where('test_query.name', '=', 1)
                    ->findAll(),
                $connect
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
            ->findAll()
        ;
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
            ->findAll()
        ;
    }

    public function testWhereBetweenArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_test_query_subsql_id) AND :test_query_id_between1",
                {
                    "test_query_id_test_query_subsql_id": [
                        1
                    ],
                    "test_query_id_between1": [
                        100
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
                    ->whereBetween('id', [function ($select): void {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1)
                        ;
                    }, 100])
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'where 查询条件支持复杂的子查询',
    ])]
    public function testWhereInArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_test_query_subsql_id),:test_query_id_in1)",
                {
                    "test_query_id_test_query_subsql_id": [
                        1
                    ],
                    "test_query_id_in1": [
                        100
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
                    ->whereIn('id', [function ($select): void {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1)
                        ;
                    }, 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereBetweenArrayItemIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT 1) AND :test_query_id_between1",
                {
                    "test_query_id_between1": [
                        100
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
                    ->whereBetween('id', [Condition::raw('(SELECT 1)'), 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereBetweenArrayItemIsFakeExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :test_query_id_between0 AND :test_query_id_between1",
                {
                    "test_query_id_between0": [
                        "(SELECT 1)"
                    ],
                    "test_query_id_between1": [
                        100
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
                    ->whereBetween('id', ['(SELECT 1)', 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInArrayItemIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT 1),:test_query_id_in1)",
                {
                    "test_query_id_in1": [
                        100
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
                    ->whereIn('id', [Condition::raw('(SELECT 1)'), 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInArrayItemIsFakeExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0,:test_query_id_in1)",
                {
                    "test_query_id_in0": [
                        "(SELECT 1)"
                    ],
                    "test_query_id_in1": [
                        100
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
                    ->whereIn('id', ['(SELECT 1)', 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereBetweenArrayItemIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1) AND :test_query_id_between1",
                {
                    "test_query_id_between1": [
                        100
                    ]
                },
                false
            ]
            eot;

        $select = $connect
            ->table('test_query_subsql', 'id')
            ->one()
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereBetween('id', [$select, 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInArrayItemIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1),:test_query_id_in1)",
                {
                    "test_query_id_in1": [
                        100
                    ]
                },
                false
            ]
            eot;

        $select = $connect
            ->table('test_query_subsql', 'id')
            ->one()
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereIn('id', [$select, 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereBetweenArrayItemIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1) AND :test_query_id_between1",
                {
                    "test_query_id_between1": [
                        100
                    ]
                },
                false
            ]
            eot;

        $condition = $connect
            ->table('test_query_subsql', 'id')
            ->one()
            ->databaseCondition()
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereBetween('id', [$condition, 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInArrayItemIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1),:test_query_id_in1)",
                {
                    "test_query_id_in1": [
                        100
                    ]
                },
                false
            ]
            eot;

        $condition = $connect
            ->table('test_query_subsql', 'id')
            ->one()
            ->databaseCondition()
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereIn('id', [$condition, 100])
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereBetweenValueIsSelectString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` BETWEEN :test_query_id_between0 AND :test_query_id_between1",
                {
                    "test_query_id_between0": [
                        "SELECT"
                    ],
                    "test_query_id_between1": [
                        100
                    ]
                },
                false
            ]
            eot;

        $condition = $connect
            ->table('test_query_subsql', 'id')
            ->one()
            ->databaseCondition()
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereBetween('id', ['SELECT', 100])
                    ->findAll(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereIn('id', function ($select): void {
                        $select->table('test_query_subsql', 'id');
                    })
                    ->findAll(),
                $connect
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
                false
            ]
            eot;

        $subSql = $connect
            ->table('test_query_subsql', 'id')
            ->makeSql(true)
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', Condition::raw($subSql))
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInIsSubFakeString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
                {
                    "test_query_id_in0": [
                        "(SELECT `test_query_subsql`.`id` FROM `test_query_subsql`)"
                    ]
                },
                false
            ]
            eot;

        $subSql = $connect
            ->table('test_query_subsql', 'id')
            ->makeSql(true)
        ;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', $subSql)
                    ->findAll(),
                $connect
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
                false
            ]
            eot;

        $subSql = $connect->table('test_query_subsql', 'id');

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', $subSql)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInIsSubIsSelectManyTimes(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_test_query_subsql_id) AND `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_1_test_query_subsql_id) AND `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_2_test_query_subsql_id) AND `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_3_test_query_subsql_id) AND `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_4_test_query_subsql_id)",
                {
                    "test_query_id_4_test_query_subsql_id": [
                        2
                    ],
                    "test_query_id_3_test_query_subsql_id": [
                        2
                    ],
                    "test_query_id_2_test_query_subsql_id": [
                        2
                    ],
                    "test_query_id_1_test_query_subsql_id": [
                        2
                    ],
                    "test_query_id_test_query_subsql_id": [
                        2
                    ]
                },
                false
            ]
            eot;

        $subSql = $connect->table('test_query_subsql', 'id')->where('id', 2);

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', $subSql)
                    ->where('id', 'in', $subSql)
                    ->where('id', 'in', $subSql)
                    ->where('id', 'in', $subSql)
                    ->where('id', 'in', $subSql)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInIsSubIsSelectManyTimes2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_test_query_subsql_id) AND `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_1_test_query_subsql_id) AND `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_2_test_query_subsql_id) AND `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_3_test_query_subsql_id)",
                {
                    "test_query_id_3_test_query_subsql_id": [
                        3
                    ],
                    "test_query_id_2_test_query_subsql_id": [
                        3
                    ],
                    "test_query_id_1_test_query_subsql_id": [
                        2
                    ],
                    "test_query_id_test_query_subsql_id": [
                        2
                    ]
                },
                false
            ]
            eot;

        $subSql = $connect->table('test_query_subsql', 'id')->where('id', 2);
        $subSql2 = $connect->table('test_query_subsql', 'id')->where('id', 3);

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', $subSql)
                    ->where('id', 'in', $subSql)
                    ->where('id', 'in', $subSql2)
                    ->where('id', 'in', $subSql2)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotArray(): void
    {
        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
    {
        "test_query_id_in0": [
            ""
        ]
    },
    false
]
eot;

        $connect = $this->createDatabaseConnectMock();

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', '')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotArray2(): void
    {
        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
    {
        "test_query_id_in0": [
            0
        ]
    },
    false
]
eot;

        $connect = $this->createDatabaseConnectMock();
        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', 0)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotArray3(): void
    {
        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
    {
        "test_query_id_in0": [
            "0"
        ]
    },
    false
]
eot;
        $connect = $this->createDatabaseConnectMock();
        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', '0')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotArray33(): void
    {
        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
    {
        "test_query_id_in0": [
            false
        ]
    },
    false
]
eot;
        $connect = $this->createDatabaseConnectMock();
        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', false)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotArray44(): void
    {
        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
    {
        "test_query_id_in0": [
            true
        ]
    },
    false
]
eot;
        $connect = $this->createDatabaseConnectMock();
        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->where('id', 'in', true)
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotArray4(): void
    {
        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
    {
        "test_query_id_in0": [
            "0"
        ]
    },
    false
]
eot;

        $connect = $this->createDatabaseConnectMock();
        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereIn('id', '0')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotArray5(): void
    {
        $sql = <<<'eot'
[
    "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` IN (:test_query_id_in0)",
    {
        "test_query_id_in0": [
            null
        ]
    },
    false
]
eot;

        $connect = $this->createDatabaseConnectMock();
        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereIn('id')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereInNotEmptyArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] in param value must not be an empty array.'
        );

        $connect = $this->createDatabaseConnectMock();
        $connect
            ->table('test_query')
            ->whereIn('id', [])
            ->findAll()
        ;
    }

    public function testWhereInNotEmptyArray2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] in param value must not be an empty array.'
        );

        $connect = $this->createDatabaseConnectMock();
        $connect
            ->table('test_query')
            ->where('id', 'in', [])
            ->findAll()
        ;
    }

    public function testWhereEqualIsSub(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = :test_query_id_test_query_subsql_id)",
                {
                    "test_query_id_test_query_subsql_id": [
                        1
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
                    ->where('id', '=', function ($select): void {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1)
                        ;
                    })
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'whereRaw 查询条件',
    ])]
    public function testWhereRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, id)",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereRaw('FIND_IN_SET(1, id)')
                    ->findAll(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'orWhereRaw 查询条件',
    ])]
    public function testOrWhereRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE FIND_IN_SET(1, id) OR FIND_IN_SET(1, id)",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->whereRaw('FIND_IN_SET(1, id)')
                    ->orWhereRaw('FIND_IN_SET(1, id)')
                    ->findAll(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->fi()
                    ->findAll(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->fi()
                    ->findAll(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->orWhereRaw('FIND_IN_SET(1, options_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->orWhereRaw('FIND_IN_SET(1, goods_id)')
                    ->fi()
                    ->findAll(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->whereRaw('FIND_IN_SET(1, goods_id)')
                    ->orWhereRaw('FIND_IN_SET(1, options_id)')
                    ->else()
                    ->whereRaw('FIND_IN_SET(1, options_id)')
                    ->orWhereRaw('FIND_IN_SET(1, goods_id)')
                    ->fi()
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereSpecialDatabaseColumn(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`中国加油` = :test_query AND `test_query`.`中国加油` = :test_query_1 AND `test_query`.`战伊` = :test_query_2 AND `test_query`.`战伊` = :test_query_3 AND `test_query`.`战伊` = :test_query_4 AND `test_query`.`a-b_c@!!defg` = :test_query_a_b_c_defg",
                {
                    "test_query": [
                        "2020"
                    ],
                    "test_query_1": [
                        "2030"
                    ],
                    "test_query_2": [
                        "高级"
                    ],
                    "test_query_3": [
                        "优秀"
                    ],
                    "test_query_4": [
                        "人才"
                    ],
                    "test_query_a_b_c_defg": [
                        "不规则字段"
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
                    ->where('中国加油', '2020')
                    ->where('中国加油', '2030')
                    ->where('战伊', '高级')
                    ->where('战伊', '优秀')
                    ->where('战伊', '人才')
                    ->where('a-b_c@!!defg', '不规则字段')
                    ->findAll(),
                $connect
            )
        );
    }

    public function testWhereGenerateBindParams(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`goods_id_1` = :test_query_goods_id_1 AND `test_query`.`goods_id` = :test_query_goods_id AND `test_query`.`goods_id` = :test_query_goods_id_1_1",
                {
                    "test_query_goods_id_1": [
                        11
                    ],
                    "test_query_goods_id": [
                        11
                    ],
                    "test_query_goods_id_1_1": [
                        11
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
                    ->where('goods_id_1', 11)
                    ->where('goods_id', 11)
                    ->where('goods_id', 11)
                    ->findAll(),
                $connect
            )
        );
    }
}
