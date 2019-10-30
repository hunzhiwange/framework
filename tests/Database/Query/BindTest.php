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

namespace Tests\Database\Query;

use PDO;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * bind test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.17
 *
 * @version 1.0
 *
 * @api(
 *     title="查询语言.bind",
 *     path="database/query/bind",
 *     description="",
 * )
 */
class BindTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="命名参数绑定",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
                        1,
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
                    ->bind('id', 1)
                    ->where('id', '=', '[:id]')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="命名参数绑定，支持绑定类型",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBindWithType(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
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
                    ->bind('id', 1, PDO::PARAM_INT)
                    ->where('id', '=', '[:id]')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="命名参数绑定，绑定值支持类型定义",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithTypeAndValueCanBeArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
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
                    ->bind('id', [1, PDO::PARAM_INT])
                    ->where('id', '=', '[:id]')
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="命名参数绑定，支持多个字段绑定",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testNameBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id AND `test_query`.`hello` LIKE :name",
                {
                    "id": [
                        1,
                        1
                    ],
                    "name": [
                        "小鸭子",
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
                    ->bind(['id' => [1, PDO::PARAM_INT], 'name'=>'小鸭子'])
                    ->where('id', '=', '[:id]')
                    ->where('hello', 'like', '[:name]')
                    ->findAll(true),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="问号 `?` 参数绑定，支持多个字段绑定",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testQuestionMarkBind(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = ? AND `test_query`.`hello` LIKE ?",
                [
                    [
                        5,
                        1
                    ],
                    [
                        "小鸭子",
                        2
                    ]
                ],
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
                    ->bind([[5, PDO::PARAM_INT], '小鸭子'])
                    ->where('id', '=', '[?]')
                    ->where('hello', 'like', '[?]')
                    ->findAll(true),
                4
            )
        );
    }

    public function testBindFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`name` = :name",
                {
                    "name": [
                        1,
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
                    ->bind('id', 1)
                    ->where('id', '=', '[:id]')
                    ->else()
                    ->bind('name', 1)
                    ->where('name', '=', '[:name]')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testBindFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`id` = :id",
                {
                    "id": [
                        1,
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
                    ->bind('id', 1)
                    ->where('id', '=', '[:id]')
                    ->else()
                    ->bind('name', 1)
                    ->where('name', '=', '[:name]')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
