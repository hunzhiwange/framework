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

namespace Tests\Database\Delete;

use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * delete delete test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.25
 *
 * @version 1.0
 */
class DeleteDeleteTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'DELETE FROM `test` WHERE `test`.`id` = 1 ORDER BY `test`.`id` DESC LIMIT 1',
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

                where('id', 1)->

                limit(1)->

                orderBy('id desc')->

                delete()
            )
        );
    }

    public function testJoin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'DELETE t FROM `test` `t` INNER JOIN `hello` `h` ON `h`.`name` = \'小牛\' WHERE `t`.`id` = 1',
  1 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test as t')->

                innerJoin(['h' => 'hello'], [], 'name', '=', '小牛')->

                where('id', 1)->

                limit(1)->

                orderBy('id desc')->

                delete()
            )
        );
    }

    public function testUsing()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'DELETE FROM t1 USING `t2`,`t3`,`test` `t1` WHERE `t1`.`id` = `t2`.`id` AND `t2`.`id` = `t3`.`id`',
  1 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test as t1')->

                where('t1.id', '{[t2.id]}')->

                where('t2.id', '{[t3.id]}')->

                using('t2,t3')->

                limit(1)->

                orderBy('id desc')->

                delete()
            )
        );
    }
}
