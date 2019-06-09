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
 * reset test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.20
 *
 * @version 1.0
 */
class ResetTest extends TestCase
{
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `newtable`.* FROM `newtable` WHERE `newtable`.`new` = 'world'",
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
                $connect
                    ->table('test')
                    ->where('id', '=', 5)
                    ->where('name', 'like', 'me')
                    ->reset()
                    ->table('newtable')
                    ->where('new', '=', 'world')
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test` WHERE `test`.`new` LIKE 'new'",
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
                $connect
                    ->table('test')
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
                "SELECT `test`.`name`,`test`.`id` FROM `test` WHERE `test`.`id` = 5 AND `test`.`name` LIKE 'me' AND `test`.`foo` LIKE 'bar'",
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
                $connect
                    ->table('test')
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
                "SELECT `foo`.* FROM `foo`",
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
                $connect
                    ->table('test')
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
}
