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

namespace Tests\Database\Read;

use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * read getdynamics test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.22
 *
 * @version 1.0
 * @coversNothing
 */
class ReadGetDynamicsTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` LIMIT 3,10',
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
                $connect->sql()->

                table('test')->

                get10start3()
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`user_name` = \'1111\' LIMIT 1',
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
                $connect->sql()->

                table('test')->

                getByUserName('1111')
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`UserName` = \'1111\' LIMIT 1',
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
                $connect->sql()->

                table('test')->

                getByUserName_('1111')
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`user_name` = \'1111\' AND `test`.`sex` = \'222\'',
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
                $connect->sql()->

                table('test')->

                getAllByUserNameAndSex('1111', '222')
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`UserName` = \'1111\' AND `test`.`Sex` = \'222\'',
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
                $connect->sql()->

                table('test')->

                getAllByUserNameAndSex_('1111', '222')
            )
        );
    }
}
