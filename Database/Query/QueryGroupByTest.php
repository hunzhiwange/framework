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
 * groupBy test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 * @coversNothing
 */
class QueryGroupByTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`id`,`test`.`name`',
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

                groupBy('id')->

                groupBy('name')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `post`.`id`',
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

                groupBy('post.id')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY SUM(`test`.`num`)',
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

                groupBy('{SUM([num])}')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`title`,`test`.`id`,concat(\'1234\',`test`.`id`,\'ttt\')',
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

                groupBy("title,id,{concat('1234',[id],'ttt')}")->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`title`,`test`.`id`,`test`.`ttt`,`test`.`value`',
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

                groupBy(['title,id,ttt', 'value'])->

                getAll(true)
            )
        );
    }
}
