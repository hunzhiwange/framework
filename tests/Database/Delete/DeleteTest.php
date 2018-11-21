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

namespace Tests\Database\Delete;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * delete test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.25
 *
 * @version 1.0
 */
class DeleteTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "DELETE FROM `test` WHERE `test`.`id` = 1 ORDER BY `test`.`id` DESC LIMIT 1",
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                where('id', 1)->

                limit(1)->

                orderBy('id desc')->

                delete()
            )
        );
    }

    public function testJoin()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "DELETE t FROM `test` `t` INNER JOIN `hello` `h` ON `h`.`name` = `t`.`content` WHERE `t`.`id` = 1",
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test as t')->

                innerJoin(['h' => 'hello'], [], 'name', '=', '{[t.content]}')->

                where('id', 1)->

                limit(1)->

                orderBy('id desc')->

                delete()
            )
        );
    }
}
