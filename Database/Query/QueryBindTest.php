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

use PDO;
use Tests\TestCase;

/**
 * bind test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.17
 *
 * @version 1.0
 * @coversNothing
 */
class QueryBindTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = :id',
  1 => 
  array (
    'id' => 
    array (
      0 => 1,
      1 => 2,
    ),
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

                bind('id', 1)->

                where('id', '=', '[:id]')->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = :id',
  1 => 
  array (
    'id' => 
    array (
      0 => 1,
      1 => 1,
    ),
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

                bind('id', 1, PDO::PARAM_INT)->

                where('id', '=', '[:id]')->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = :id',
  1 => 
  array (
    'id' => 
    array (
      0 => 1,
      1 => 1,
    ),
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

                bind('id', [1, PDO::PARAM_INT])->

                where('id', '=', '[:id]')->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = :id AND `test`.`hello` LIKE :name',
  1 => 
  array (
    'id' => 
    array (
      0 => 1,
      1 => 1,
    ),
    'name' => 
    array (
      0 => '小鸭子',
      1 => 2,
    ),
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

                bind(['id' => [1, PDO::PARAM_INT], 'name'=>'小鸭子'])->

                where('id', '=', '[:id]')->

                where('hello', 'like', '[:name]')->

                getAll(true),
                __METHOD__
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = ? AND `test`.`hello` LIKE ?',
  1 => 
  array (
    0 => 
    array (
      0 => 5,
      1 => 1,
    ),
    1 => 
    array (
      0 => '小鸭子',
      1 => 2,
    ),
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

                bind([[5, PDO::PARAM_INT], '小鸭子'])->

                where('id', '=', '[?]')->

                where('hello', 'like', '[?]')->

                getAll(true),
                __METHOD__
            )
        );
    }
}
