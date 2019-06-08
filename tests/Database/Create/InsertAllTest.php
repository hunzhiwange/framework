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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
 */
class InsertAllTest extends TestCase
{
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:value_1),(:name_2,:value_2),(:name_3,:value_3)",
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
                    ->table('test')
                    ->insertAll($data)
            )
        );
    }

    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)",
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
                    ->table('test')
                    ->insertAll($data, ['吃肉1', '吃肉2'])
            )
        );

        $sql = <<<'eot'
            [
                "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:hello),(:name_2,:value_2),(:name_3,:world)",
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
                    ->table('test')
                    ->insertAll($data, ['hello' => 'hello 吃肉', 'world' => 'world 喝汤']),
                1
            )
        );
    }

    public function testWithBindFunction(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)",
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
                    ->table('test')
                    ->bind(['吃鱼', '吃肉'])
                    ->insertAll($data)
            )
        );
    }

    public function testReplace(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)",
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
                    ->table('test')
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
            ->table('test')
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
            ->table('test')
            ->insertAll($data);
    }
}
