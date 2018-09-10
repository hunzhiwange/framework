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
 * orderBy test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 */
class QueryOrderByTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`id` DESC,`test`.`name` ASC',
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
                $connect->table('test', 'tid as id,tname as value')->

                orderBy('id DESC')->

                orderBy('name')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`id` DESC',
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
                $connect->table('test', 'tid as id,tname as value')->

                orderBy('test.id DESC')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY SUM(`test`.`num`) ASC',
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
                $connect->table('test', 'tid as id,tname as value')->

                orderBy('{SUM([num]) ASC}')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY SUM(`test`.`num`) ASC',
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
                $connect->table('test', 'tid as id,tname as value')->

                orderBy('{SUM([num]) ASC}')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`title` ASC,`test`.`id` ASC,concat(\'1234\',`test`.`id`,\'ttt\') DESC',
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
                $connect->table('test', 'tid as id,tname as value')->

                orderBy("title,id,{concat('1234',[id],'ttt') desc}")->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`title` ASC,`test`.`id` ASC,`test`.`ttt` ASC,`test`.`value` DESC',
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
                $connect->table('test', 'tid as id,tname as value')->

                orderBy(['title,id,ttt', 'value desc'])->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`title` DESC,`test`.`id` DESC,`test`.`ttt` ASC,`test`.`value` DESC',
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
                $connect->table('test', 'tid as id,tname as value')->

                orderBy(['title,id,ttt asc', 'value'], 'desc')->

                getAll(true)
            )
        );
    }

    public function testLatestOrOldest()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` ORDER BY `test`.`create_at` DESC',
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

                latest()->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` ORDER BY `test`.`foo` DESC',
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

                latest('foo')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` ORDER BY `test`.`create_at` ASC',
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

                oldest()->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` ORDER BY `test`.`bar` ASC',
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

                oldest('bar')->

                getAll(true)
            )
        );
    }
}
