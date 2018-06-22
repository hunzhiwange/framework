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
 * having test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 * @coversNothing
 */
class QueryHavingTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        // 字段 （表达式） 值
        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`tid` HAVING `test`.`tid` > 5',
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

                groupBy('tid')->

                having('tid', '>', 5)->

                getAll(true)
            )
        );
    }

    public function testArray()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` LIKE \'技术\'',
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having(['name', 'like', '技术'])->

                getAll(true)
            )
        );
    }

    public function testOrHaving()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` LIKE \'技术\' OR `test`.`tname` LIKE \'技术\'',
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having(['name', 'like', '技术'])->

                orHaving(['tname', 'like', '技术'])->

                getAll(true)
            )
        );
    }

    public function testHavingBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` BETWEEN 1 AND 10 AND `test`.`id` BETWEEN 1 AND 100',
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having('id', 'between', [1, 10])->

                havingBetween('id', [1, 100])->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` BETWEEN 1 AND 100 AND `test`.`tname` BETWEEN 5 AND 22',
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                havingBetween([
                    ['name', [1, 100]],
                    ['tname', [5, 22]],
                ])->

                getAll(true)
            )
        );
    }

    public function testHavingNotBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` NOT BETWEEN 1 AND 10 AND `test`.`id` NOT BETWEEN 1 AND 100',
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

                groupBy('name')->

                having('id', 'not between', [1, 10])->

                havingNotBetween('id', [1, 100])->

                getAll(true)
            )
        );
    }

    public function testHavingIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IN (2,50) AND `test`.`num` IN (2,50)',
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

                groupBy('name')->

                having('id', 'in', [2, 50])->

                havingIn('num', [2, 50])->

                getAll(true)
            )
        );
    }

    public function testHavingNotIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` NOT IN (2,50) AND `test`.`num` NOT IN (2,50)',
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

                groupBy('name')->

                having('id', 'not in', [2, 50])->

                havingNotIn('num', [2, 50])->

                getAll(true)
            )
        );
    }

    public function testHavingNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IS NULL AND `test`.`num` IS NULL',
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

                groupBy('name')->

                having('id', 'null')->

                havingNull('num')->

                getAll(true)
            )
        );
    }

    public function testHavingNotNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IS NOT NULL AND `test`.`num` IS NOT NULL',
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

                groupBy('name')->

                having('id', 'not null')->

                havingNotNull('num')->

                getAll(true)
            )
        );
    }

    public function testHavingLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` LIKE \'123\' AND `test`.`num` LIKE \'55\'',
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

                groupBy('name')->

                having('id', 'like', '123')->

                havingLike('num', '55')->

                getAll(true)
            )
        );
    }

    public function testHavingNotLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` NOT LIKE \'123\' AND `test`.`num` NOT LIKE \'55\'',
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

                groupBy('name')->

                having('id', 'not like', '123')->

                havingNotLike('num', '55')->

                getAll(true)
            )
        );
    }

    public function testHavingGroup()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`id` = 5 OR (`test`.`votes` > 100 AND `test`.`title` <> \'Admin\')',
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

                groupBy('id')->

                having('id', 5)->

                orHaving(function ($select) {
                    $select->having('votes', '>', 100)->having('title', '<>', 'Admin');
                })->

                getAll(true)
            )
        );
    }

    public function testConditionalExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.`post`,`test`.`value`,concat("tt_",`test`.`id`) FROM `test` GROUP BY `test`.`id` HAVING concat("hello_",`test`.`posts`) = `test`.`id`',
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
                $connect->table('test', 'post,value,{concat("tt_",[id])}')->

                groupBy('id')->

                having('{concat("hello_",[posts])}', '=', '{[id]}')->

                getAll(true)
            )
        );
    }

    public function testArrayKeyAsField()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`id` = \'故事\' AND `test`.`name` IN (1,2,3) AND `test`.`weidao` BETWEEN \'40\' AND \'100\' AND `test`.`value` IS NULL AND `test`.`remark` IS NOT NULL AND `test`.`goods` = \'东亚商品\' AND `test`.`hello` = \'world\'',
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

                groupBy('id')->

                having([
                    'id'     => ['=', '故事'],
                    'name'   => ['in', [1, 2, 3]],
                    'weidao' => ['between', '40,100'],
                    'value'  => 'null',
                    'remark' => ['not null'],
                    'goods'  => '东亚商品',
                    'hello'  => ['world'],
                ])->

                getAll(true)
            )
        );
    }

    public function testSupportString()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`name` = 11 and `post`.`value` = 22 and concat("tt_",`test`.`id`)',
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

                groupBy('id')->

                having(
                    ['string__' => '{[name] = 11 and [post.value] = 22 and concat("tt_",[id])}']
                )->

                getAll(true)
            )
        );
    }

    public function testSupportSubandSubor()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`hello` = \'world\' OR (`test`.`id` LIKE \'你好\')',
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

                groupBy('id')->

                having(
                    [
                        'hello'   => 'world',
                        'subor__' => ['id', 'like', '你好'],
                    ]
                )->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`hello` = \'111\' OR (`test`.`id` LIKE \'你好\' AND `test`.`value` = \'helloworld\') AND (`test`.`id` LIKE \'你好\' OR `test`.`value` = \'helloworld\' OR (`test`.`child_one` > \'123\' AND `test`.`child_two` LIKE \'123\'))',
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

                groupBy('id')->

                having(
                    [
                        'hello'   => '111',
                        'subor__' => [
                            ['id', 'like', '你好'],
                            ['value', '=', 'helloworld'],
                        ],
                        'suband__' => [
                            'logic__' => 'or',
                            ['id', 'like', '你好'],
                            ['value', '=', 'helloworld'],
                            'subor__' => [
                                ['child_one', '>', '123'],
                                ['child_two', 'like', '123'],
                            ],
                        ],
                    ]
                )->

                getAll(true)
            )
        );
    }
}
