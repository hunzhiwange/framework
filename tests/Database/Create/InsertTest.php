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
 * @api(
 *     zh-CN:title="插入单条数据.insert",
 *     path="database/create/insert",
 *     description="",
 * )
 */
class InsertTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="insert 基本用法",
     *     zh-CN:description="写入成功后，返回 `lastInsertId`。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value)",
                {
                    "name": [
                        "小鸭子",
                        2
                    ],
                    "value": [
                        "吃饭饭",
                        2
                    ]
                }
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => '吃饭饭'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 绑定参数",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:questionmark_0)",
                {
                    "name": [
                        "小鸭子",
                        2
                    ],
                    "questionmark_0": [
                        "吃肉",
                        2
                    ]
                }
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => '[?]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['吃肉'])
            )
        );

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value)",
                {
                    "name": [
                        "小鸭子",
                        2
                    ],
                    "value": "呱呱呱"
                }
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['value' => '呱呱呱']),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="bind.insert 绑定参数写入数据",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithBindFunction(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:questionmark_0)",
                {
                    "name": [
                        "小鸭子",
                        2
                    ],
                    "questionmark_0": [
                        "吃鱼",
                        2
                    ]
                }
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => '[?]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->bind(['吃鱼'])
                    ->insert($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 支持 replace 用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testReplace(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value)",
                {
                    "name": [
                        "小鸭子",
                        2
                    ],
                    "value": "呱呱呱"
                }
            ]
            eot;

        $data = ['name' => '小鸭子', 'value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['value' => '呱呱呱'], true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 支持字段指定表名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInsertSupportTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` (`test_query`.`name`,`test_query`.`value`) VALUES (:name,:value)",
                {
                    "name": [
                        "小鸭子",
                        2
                    ],
                    "value": "呱呱呱"
                }
            ]
            eot;

        $data = ['name' => '小鸭子', 'test_query.value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, ['value' => '呱呱呱'], true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert 空数据写入示例",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInsertWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "INSERT INTO `test_query` () VALUES ()",
                []
            ]
            eot;

        $data = [];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="insert.replace 空数据写入示例",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testReplaceWithEmptyData(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "REPLACE INTO `test_query` () VALUES ()",
                []
            ]
            eot;

        $data = [];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test_query')
                    ->insert($data, [], true)
            )
        );
    }

    protected function getDatabaseTable(): array
    {
        return ['test_query'];
    }
}
