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
 *     title="Query lang.reset",
 *     zh-CN:title="查询语言.reset",
 *     path="database/query/reset",
 *     description="",
 * )
 */
class ResetTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="重置所有",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query_subsql`.* FROM `test_query_subsql` WHERE `test_query_subsql`.`new` = :test_query_subsql_new",
                {
                    "test_query_subsql_new": [
                        "world",
                        2
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->reset()
                    ->table('test_query_subsql')
                    ->where('new', '=', 'world')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="重置某一项",
     *     description="",
     *     note="",
     * )
     */
    public function testResetItem(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name`,`test_query`.`id` FROM `test_query` WHERE `test_query`.`new` LIKE :test_query_new",
                {
                    "test_query_new": [
                        "new",
                        2
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->setColumns('name,id')
                    ->reset('where')
                    ->where('new', 'like', 'new')
                    ->findAll(true),
                1
            )
        );
    }

    public function testResetFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name`,`test_query`.`id` FROM `test_query` WHERE `test_query`.`id` = :test_query_id AND `test_query`.`name` LIKE :test_query_name AND `test_query`.`foo` LIKE :test_query_foo",
                {
                    "test_query_id": [
                        5,
                        1
                    ],
                    "test_query_name": [
                        "me",
                        2
                    ],
                    "test_query_foo": [
                        "bar",
                        2
                    ]
                },
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->setColumns('name,id')
                    ->if($condition)
                    ->reset()
                    ->table('foo')
                    ->else()
                    ->where('foo', 'like', 'bar')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testResetFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query_subsql`.* FROM `test_query_subsql`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->setColumns('name,id')
                    ->if($condition)
                    ->reset()
                    ->table('test_query_subsql')
                    ->else()
                    ->where('foo', 'like', 'bar')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
