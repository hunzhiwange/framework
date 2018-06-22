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
 * join test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.17
 *
 * @version 1.0
 * @coversNothing
 */
class QueryJoinTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = \'小牛\'',
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

                join('hello', 'name,value', 'name', '=', '小牛')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = \'小牛\'',
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

                join(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`hello` = \'world\' AND `hello`.`test` > `hello`.`name`',
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

                join('hello', 'name,value', ['hello' => 'world', ['test', '>', '{[name]}']])->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON (`hello`.`id` < 5 AND `hello`.`name` LIKE \'hello\')',
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

                join('hello', 'name,value', function ($select) {
                    $select->where('id', '<', 5)->where('name', 'like', 'hello');
                })->

                getAll(true)
            )
        );
    }

    public function testInnerJoin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = \'小牛\'',
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

                innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')->

                getAll(true)
            )
        );
    }

    public function testRightJoin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` RIGHT JOIN `hello` `t` ON `t`.`name` = \'小牛\'',
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

                rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')->

                getAll(true)
            )
        );
    }

    public function testFullJoin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` FULL JOIN `hello` `t` ON `t`.`name` = \'小牛\'',
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

                fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')->

                getAll(true)
            )
        );
    }

    public function testCrossJoin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` CROSS JOIN `hello` `t`',
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

                crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'])->

                getAll(true)
            )
        );
    }

    public function testNaturalJoin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` NATURAL JOIN `hello` `t`',
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

                naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'])->

                getAll(true)
            )
        );
    }
}
