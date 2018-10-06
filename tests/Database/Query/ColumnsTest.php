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
 * columns test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.20
 *
 * @version 1.0
 */
class ColumnsTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.*,`test`.`id`,`test`.`name`,`test`.`value` FROM `test`",
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

                columns('id')->

                columns('name,value')->

                findAll(true)
            )
        );
    }

    public function testSetColumns()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`remark` FROM `test`",
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

                columns('id')->

                columns('name,value')->

                setColumns('remark')->

                findAll(true)
            )
        );
    }

    public function testColumnsExpressionForSelectString()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    [
        "SELECT 'foo'",
        [],
        false,
        null,
        null,
        []
    ]
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                [
                    $connect->

                    columns("{'foo'}")->

                    findAll(true),
                ]
            )
        );
    }

    public function testColumnsFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.*,`test`.`name`,`test`.`value` FROM `test`",
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

                columns('id')->

                elses()->

                columns('name,value')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testColumnsFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.*,`test`.`id` FROM `test`",
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

                columns('id')->

                elses()->

                columns('name,value')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testSetColumnsFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`name`,`test`.`value` FROM `test`",
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

                setColumns('foo')->

                ifs($condition)->

                setColumns('id')->

                elses()->

                setColumns('name,value')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testSetColumnsFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`id` FROM `test`",
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

                setColumns('foo')->

                ifs($condition)->

                setColumns('id')->

                elses()->

                setColumns('name,value')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testSetColumnsWithTableName()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`name`,`test`.`value`,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = `test`.`name`",
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

                setColumns('test.name,test.value')->

                join('hello', 'name,value', 'name', '=', '{[test.name]}')->

                findAll(true)
            )
        );
    }
}
