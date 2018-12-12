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

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * lists test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.23
 *
 * @version 1.0
 */
class ListTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.`name` FROM `test`",
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
                $connect->sql()->

                table('test')->

                list('name')
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.`name`,`test`.`id` FROM `test`",
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
                $connect->sql()->

                table('test')->

                list('name,id'),
                1
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                list('name', 'id'),
                2
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                list(['name', 'id']),
                3
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                list(['name'], 'id'),
                4
            )
        );
    }
}
