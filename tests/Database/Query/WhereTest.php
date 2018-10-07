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
class WhereTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        // 字段 （表达式） 值
        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', '=', 1)->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = 2",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 2)->

                findAll(true),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = 2 AND `test`.`name` > '狗蛋' AND `test`.`value` LIKE '小鸭子'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 2)->

                where('name', '>', '狗蛋')->

                where('value', 'like', '小鸭子')->

                findAll(true),
                2
            )
        );
    }

    public function testArray()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`name` LIKE '技术'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where(['name', 'like', '技术'])->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`name` LIKE '技术' AND `test`.`value` <> '结局'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where([
                    ['name', 'like', '技术'],
                    ['value', '<>', '结局'],
                ])->

                findAll(true),
                1
            )
        );
    }

    public function testOrWhere()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`name` LIKE '技术' OR `test`.`value` <> '结局'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('name', 'like', '技术')->

                orWhere('value', '<>', '结局')->

                findAll(true)
            )
        );
    }

    public function testWhereBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN 1 AND 100",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereBetween('id', [1, 100])->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN 1 AND 10",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'between', [1, 10])->

                findAll(true),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN 1 AND 100 AND `test`.`name` BETWEEN 5 AND 22",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereBetween([
                    ['id', [1, 100]],
                    ['name', [5, 22]],
                ])->

                findAll(true),
                2
            )
        );
    }

    public function testWhereNotBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` NOT BETWEEN 1 AND 10",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereNotBetween('id', [1, 10])->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` NOT BETWEEN 1 AND 10",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'not between', [1, 10])->

                findAll(true),
                1
            )
        );
    }

    public function testWhereIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IN (2,50)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereIn('id', [2, 50])->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IN ('1','10')",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'in', '1,10')->

                findAll(true),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IN (2,50)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'in', [2, 50])->

                findAll(true),
                2
            )
        );
    }

    public function testWhereNotIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` NOT IN (2,50)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereNotIn('id', [2, 50])->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` NOT IN ('1','10')",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'not in', '1,10')->

                findAll(true),
                1
            )
        );
    }

    public function testWhereNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IS NULL",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereNull('id')->

                findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'null')->

                findAll(true),
                1
            )
        );
    }

    public function testWhereNotNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IS NOT NULL",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereNotNull('id')->

                findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'not null')->

                findAll(true),
                1
            )
        );
    }

    public function testWhereLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` LIKE '5'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereLike('id', '5')->

                findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'like', '5')->

                findAll(true),
                1
            )
        );
    }

    public function testWhereNotLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` NOT LIKE '5'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereNotLike('id', '5')->

                findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 'not like', '5')->

                findAll(true),
                1
            )
        );
    }

    public function testWhereExists()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `subsql`.* FROM `subsql` WHERE `subsql`.`id` = 1)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereExists(
                    function ($select) {
                        $select->table('subsql')->where('id', 1);
                    }
                )->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `subsql`.* FROM `subsql`)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $subSelect = $connect->table('subsql');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where(
                   [
                       'exists__' => $subSelect,
                   ]
                )->

                findAll(true),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE EXISTS (select *from d_sub)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $subSelect = $connect->table('subsql');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where([
                    'exists__' => 'select *from d_sub',
                ])->

                findAll(true),
                2
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `subsql`.* FROM `subsql` WHERE `subsql`.`id` = 1)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $subSelect = $connect->table('subsql');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where(
                   [
                       'exists__' => function ($select) {
                           $select->table('subsql')->where('id', 1);
                       },
                   ]
                )->

                findAll(true),
                3
            )
        );
    }

    public function testWhereNotExists()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE NOT EXISTS (SELECT `subsql`.* FROM `subsql` WHERE `subsql`.`id` = 1)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereNotExists(
                    function ($select) {
                        $select->table('subsql')->where('id', 1);
                    }
                )->

                findAll(true)
            )
        );
    }

    public function testWhereGroup()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = 5 OR (`test`.`votes` > 100 AND `test`.`title` <> 'Admin')",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 5)->

                orWhere(function ($select) {
                    $select->where('votes', '>', 100)->where('title', '<>', 'Admin');
                })->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = 5 OR `test`.`name` = '小牛' AND (`test`.`votes` > 100 OR `test`.`title` <> 'Admin')",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('id', 5)->

                orWhere('name', '小牛')->

                where(function ($select) {
                    $select->where('votes', '>', 100)->orWhere('title', '<>', 'Admin');
                })->

                findAll(true),
                1
            )
        );
    }

    public function testConditionalExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`post`,`test`.`value`,concat(\"tt_\",`test`.`id`) FROM `test` WHERE concat(\"hello_\",`test`.`posts`) = `test`.`id`",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test', 'post,value,{concat("tt_",[id])}')->

                where('{concat("hello_",[posts])}', '=', '{[id]}')->

                findAll(true)
            )
        );
    }

    public function testArrayKeyAsField()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` = '故事' AND `test`.`name` IN (1,2,3) AND `test`.`weidao` BETWEEN '40' AND '100' AND `test`.`value` IS NULL AND `test`.`remark` IS NOT NULL AND `test`.`goods` = '东亚商品' AND `test`.`hello` = 'world'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
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

                findAll(true)
            )
        );
    }

    public function testSupportString()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`name` = 11 and `post`.`value` = 22 and concat(\"tt_\",`test`.`id`)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where(
                   ['string__' => '{[name] = 11 and [post.value] = 22 and concat("tt_",[id])}']
                )->

                findAll(true)
            )
        );
    }

    public function testSupportStringMustBeString()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'String__ type only supports string,but array given.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        where(
           ['string__' => []]
        )->

        findAll(true);
    }

    public function testSupportStringMustBeString2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'String__ type only supports string,but integer given.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        where(
           ['string__' => 1]
        )->

        findAll(true);
    }

    public function testSupportSubandSubor()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`hello` = 'world' OR (`test`.`id` LIKE '你好')",
    [],
    false,
    null,
    null,
    []
]
eot;
        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where(
                    [
                        'hello'   => 'world',
                        'subor__' => ['id', 'like', '你好'],
                    ]
                )->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`hello` = '111' OR (`test`.`id` LIKE '你好' AND `test`.`value` = 'helloworld') AND (`test`.`id2` LIKE '你好2' OR `test`.`value2` = 'helloworld2' OR (`test`.`child_one` > '123' AND `test`.`child_two` LIKE '123'))",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
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

                findAll(true),
                1
            )
        );
    }

    public function testWhereNotSupportMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Select do not implement magic method whereNotSupportMethod.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        whereNotSupportMethod()->

        findAll(true);
    }

    public function testCallWhereSugarFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` LIKE '6'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                whereLike('id', '5')->

                elses()->

                whereLike('id', '6')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testCallWhereSugarFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` LIKE '5'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                whereLike('id', '5')->

                elses()->

                whereLike('id', '6')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testOrWhereFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`value` <> 'bar'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                orWhere('value', '<>', 'foo')->

                elses()->

                orWhere('value', '<>', 'bar')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testOrWhereFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`value` <> 'foo'",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                orWhere('value', '<>', 'foo')->

                elses()->

                orWhere('value', '<>', 'bar')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testWhereExistsFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `bar`.* FROM `bar` WHERE `bar`.`id` = 2)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                whereExists(
                    function ($select) {
                        $select->table('foo')->where('id', 2);
                    }
                )->

                elses()->

                whereExists(
                    function ($select) {
                        $select->table('bar')->where('id', 2);
                    }
                )->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testWhereExistsFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `foo`.* FROM `foo` WHERE `foo`.`id` = 2)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                whereExists(
                    function ($select) {
                        $select->table('foo')->where('id', 2);
                    }
                )->

                elses()->

                whereExists(
                    function ($select) {
                        $select->table('bar')->where('id', 2);
                    }
                )->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testWhereNotExistsFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE NOT EXISTS (SELECT `bar`.* FROM `bar` WHERE `bar`.`id` = 2)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                whereNotExists(
                    function ($select) {
                        $select->table('foo')->where('id', 2);
                    }
                )->

                elses()->

                whereNotExists(
                    function ($select) {
                        $select->table('bar')->where('id', 2);
                    }
                )->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testWhereNotExistsFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE EXISTS (SELECT `foo`.* FROM `foo` WHERE `foo`.`id` = 2)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                whereExists(
                    function ($select) {
                        $select->table('foo')->where('id', 2);
                    }
                )->

                elses()->

                whereExists(
                    function ($select) {
                        $select->table('bar')->where('id', 2);
                    }
                )->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testWhereFieldWithTable()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`name` = 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                where('test.name', '=', 1)->

                findAll(true)
            )
        );
    }

    public function testWhereBetweenValueNotAnArrayException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between parameter value must be an array which not less than two elements.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        whereBetween('id', 'foo')->

        findAll(true);
    }

    public function testWhereBetweenValueNotAnArrayException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between parameter value must be an array which not less than two elements.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        whereBetween('id', [1])->

        findAll(true);
    }

    public function testWhereBetweenArrayItemIsClosure()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN (SELECT `subsql`.`id` FROM `subsql` WHERE `subsql`.`id` = 1) AND 100",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereBetween('id', [function ($select) {
                    $select->table('subsql', 'id')->where('id', 1);
                }, 100])->

                findAll(true)
            )
        );
    }

    public function testWhereInArrayItemIsClosure()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IN ((SELECT `subsql`.`id` FROM `subsql` WHERE `subsql`.`id` = 1),100)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereIn('id', [function ($select) {
                    $select->table('subsql', 'id')->where('id', 1);
                }, 100])->

                findAll(true)
            )
        );
    }

    public function testWhereBetweenArrayItemIsExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN (SELECT 1) AND 100",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereBetween('id', ['(SELECT 1)', 100])->

                findAll(true)
            )
        );
    }

    public function testWhereInArrayItemIsExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IN ((SELECT 1),100)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereIn('id', ['(SELECT 1)', 100])->

                findAll(true)
            )
        );
    }

    public function testWhereBetweenArrayItemIsSelect()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN (SELECT `foo`.`id` FROM `foo` LIMIT 1) AND 100",
    [],
    false,
    null,
    null,
    []
]
eot;

        $select = $connect->table('foo', 'id')->one();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereBetween('id', [$select, 100])->

                findAll(true)
            )
        );
    }

    public function testWhereInArrayItemIsSelect()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IN ((SELECT `foo`.`id` FROM `foo` LIMIT 1),100)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $select = $connect->table('foo', 'id')->one();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereIn('id', [$select, 100])->

                findAll(true)
            )
        );
    }

    public function testWhereBetweenArrayItemIsCondition()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` BETWEEN (SELECT `foo`.`id` FROM `foo` LIMIT 1) AND 100",
    [],
    false,
    null,
    null,
    []
]
eot;

        $condition = $connect->table('foo', 'id')->one()->getCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereBetween('id', [$condition, 100])->

                findAll(true)
            )
        );
    }

    public function testWhereInArrayItemIsCondition()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`id` IN ((SELECT `foo`.`id` FROM `foo` LIMIT 1),100)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $condition = $connect->table('foo', 'id')->one()->getCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('test')->

                whereIn('id', [$condition, 100])->

                findAll(true)
            )
        );
    }

    public function testWhereInIsClosure()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` WHERE `hello`.`id` IN (SELECT `world`.* FROM `world`)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('hello')->

                whereIn('id', function ($select) {
                    $select->table('world');
                })->

                findAll(true)
            )
        );
    }

    public function testWhereInIsSubString()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` WHERE `hello`.`id` IN (SELECT `test`.* FROM `test`)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $subSql = $connect->table('test')->makeSql(true);

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('hello')->

                where('id', 'in', $subSql)->

                findAll(true)
            )
        );
    }

    public function testWhereInIsSubIsSelect()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` WHERE `hello`.`id` IN (SELECT `test`.* FROM `test`)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $subSql = $connect->table('test');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('hello')->

                where('id', 'in', $subSql)->

                findAll(true)
            )
        );
    }

    public function testWhereEqualIsSub()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` WHERE `hello`.`id` = (SELECT `test`.`id` FROM `test` WHERE `test`.`id` = 1)",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->table('hello')->

                where('id', '=', function ($select) {
                    $select->table('test', 'id')->where('id', 1);
                })->

                findAll(true)
            )
        );
    }
}
