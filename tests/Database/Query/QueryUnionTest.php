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
 * union test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 */
class QueryUnionTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` 
UNION SELECT `yyyyy`.`yid` AS `id`,`yyyyy`.`name` AS `value` FROM `yyyyy` WHERE `yyyyy`.`first_name` = \'222\'
UNION SELECT id,value FROM test2
UNION SELECT `yyyyy`.`yid` AS `id`,`yyyyy`.`name` AS `value` FROM `yyyyy` WHERE `yyyyy`.`first_name` = \'222\'',
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

        $union1 = $connect->table('yyyyy', 'yid as id,name as value')->where('first_name', '=', '222');
        $union2 = 'SELECT id,value FROM test2';

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test', 'tid as id,tname as value')->

                union($union1)->

                union($union2)->

                union($union1)->

                findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test', 'tid as id,tname as value')->

                union([$union1, $union2, $union1])->

                findAll(true)
            )
        );
    }

    public function testUnionAll()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` 
UNION ALL SELECT id,value FROM test2',
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

        $union1 = 'SELECT id,value FROM test2';

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test', 'tid as id,tname as value')->

                unionAll($union1)->

                findAll(true)
            )
        );
    }

    public function testUnionFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION SELECT id,value FROM test3",
    [],
    false,
    null,
    null,
    []
]
eot;

        $union1 = 'SELECT id,value FROM test2';

        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJsonEncode(
                $connect->table('test', 'tid as id,tname as value')->

                ifs($condition)->

                union($union1)->

                elses()->

                union($union2)->

                endIfs()->

                findAll(true),
                __FUNCTION__
            )
        );
    }

    public function testUnionFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION SELECT id,value FROM test2",
    [],
    false,
    null,
    null,
    []
]
eot;

        $union1 = 'SELECT id,value FROM test2';

        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJsonEncode(
                $connect->table('test', 'tid as id,tname as value')->

                ifs($condition)->

                union($union1)->

                elses()->

                union($union2)->

                endIfs()->

                findAll(true),
                __FUNCTION__
            )
        );
    }

    public function testUnionAllFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION ALL SELECT id,value FROM test3",
    [],
    false,
    null,
    null,
    []
]
eot;

        $union1 = 'SELECT id,value FROM test2';

        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJsonEncode(
                $connect->table('test', 'tid as id,tname as value')->

                ifs($condition)->

                unionAll($union1)->

                elses()->

                unionAll($union2)->

                endIfs()->

                findAll(true),
                __FUNCTION__
            )
        );
    }

    public function testUnionAllFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION ALL SELECT id,value FROM test2",
    [],
    false,
    null,
    null,
    []
]
eot;

        $union1 = 'SELECT id,value FROM test2';

        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJsonEncode(
                $connect->table('test', 'tid as id,tname as value')->

                ifs($condition)->

                unionAll($union1)->

                elses()->

                unionAll($union2)->

                endIfs()->

                findAll(true),
                __FUNCTION__
            )
        );
    }

    public function testUnionNotSupportType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid UNION type `NOT FOUND`.'
        );

        $connect = $this->createConnect();

        $union1 = 'SELECT id,value FROM test2';

        $connect->table('test', 'tid as id,tname as value')->

        union($union1, 'NOT FOUND')->

        findAll(true);
    }
}
