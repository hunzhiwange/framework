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

namespace Tests\Database\Query;

use PDO;
use Tests\TestCase;

/**
 * bind test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.17
 *
 * @version 1.0
 */
class QueryBindTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = :id",
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
                $connect->table('test')->

                bind('id', 1)->

                where('id', '=', '[:id]')->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = :id",
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
                $connect->table('test')->

                bind('id', 1, PDO::PARAM_INT)->

                where('id', '=', '[:id]')->

                findAll(true),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = :id",
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
                $connect->table('test')->

                bind('id', [1, PDO::PARAM_INT])->

                where('id', '=', '[:id]')->

                findAll(true),
                2
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = :id AND `test`.`hello` LIKE :name",
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
                $connect->table('test')->

                bind(['id' => [1, PDO::PARAM_INT], 'name'=>'小鸭子'])->

                where('id', '=', '[:id]')->

                where('hello', 'like', '[:name]')->

                findAll(true),
                3
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = ? AND `test`.`hello` LIKE ?",
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
                $connect->table('test')->

                bind([[5, PDO::PARAM_INT], '小鸭子'])->

                where('id', '=', '[?]')->

                where('hello', 'like', '[?]')->

                findAll(true),
                4
            )
        );
    }
}
