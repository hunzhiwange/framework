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

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * limit test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 */
class LimitTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 5,10",
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
                $connect->table('test')->

                limit(5, 10)->

                find(null, true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 1",
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
                $connect->table('test')->

                one()->

                find(null, true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`",
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
                $connect->table('test')->

                all()->

                find(null, true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 0,15",
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
                $connect->table('test')->

                top(15)->

                find(null, true),
                3
            )
        );
    }

    public function testTopFlow()
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 0,6",
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
                $connect->table('test')->

                ifs($condition)->

                top(5)->

                elses()->

                top(6)->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testTopFlow2()
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 0,5",
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
                $connect->table('test')->

                ifs($condition)->

                top(5)->

                elses()->

                top(6)->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testLimitFlow()
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 2,3",
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
                $connect->table('test')->

                ifs($condition)->

                limit(0, 5)->

                elses()->

                limit(2, 3)->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testLimitFlow2()
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 0,5",
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
                $connect->table('test')->

                ifs($condition)->

                limit(0, 5)->

                elses()->

                limit(2, 3)->

                endIfs()->

                findAll(true)
            )
        );
    }
}
