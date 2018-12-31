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

namespace Tests\Database\Update;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * updateColumn test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.24
 *
 * @version 1.0
 */
class UpdateColumnTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "UPDATE `test` SET `test`.`name` = :name WHERE `test`.`id` = 503",
    {
        "name": [
            "小小小鸟，怎么也飞不高。",
            2
        ]
    }
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                updateColumn('name', '小小小鸟，怎么也飞不高。')
            )
        );
    }

    public function testExpression()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "UPDATE `test` SET `test`.`name` = concat(`test`.`value`,`test`.`name`) WHERE `test`.`id` = 503",
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                updateColumn('name', '{concat([value],[name])}')
            )
        );
    }
}
