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

namespace Tests\Database\Update;

use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * update update test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.24
 *
 * @version 1.0
 */
class UpdateUpdateTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'UPDATE `test` SET `test`.`name` = :name WHERE `test`.`id` = 503',
  1 => 
  array (
    'name' => 
    array (
      0 => '小猪',
      1 => 2,
    ),
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                update(['name' => '小猪'])
            )
        );
    }

    public function testForUpdate()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'UPDATE `test` SET `test`.`name` = :name WHERE `test`.`id` = 503 FOR UPDATE',
  1 => 
  array (
    'name' => 
    array (
      0 => '小猪',
      1 => 2,
    ),
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                forUpdate()->

                update(['name' => '小猪'])
            )
        );
    }

    public function testWithLimit()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'UPDATE `test` SET `test`.`name` = :name WHERE `test`.`id` = 503 LIMIT 0,2',
  1 => 
  array (
    'name' => 
    array (
      0 => '小猪',
      1 => 2,
    ),
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                top(2)->

                update(['name' => '小猪'])
            )
        );
    }

    public function testWithOrderBy()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'UPDATE `test` SET `test`.`name` = :name WHERE `test`.`id` = 503 ORDER BY `test`.`id` DESC',
  1 => 
  array (
    'name' => 
    array (
      0 => '小猪',
      1 => 2,
    ),
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                orderBy('id desc')->

                update(['name' => '小猪'])
            )
        );
    }

    public function testWithJoin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'UPDATE `test` `t` INNER JOIN `hello` `h` ON `t`.`id` = `h`.`size` SET `t`.`name` = :name WHERE `t`.`id` = 503',
  1 => 
  array (
    'name' => 
    array (
      0 => '小猪',
      1 => 2,
    ),
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test as t')->

                join('hello as h', '', 't.id', '=', '{[size]}')->

                where('id', 503)->

                update(['name' => '小猪'])
            )
        );
    }

    public function testBind()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'UPDATE `test` SET `test`.`name` = :hello,`test`.`value` = :questionmark_0 WHERE `test`.`id` = 503',
  1 => 
  array (
    'questionmark_0' => 
    array (
      0 => '小牛逼',
      1 => 2,
    ),
    'hello' => 'hello world!',
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                bind(['小牛逼'])->

                update(
                    [
                        'name'  => '[:hello]',
                        'value' => '[?]',
                    ],
                    [
                        'hello' => 'hello world!',
                    ]
                )
            )
        );
    }

    public function testExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'UPDATE `test` SET `test`.`name` = concat(`test`.`value`,`test`.`name`) WHERE `test`.`id` = 503',
  1 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                where('id', 503)->

                update([
                    'name' => '{concat([value],[name])}',
                ])
            )
        );
    }
}
