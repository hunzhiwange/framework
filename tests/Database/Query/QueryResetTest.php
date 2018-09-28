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

use Tests\TestCase;

/**
 * reset test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.20
 *
 * @version 1.0
 */
class QueryResetTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `newtable`.* FROM `newtable` WHERE `newtable`.`new` = \'world\'',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', '=', 5)->

                where('name', 'like', 'me')->

                reset()->

                table('newtable')->

                where('new', '=', 'world')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`name`,`test`.`id` FROM `test` WHERE `test`.`new` LIKE \'new\'',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', '=', 5)->

                where('name', 'like', 'me')->

                setColumns('name,id')->

                reset('where')->

                where('new', 'like', 'new')->

                getAll(true)
            )
        );
    }

    public function testResetFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

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
            $this->varJsonEncode(
                $connect->table('test')->

                where('id', '=', 5)->

                where('name', 'like', 'me')->

                setColumns('name,id')->

                ifs($condition)->

                reset()->

                table('foo')->

                elses()->

                where('foo', 'like', 'bar')->

                endIfs()->

                getAll(true),
                __FUNCTION__
            )
        );
    }

    public function testResetFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

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
            $this->varJsonEncode(
                $connect->table('test')->

                where('id', '=', 5)->

                where('name', 'like', 'me')->

                setColumns('name,id')->

                ifs($condition)->

                reset()->

                table('foo')->

                elses()->

                where('foo', 'like', 'bar')->

                endIfs()->

                getAll(true),
                __FUNCTION__
            )
        );
    }
}
