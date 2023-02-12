<?php

declare(strict_types=1);

namespace Tests\Database\Create;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="批量写入数据.insertAll",
 *     path="database/create/insertall",
 *     zh-CN:description="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class InsertAllTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="insertAll 基本用法",
     *     zh-CN:description="写入成功后，返回 `lastInsertId`。",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdonamedparameter_value),(:pdonamedparameter_name_1,:pdonamedparameter_value_1),(:pdonamedparameter_name_2,:pdonamedparameter_value_2),(:pdonamedparameter_name_3,:pdonamedparameter_value_3)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子1"
                    ],
                    "pdonamedparameter_value": [
                        "呱呱呱1"
                    ],
                    "pdonamedparameter_name_1": [
                        "小鸭子2"
                    ],
                    "pdonamedparameter_value_1": [
                        "呱呱呱2"
                    ],
                    "pdonamedparameter_name_2": [
                        "小鸭子3"
                    ],
                    "pdonamedparameter_value_2": [
                        "呱呱呱3"
                    ],
                    "pdonamedparameter_name_3": [
                        "小鸭子4"
                    ],
                    "pdonamedparameter_value_3": [
                        "呱呱呱4"
                    ]
                },
                false
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '呱呱呱2'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '呱呱呱4'],
        ];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test_query')
                    ->insertAll($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insertAll 绑定参数",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdonamedparameter_value),(:pdonamedparameter_name_1,:pdopositional2namedparameter_0_1),(:pdonamedparameter_name_2,:pdonamedparameter_value_2),(:pdonamedparameter_name_3,:pdopositional2namedparameter_1_3)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子1"
                    ],
                    "pdonamedparameter_value": [
                        "呱呱呱1"
                    ],
                    "pdonamedparameter_name_1": [
                        "小鸭子2"
                    ],
                    "pdopositional2namedparameter_0_1": [
                        "吃肉1"
                    ],
                    "pdonamedparameter_name_2": [
                        "小鸭子3"
                    ],
                    "pdonamedparameter_value_2": [
                        "呱呱呱3"
                    ],
                    "pdonamedparameter_name_3": [
                        "小鸭子4"
                    ],
                    "pdopositional2namedparameter_1_3": [
                        "吃肉2"
                    ]
                },
                false
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => Condition::raw('?')],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => Condition::raw('?')],
        ];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test_query')
                    ->insertAll($data, ['吃肉1', '吃肉2'])
            )
        );

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdonamedparameter_value),(:pdonamedparameter_name_1,:hello),(:pdonamedparameter_name_2,:pdonamedparameter_value_2),(:pdonamedparameter_name_3,:world)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子1"
                    ],
                    "pdonamedparameter_value": [
                        "呱呱呱1"
                    ],
                    "pdonamedparameter_name_1": [
                        "小鸭子2"
                    ],
                    "pdonamedparameter_name_2": [
                        "小鸭子3"
                    ],
                    "pdonamedparameter_value_2": [
                        "呱呱呱3"
                    ],
                    "pdonamedparameter_name_3": [
                        "小鸭子4"
                    ],
                    "hello": "hello 吃肉",
                    "world": "world 喝汤"
                },
                false
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => Condition::raw(':hello')],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => Condition::raw(':world')],
        ];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test_query')
                    ->insertAll($data, ['hello' => 'hello 吃肉', 'world' => 'world 喝汤']),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="bind.insertAll 绑定参数批量写入数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testWithBindFunction(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdonamedparameter_value),(:pdonamedparameter_name_1,:pdopositional2namedparameter_0_1),(:pdonamedparameter_name_2,:pdonamedparameter_value_2),(:pdonamedparameter_name_3,:pdopositional2namedparameter_1_3)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子1"
                    ],
                    "pdonamedparameter_value": [
                        "呱呱呱1"
                    ],
                    "pdonamedparameter_name_1": [
                        "小鸭子2"
                    ],
                    "pdopositional2namedparameter_0_1": [
                        "吃鱼"
                    ],
                    "pdonamedparameter_name_2": [
                        "小鸭子3"
                    ],
                    "pdonamedparameter_value_2": [
                        "呱呱呱3"
                    ],
                    "pdonamedparameter_name_3": [
                        "小鸭子4"
                    ],
                    "pdopositional2namedparameter_1_3": [
                        "吃肉"
                    ]
                },
                false
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => Condition::raw('?')],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => Condition::raw('?')],
        ];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test_query')
                    ->bind(['吃鱼', '吃肉'])
                    ->insertAll($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insertAll 支持 replace 用法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testReplace(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:pdonamedparameter_name,:pdonamedparameter_value),(:pdonamedparameter_name_1,:pdopositional2namedparameter_0_1),(:pdonamedparameter_name_2,:pdonamedparameter_value_2),(:pdonamedparameter_name_3,:pdopositional2namedparameter_1_3)",
                {
                    "pdonamedparameter_name": [
                        "小鸭子1"
                    ],
                    "pdonamedparameter_value": [
                        "呱呱呱1"
                    ],
                    "pdonamedparameter_name_1": [
                        "小鸭子2"
                    ],
                    "pdopositional2namedparameter_0_1": [
                        "吃鱼"
                    ],
                    "pdonamedparameter_name_2": [
                        "小鸭子3"
                    ],
                    "pdonamedparameter_value_2": [
                        "呱呱呱3"
                    ],
                    "pdonamedparameter_name_3": [
                        "小鸭子4"
                    ],
                    "pdopositional2namedparameter_1_3": [
                        "吃肉"
                    ]
                },
                false
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => Condition::raw('?')],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => Condition::raw('?')],
        ];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test_query')
                    ->bind(['吃鱼', '吃肉'])
                    ->insertAll($data, [], true)
            )
        );
    }

    public function testDataIsNotInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Data for insertAll is not invalid.'
        );

        $connect = $this->createDatabaseConnectMock();

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            5,
        ];

        $connect

            ->table('test_query')
            ->insertAll($data)
        ;
    }

    public function testDataIsNotInvalid2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Data for insertAll is not invalid.'
        );

        $connect = $this->createDatabaseConnectMock();

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['foo' => ['hello', 'world']],
        ];

        $connect

            ->table('test_query')
            ->insertAll($data)
        ;
    }

    /**
     * @api(
     *     zh-CN:title="insertAll 空数据批量写入示例",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testInsertWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` () VALUES (),(),(),()",
                [],
                false
            ]
            eot;

        $data = [
            [],
            [],
            [],
            [],
        ];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test_query')
                    ->insertAll($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insertAll.replace 空数据写入示例",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testReplaceWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` () VALUES (),(),(),()",
                [],
                false
            ]
            eot;

        $data = [
            [],
            [],
            [],
            [],
        ];

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect

                    ->table('test_query')
                    ->insertAll($data, [], true)
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['test_query'];
    }
}
