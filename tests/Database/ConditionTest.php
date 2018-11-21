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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * condition test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.29
 *
 * @version 1.0
 */
class ConditionTest extends TestCase
{
    public function testForPage()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` LIMIT 114,6",
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

                forPage(20, 6)->

                findAll(true)
            )
        );
    }

    public function testParseFormNotSet()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT 2",
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
                $connect->

                setColumns('{2}')->

                findAll(true)
            )
        );
    }

    public function testMakeSql()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test`"
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                [
                    $connect->table('test')->

                    makeSql(),
                ]
            )
        );
    }

    public function testMakeSqlWithLogicGroup()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "(SELECT `test`.* FROM `test`)"
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                [
                    $connect->table('test')->

                    makeSql(true),
                ]
            )
        );
    }
}
