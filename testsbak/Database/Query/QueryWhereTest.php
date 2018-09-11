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
 * where test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.10
 *
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', '=', 1)->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 2)->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 2)->

                where('name', '>', '狗蛋')->

                where('value', 'like', '小鸭子')->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where(['name', 'like', '技术'])->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where([
                    ['name', 'like', '技术'],
                    ['value', '<>', '结局'],
                ])->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('name', 'like', '技术')->

                orWhere('value', '<>', '结局')->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                whereBetween('id', [1, 100])->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 'between', [1, 10])->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                whereBetween([
                    ['id', [1, 100]],
                    ['name', [5, 22]],
                ])->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                whereNotBetween('id', [1, 10])->

                getAll(true)
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

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 'not between', [1, 10])->

                getAll(true)
            )
        );
    }

    public function testWhereIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` IN (2,50)',
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

                whereIn('id', [2, 50])->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` IN (\'1\',\'10\')',
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

                where('id', 'in', '1,10')->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` IN (2,50)',
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

                where('id', 'in', [2, 50])->

                getAll(true)
            )
        );
    }

    public function testWhereNotIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` NOT IN (2,50)',
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

                whereNotIn('id', [2, 50])->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` NOT IN (\'1\',\'10\')',
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

                where('id', 'not in', '1,10')->

                getAll(true)
            )
        );
    }

    public function testWhereNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` IS NULL',
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

                whereNull('id')->

                getAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 'null')->

                getAll(true)
            )
        );
    }

    public function testWhereNotNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` IS NOT NULL',
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

                whereNotNull('id')->

                getAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 'not null')->

                getAll(true)
            )
        );
    }

    public function testWhereLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` LIKE \'5\'',
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

                whereLike('id', '5')->

                getAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 'like', '5')->

                getAll(true)
            )
        );
    }

    public function testWhereNotLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` NOT LIKE \'5\'',
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

                whereNotLike('id', '5')->

                getAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where('id', 'not like', '5')->

                getAll(true)
            )
        );
    }

    public function testWhereExists()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `subsql`.* FROM `subsql` WHERE `subsql`.`id` = 1)',
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

                whereExists(
                    function ($select) {
                        $select->table('subsql')->where('id', 1);
                    }
                )->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `subsql`.* FROM `subsql`)',
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

        $subSelect = $connect->table('subsql');

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where(
                   [
                       'exists__' => $subSelect,
                   ]
                )->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE EXISTS (select *from d_sub)',
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

        $subSelect = $connect->table('subsql');

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where(
                   [
                       'exists__' => 'select *from d_sub',
                   ]
                )->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `subsql`.* FROM `subsql` WHERE `subsql`.`id` = 1)',
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

        $subSelect = $connect->table('subsql');

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->table('test')->

                where(
                   [
                       'exists__' => function ($select) {
                           $select->table('subsql')->where('id', 1);
                       },
                   ]
                )->

                getAll(true)
            )
        );
    }

    public function testWhereNotExists()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE NOT EXISTS (SELECT `subsql`.* FROM `subsql` WHERE `subsql`.`id` = 1)',
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

                whereNotExists(
                    function ($select) {
                        $select->table('subsql')->where('id', 1);
                    }
                )->

                getAll(true)
            )
        );
    }

    public function testWhereGroup()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = 5 OR (`test`.`votes` > 100 AND `test`.`title` <> \'Admin\')',
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

                where('id', 5)->

                orWhere(function ($select) {
                    $select->where('votes', '>', 100)->where('title', '<>', 'Admin');
                })->

                getAll(true)
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = 5 OR `test`.`name` = \'小牛\' AND (`test`.`votes` > 100 OR `test`.`title` <> \'Admin\')',
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

                where('id', 5)->

                orWhere('name', '小牛')->

                where(function ($select) {
                    $select->where('votes', '>', 100)->orWhere('title', '<>', 'Admin');
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
  0 => 'SELECT `test`.`post`,`test`.`value`,concat("tt_",`test`.`id`) FROM `test` WHERE concat("hello_",`test`.`posts`) = `test`.`id`',
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

                where('{concat("hello_",[posts])}', '=', '{[id]}')->

                getAll(true)
            )
        );
    }

    public function testArrayKeyAsField()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`id` = \'故事\' AND `test`.`name` IN (1,2,3) AND `test`.`weidao` BETWEEN \'40\' AND \'100\' AND `test`.`value` IS NULL AND `test`.`remark` IS NOT NULL AND `test`.`goods` = \'东亚商品\' AND `test`.`hello` = \'world\'',
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

                where([
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
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`name` = 11 and `post`.`value` = 22 and concat("tt_",`test`.`id`)',
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

                where(
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
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`hello` = \'world\' OR (`test`.`id` LIKE \'你好\')',
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

                where(
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
  0 => 'SELECT `test`.* FROM `test` WHERE `test`.`hello` = \'111\' OR (`test`.`id` LIKE \'你好\' AND `test`.`value` = \'helloworld\') AND (`test`.`id2` LIKE \'你好2\' OR `test`.`value2` = \'helloworld2\' OR (`test`.`child_one` > \'123\' AND `test`.`child_two` LIKE \'123\'))',
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

                where(
                    [
                        'hello'   => '111',
                        'subor__' => [
                            ['id', 'like', '你好'],
                            ['value', '=', 'helloworld'],
                        ],
                        'suband__' => [
                            'logic__' => 'or',
                            ['id2', 'like', '你好2'],
                            ['value2', '=', 'helloworld2'],
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
