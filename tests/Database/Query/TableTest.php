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

use stdClass;
use Tests\TestCase;

/**
 * table test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
 * @version 1.0
 */
class TableTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `posts`.* FROM `posts`",
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
                $connect->table('posts')->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `posts`.* FROM `mydb`.`posts`",
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
                $connect->table('mydb.posts')->

                findAll(true),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT `p`.* FROM `mydb`.`posts` `p`",
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
                $connect->table(['p' => 'mydb.posts'])->

                findAll(true),
                2
            )
        );
    }

    public function testField()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `posts`.`title`,`posts`.`body` FROM `posts`",
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
                $connect->table('posts', 'title,body')->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `posts`.`title` AS `t`,`posts`.`name`,`posts`.`remark`,`posts`.`value` FROM `mydb`.`posts`",
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
                $connect->table(
                    'mydb.posts', [
                        't' => 'title', 'name', 'remark,value',
                    ])->

                findAll(true),
                1
            )
        );
    }

    public function testTableFlow()
    {
        $condition = false;

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
            $this->varJson(
                $connect->

                ifs($condition)->

                table('test')->

                elses()->

                table('foo')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testTableFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

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
                $connect->

                ifs($condition)->

                table('test')->

                elses()->

                table('foo')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testTableIsInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Invalid table name.'
        );

        $connect = $this->createConnect();

        $connect->table(new stdClass())->

        findAll(true);
    }
}
