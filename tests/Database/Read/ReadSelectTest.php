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

use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * read select test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.21
 *
 * @version 1.0
 */
class ReadSelectTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "select *from test where id = ?",
    [
        1
    ]
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                select('select *from test where id = ?', [1])
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
                $connect->sql()->

                table('test')->

                select(),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = 1",
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

                select(function ($select) {
                    $select->where('id', 1);
                }),
                2
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = 5",
    [],
    false,
    null,
    null,
    []
]
eot;

        $select = $connect->table('test')->

        where('id', 5);

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                select($select),
                3
            )
        );
    }
}
