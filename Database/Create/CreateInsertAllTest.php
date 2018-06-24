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
 * create insertall test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.24
 *
 * @version 1.0
 */
class CreateInsertAllTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:value_1),(:name_2,:value_2),(:name_3,:value_3)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子1',
      1 => 2,
    ),
    'value' => 
    array (
      0 => '呱呱呱1',
      1 => 2,
    ),
    'name_1' => 
    array (
      0 => '小鸭子2',
      1 => 2,
    ),
    'value_1' => 
    array (
      0 => '呱呱呱2',
      1 => 2,
    ),
    'name_2' => 
    array (
      0 => '小鸭子3',
      1 => 2,
    ),
    'value_2' => 
    array (
      0 => '呱呱呱3',
      1 => 2,
    ),
    'name_3' => 
    array (
      0 => '小鸭子4',
      1 => 2,
    ),
    'value_3' => 
    array (
      0 => '呱呱呱4',
      1 => 2,
    ),
  ),
)
eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '呱呱呱2'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '呱呱呱4'],
        ];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                insertAll($data)
            )
        );
    }

    public function testBind()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子1',
      1 => 2,
    ),
    'value' => 
    array (
      0 => '呱呱呱1',
      1 => 2,
    ),
    'name_1' => 
    array (
      0 => '小鸭子2',
      1 => 2,
    ),
    'questionmark_0_1' => 
    array (
      0 => '吃肉1',
      1 => 2,
    ),
    'name_2' => 
    array (
      0 => '小鸭子3',
      1 => 2,
    ),
    'value_2' => 
    array (
      0 => '呱呱呱3',
      1 => 2,
    ),
    'name_3' => 
    array (
      0 => '小鸭子4',
      1 => 2,
    ),
    'questionmark_1_3' => 
    array (
      0 => '吃肉2',
      1 => 2,
    ),
  ),
)
eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[?]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[?]'],
        ];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                insertAll($data, ['吃肉1', '吃肉2'])
            )
        );

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:hello),(:name_2,:value_2),(:name_3,:world)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子1',
      1 => 2,
    ),
    'value' => 
    array (
      0 => '呱呱呱1',
      1 => 2,
    ),
    'name_1' => 
    array (
      0 => '小鸭子2',
      1 => 2,
    ),
    'name_2' => 
    array (
      0 => '小鸭子3',
      1 => 2,
    ),
    'value_2' => 
    array (
      0 => '呱呱呱3',
      1 => 2,
    ),
    'name_3' => 
    array (
      0 => '小鸭子4',
      1 => 2,
    ),
    'hello' => 'hello 吃肉',
    'world' => 'world 喝汤',
  ),
)
eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[:hello]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[:world]'],
        ];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                insertAll($data, ['hello' => 'hello 吃肉', 'world' => 'world 喝汤'])
            )
        );
    }

    public function testWithBindFunction()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子1',
      1 => 2,
    ),
    'value' => 
    array (
      0 => '呱呱呱1',
      1 => 2,
    ),
    'name_1' => 
    array (
      0 => '小鸭子2',
      1 => 2,
    ),
    'questionmark_0_1' => 
    array (
      0 => '吃鱼',
      1 => 2,
    ),
    'name_2' => 
    array (
      0 => '小鸭子3',
      1 => 2,
    ),
    'value_2' => 
    array (
      0 => '呱呱呱3',
      1 => 2,
    ),
    'name_3' => 
    array (
      0 => '小鸭子4',
      1 => 2,
    ),
    'questionmark_1_3' => 
    array (
      0 => '吃肉',
      1 => 2,
    ),
  ),
)
eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[?]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[?]'],
        ];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                bind(['吃鱼', '吃肉'])->

                insertAll($data)
            )
        );
    }

    public function testReplace()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'REPLACE INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value),(:name_1,:questionmark_0_1),(:name_2,:value_2),(:name_3,:questionmark_1_3)',
  1 => 
  array (
    'name' => 
    array (
      0 => '小鸭子1',
      1 => 2,
    ),
    'value' => 
    array (
      0 => '呱呱呱1',
      1 => 2,
    ),
    'name_1' => 
    array (
      0 => '小鸭子2',
      1 => 2,
    ),
    'questionmark_0_1' => 
    array (
      0 => '吃鱼',
      1 => 2,
    ),
    'name_2' => 
    array (
      0 => '小鸭子3',
      1 => 2,
    ),
    'value_2' => 
    array (
      0 => '呱呱呱3',
      1 => 2,
    ),
    'name_3' => 
    array (
      0 => '小鸭子4',
      1 => 2,
    ),
    'questionmark_1_3' => 
    array (
      0 => '吃肉',
      1 => 2,
    ),
  ),
)
eot;

        $data = [
            ['name' => '小鸭子1', 'value' => '呱呱呱1'],
            ['name' => '小鸭子2', 'value' => '[?]'],
            ['name' => '小鸭子3', 'value' => '呱呱呱3'],
            ['name' => '小鸭子4', 'value' => '[?]'],
        ];

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                bind(['吃鱼', '吃肉'])->

                insertAll($data, [], true)
            )
        );
    }
}
