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

namespace Tests\Database\Create;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * insertall test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.24
 *
 * @version 1.0
 *
 * @api(
 *     zh-CN:title="批量写入数据.insertAll",
 *     path="database/create/insertall",
 *     description="",
 * )
 */
class InsertAllTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="insertAll 基本用法",
     *     zh-CN:description="写入成功后，返回 `lastInsertId`。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value),(:name_1,:value_1),(:name_2,:value_2),(:name_3,:value_3)",
                {
                    "name": [
                        "小鸭子1",
                        2
                    ],
                    "value": [
                        "呱呱呱1",
                        2
                    ],
                    "name_1": [
                        "小鸭子2",
                        2
                    ],
                    "value_1": [
                        "呱呱呱2",
                        2
                    ],
                    "name_2": [
                        "小鸭子3",
                        2
                    ],
                    "value_2": [
                        "呱呱呱3",
                        2
                    ],
                    "name_3": [
                        "小鸭子4",
                        2
                    ],
                    "value_3": [
                        "呱呱呱4",
                        2
                    ]
                }
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '呱呱呱2'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '呱呱呱4'],
        ];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insertAll($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insertAll 绑定参数",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)",
                {
                    "name": [
                        "小鸭子1",
                        2
                    ],
                    "value": [
                        "呱呱呱1",
                        2
                    ],
                    "name_1": [
                        "小鸭子2",
                        2
                    ],
                    "questionmark_0_1": [
                        "吃肉1",
                        2
                    ],
                    "name_2": [
                        "小鸭子3",
                        2
                    ],
                    "value_2": [
                        "呱呱呱3",
                        2
                    ],
                    "name_3": [
                        "小鸭子4",
                        2
                    ],
                    "questionmark_1_3": [
                        "吃肉2",
                        2
                    ]
                }
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[?]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[?]'],
        ];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insertAll($data, ['吃肉1', '吃肉2'])
            )
        );

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value),(:name_1,:hello),(:name_2,:value_2),(:name_3,:world)",
                {
                    "name": [
                        "小鸭子1",
                        2
                    ],
                    "value": [
                        "呱呱呱1",
                        2
                    ],
                    "name_1": [
                        "小鸭子2",
                        2
                    ],
                    "name_2": [
                        "小鸭子3",
                        2
                    ],
                    "value_2": [
                        "呱呱呱3",
                        2
                    ],
                    "name_3": [
                        "小鸭子4",
                        2
                    ],
                    "hello": "hello 吃肉",
                    "world": "world 喝汤"
                }
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[:hello]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[:world]'],
        ];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
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
     *     note="",
     * )
     */
    public function testWithBindFunction(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)",
                {
                    "name": [
                        "小鸭子1",
                        2
                    ],
                    "value": [
                        "呱呱呱1",
                        2
                    ],
                    "name_1": [
                        "小鸭子2",
                        2
                    ],
                    "questionmark_0_1": [
                        "吃鱼",
                        2
                    ],
                    "name_2": [
                        "小鸭子3",
                        2
                    ],
                    "value_2": [
                        "呱呱呱3",
                        2
                    ],
                    "name_3": [
                        "小鸭子4",
                        2
                    ],
                    "questionmark_1_3": [
                        "吃肉",
                        2
                    ]
                }
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[?]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[?]'],
        ];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
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
     *     note="",
     * )
     */
    public function testReplace(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)",
                {
                    "name": [
                        "小鸭子1",
                        2
                    ],
                    "value": [
                        "呱呱呱1",
                        2
                    ],
                    "name_1": [
                        "小鸭子2",
                        2
                    ],
                    "questionmark_0_1": [
                        "吃鱼",
                        2
                    ],
                    "name_2": [
                        "小鸭子3",
                        2
                    ],
                    "value_2": [
                        "呱呱呱3",
                        2
                    ],
                    "name_3": [
                        "小鸭子4",
                        2
                    ],
                    "questionmark_1_3": [
                        "吃肉",
                        2
                    ]
                }
            ]
            eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[?]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[?]'],
        ];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
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
            ->sql()
            ->table('test_query')
            ->insertAll($data);
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
            ->sql()
            ->table('test_query')
            ->insertAll($data);
    }

    /**
     * @api(
     *     zh-CN:title="insertAll 空数据批量写入示例",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInsertWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` () VALUES (),(),(),()",
                []
            ]
            eot;

        $data = [
            [],
            [],
            [],
            [],
        ];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insertAll($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insertAll.replace 空数据写入示例",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testReplaceWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` () VALUES (),(),(),()",
                []
            ]
            eot;

        $data = [
            [],
            [],
            [],
            [],
        ];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
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
