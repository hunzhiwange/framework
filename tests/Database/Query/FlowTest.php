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
 * flow test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.14
 *
 * @version 1.0
 */
class FlowTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 2 ORDER BY `test`.`name` DESC LIMIT 1",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $id = 2;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs(1 === $id)->where('id', 1)->

                elseIfs(2 === $id)->where('id', 2)->orderBy('name DESC')->

                elseIfs(3 === $id)->where('id', 3)->where('id', 1111)->

                elseIfs(4 === $id)->where('id', 4)->

                endIfs()->

                findOne(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 1 LIMIT 1",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $id = 1;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs(1 === $id)->where('id', 1)->

                elseIfs(2 === $id)->where('id', 2)->orderBy('name DESC')->

                elseIfs(3 === $id)->where('id', 3)->where('id', 1111)->

                elseIfs(4 === $id)->where('id', 4)->

                endIfs()->

                findOne(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 3 AND `test`.`id` = 1111 LIMIT 1",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $id = 3;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs(1 === $id)->where('id', 1)->

                elseIfs(2 === $id)->where('id', 2)->orderBy('name DESC')->

                elseIfs(3 === $id)->where('id', 3)->where('id', 1111)->

                elseIfs(4 === $id)->where('id', 4)->

                endIfs()->

                findOne(true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 4 LIMIT 1",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $id = 4;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs(1 === $id)->where('id', 1)->

                elseIfs(2 === $id)->where('id', 2)->orderBy('name DESC')->

                elseIfs(3 === $id)->where('id', 3)->where('id', 1111)->

                elseIfs(4 === $id)->where('id', 4)->

                endIfs()->

                findOne(true),
                3
            )
        );
    }

    public function testElses()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 2 AND `test`.`id` = 4 ORDER BY `test`.`name` DESC LIMIT 1",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $id = 2;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs(1 === $id)->where('id', 1)->

                elseIfs(2 === $id)->where('id', 2)->orderBy('name DESC')->

                elseIfs(3 === $id)->where('id', 3)->where('id', 1111)->

                // elses 仅仅能记忆上一次 ifs,elseIfs 的结果，上一次的反向结果就是 elses 的条件值
                // 其等价于 elseIfs($id != 3)
                elses()->where('id', 4)->

                endIfs()->

                findOne(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 3 AND `test`.`id` = 1111 LIMIT 1",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $id = 3;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs(1 === $id)->where('id', 1)->

                elseIfs(2 === $id)->where('id', 2)->orderBy('name DESC')->

                elseIfs(3 === $id)->where('id', 3)->where('id', 1111)->

                // elses 仅仅能记忆上一次 ifs,elseIfs 的结果，上一次的反向结果就是 elses 的条件值
                // 其等价于 elseIfs($id != 3)
                elses()->where('id', 4)->

                endIfs()->

                findOne(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 4 LIMIT 1",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $id = 5;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs(1 === $id)->where('id', 1)->

                elseIfs(2 === $id)->where('id', 2)->orderBy('name DESC')->

                elseIfs(3 === $id)->where('id', 3)->where('id', 1111)->

                // elses 仅仅能记忆上一次 ifs,elseIfs 的结果，上一次的反向结果就是 elses 的条件值
                // 其等价于 elseIfs($id != 3)
                elses()->where('id', 4)->

                endIfs()->

                findOne(true),
                2
            )
        );
    }
}
