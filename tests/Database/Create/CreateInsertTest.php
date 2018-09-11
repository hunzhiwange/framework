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

namespace Tests\Database\Create;

use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * create insert test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.23
 *
 * @version 1.0
 */
class CreateInsertTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子',
      1 => 2,
    ),
    'value' => 
    array (
      0 => '吃饭饭',
      1 => 2,
    ),
  ),
)
eot;

        $data = ['name' => '小鸭子', 'value' => '吃饭饭'];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                insert($data)
            )
        );
    }

    public function testBind()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:questionmark_0)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子',
      1 => 2,
    ),
    'questionmark_0' => 
    array (
      0 => '吃肉',
      1 => 2,
    ),
  ),
)
eot;

        $data = ['name' => '小鸭子', 'value' => '[?]'];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                insert($data, ['吃肉'])
            )
        );

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子',
      1 => 2,
    ),
    'value' => '呱呱呱',
  ),
)
eot;

        $data = ['name' => '小鸭子', 'value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                insert($data, ['value' => '呱呱呱'])
            )
        );
    }

    public function testWithBindFunction()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:questionmark_0)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子',
      1 => 2,
    ),
    'questionmark_0' => 
    array (
      0 => '吃鱼',
      1 => 2,
    ),
  ),
)
eot;

        $data = ['name' => '小鸭子', 'value' => '[?]'];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                bind(['吃鱼'])->

                insert($data)
            )
        );
    }

    public function testReplace()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'REPLACE INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子',
      1 => 2,
    ),
    'value' => '呱呱呱',
  ),
)
eot;

        $data = ['name' => '小鸭子', 'value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                insert($data, ['value' => '呱呱呱'], true)
            )
        );
    }
}
