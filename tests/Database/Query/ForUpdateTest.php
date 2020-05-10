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
 *     title="Query lang.forUpdate",
 *     zh-CN:title="查询语言.forUpdate",
 *     path="database/query/forupdate",
 *     description="",
 * )
 */
class ForUpdateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="数据库悲观锁",
     *     zh-CN:description="对数据库悲观锁的支持。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FOR UPDATE",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->forUpdate()
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="取消数据库悲观锁",
     *     description="",
     *     note="",
     * )
     */
    public function testCancelUpdate(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->forUpdate()
                    ->forUpdate(false)
                    ->findAll(true),
                1
            )
        );
    }

    public function testForUpdateFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query`",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->forUpdate()
                    ->else()
                    ->forUpdate(false)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testForUpdateFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FOR UPDATE",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->forUpdate()
                    ->else()
                    ->forUpdate(false)
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
