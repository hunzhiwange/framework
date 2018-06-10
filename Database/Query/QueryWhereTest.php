<?php declare(strict_types=1);
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
 * where test
 * 
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.06.10
 * @version 1.0
 */
class QueryWhereTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        // 字段 （表达式） 值
        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = 1',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', '=', 1)->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = 2',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 2)->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = 2 AND `test`.`name` > \'狗蛋\' AND `test`.`value` LIKE \'小鸭子\'',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 2)->

                where('name', '>', '狗蛋')->

                where('value', 'like', '小鸭子')->

                getAll(true),
                __METHOD__
            )
        );
    }

    public function testArray()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`name` LIKE \'技术\'',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where(['name','like', '技术'])->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`name` LIKE \'技术\' AND `test`.`value` <> \'结局\'',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where ([
                  ['name','like', '技术'],
                  ['value','<>', '结局']
                ])->

                getAll(true),
                __METHOD__
            )
        );
    }

    public function testOrWhere()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`name` LIKE \'技术\' OR `test`.`value` <> \'结局\'',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('name','like', '技术')->

                orWhere('value','<>', '结局')->

                getAll(true),
                __METHOD__
            )
        );
    }

    public function testWhereBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN 1 AND 100',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                whereBetween('id', [1, 100])->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN 1 AND 10',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id','between', [1, 10])->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN 1 AND 100 AND `test`.`name` BETWEEN 5 AND 22',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                whereBetween([
                  ['id', [1, 100]],
                  ['name', [5, 22]]
                ])->

                getAll(true),
                __METHOD__
            )
        );
    }

    public function testWhereNotBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` NOT BETWEEN 1 AND 10',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                whereNotBetween('id', [1, 10])->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` NOT BETWEEN 1 AND 10',
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

        $this->assertEquals(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id','not between', [1, 10])->

                getAll(true),
                __METHOD__
            )
        );
    }
}
