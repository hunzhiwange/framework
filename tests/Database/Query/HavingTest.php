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
 */
class HavingTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        // 字段 （表达式） 值
        $sql = <<<'eot'
[
    "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`tid` HAVING `test`.`tid` > 5",
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
                $connect->table('test', 'tid as id,tname as value')->

                groupBy('tid')->

                having('tid', '>', 5)->

                findAll(true)
            )
        );
    }

    public function testArray()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` LIKE '技术'",
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having(['name', 'like', '技术'])->

                findAll(true)
            )
        );
    }

    public function testOrHaving()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` LIKE '技术' OR `test`.`tname` LIKE '技术'",
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having(['name', 'like', '技术'])->

                orHaving(['tname', 'like', '技术'])->

                findAll(true)
            )
        );
    }

    public function testHavingBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` BETWEEN 1 AND 10 AND `test`.`id` BETWEEN 1 AND 100",
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having('id', 'between', [1, 10])->

                havingBetween('id', [1, 100])->

                findAll(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` BETWEEN 1 AND 100 AND `test`.`tname` BETWEEN 5 AND 22",
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                havingBetween([
                    ['name', [1, 100]],
                    ['tname', [5, 22]],
                ])->

                findAll(true),
                1
            )
        );
    }

    public function testHavingNotBetween()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` NOT BETWEEN 1 AND 10 AND `test`.`id` NOT BETWEEN 1 AND 100",
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

                groupBy('name')->

                having('id', 'not between', [1, 10])->

                havingNotBetween('id', [1, 100])->

                findAll(true)
            )
        );
    }

    public function testHavingIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IN (2,50) AND `test`.`num` IN (2,50)",
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

                groupBy('name')->

                having('id', 'in', [2, 50])->

                havingIn('num', [2, 50])->

                findAll(true)
            )
        );
    }

    public function testHavingNotIn()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` NOT IN (2,50) AND `test`.`num` NOT IN (2,50)",
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

                groupBy('name')->

                having('id', 'not in', [2, 50])->

                havingNotIn('num', [2, 50])->

                findAll(true)
            )
        );
    }

    public function testHavingNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IS NULL AND `test`.`num` IS NULL",
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

                groupBy('name')->

                having('id', 'null')->

                havingNull('num')->

                findAll(true)
            )
        );
    }

    public function testHavingNotNull()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IS NOT NULL AND `test`.`num` IS NOT NULL",
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

                groupBy('name')->

                having('id', 'not null')->

                havingNotNull('num')->

                findAll(true)
            )
        );
    }

    public function testHavingLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` LIKE '123' AND `test`.`num` LIKE '55'",
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

                groupBy('name')->

                having('id', 'like', '123')->

                havingLike('num', '55')->

                findAll(true)
            )
        );
    }

    public function testHavingNotLike()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` NOT LIKE '123' AND `test`.`num` NOT LIKE '55'",
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

                groupBy('name')->

                having('id', 'not like', '123')->

                havingNotLike('num', '55')->

                findAll(true)
            )
        );
    }

    public function testHavingGroup()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`id` = 5 OR (`test`.`votes` > 100 AND `test`.`title` <> 'Admin')",
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

                groupBy('id')->

                having('id', 5)->

                orHaving(function ($select) {
                    $select->having('votes', '>', 100)->having('title', '<>', 'Admin');
                })->

                findAll(true)
            )
        );
    }

    public function testConditionalExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`post`,`test`.`value`,concat(\"tt_\",`test`.`id`) FROM `test` GROUP BY `test`.`id` HAVING concat(\"hello_\",`test`.`posts`) = `test`.`id`",
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

                groupBy('id')->

                having('{concat("hello_",[posts])}', '=', '{[id]}')->

                findAll(true)
            )
        );
    }

    public function testArrayKeyAsField()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`id` = '故事' AND `test`.`name` IN (1,2,3) AND `test`.`weidao` BETWEEN '40' AND '100' AND `test`.`value` IS NULL AND `test`.`remark` IS NOT NULL AND `test`.`goods` = '东亚商品' AND `test`.`hello` = 'world'",
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

                findAll(true)
            )
        );
    }

    public function testSupportString()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`name` = 11 and `post`.`value` = 22 and concat(\"tt_\",`test`.`id`)",
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

                groupBy('id')->

                having(
                    ['string__' => '{[name] = 11 and [post.value] = 22 and concat("tt_",[id])}']
                )->

                findAll(true)
            )
        );
    }

    public function testSupportSubandSubor()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`hello` = 'world' OR (`test`.`id` LIKE '你好')",
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

                groupBy('id')->

                having(
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
    "SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`hello` = '111' OR (`test`.`id` LIKE '你好' AND `test`.`value` = 'helloworld') AND (`test`.`id` LIKE '你好' OR `test`.`value` = 'helloworld' OR (`test`.`child_one` > '123' AND `test`.`child_two` LIKE '123'))",
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

                findAll(true),
                1
            )
        );
    }

    public function testHavingNotSupportMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Select do not implement magic method havingNotSupportMethod.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        havingNotSupportMethod()->

        findAll(true);
    }

    public function testCallHavingSugarFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`id` LIKE '6'",
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

                groupBy('id')->

                ifs($condition)->

                havingLike('id', '5')->

                elses()->

                havingLike('id', '6')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testCallHavingSugarFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`id` HAVING `test`.`id` LIKE '5'",
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

                groupBy('id')->

                ifs($condition)->

                havingLike('id', '5')->

                elses()->

                havingLike('id', '6')->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testOrHavingFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` LIKE '技术' OR `test`.`tname` LIKE '改变世界'",
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having(['name', 'like', '技术'])->

                ifs($condition)->

                orHaving(['tname', 'like', '技术'])->

                elses()->

                orHaving(['tname', 'like', '改变世界'])->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testOrHavingFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` LIKE '技术' OR `test`.`tname` LIKE '技术'",
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
                $connect->table('test', 'name as id,tname as value')->

                groupBy('name')->

                having(['name', 'like', '技术'])->

                ifs($condition)->

                orHaving(['tname', 'like', '技术'])->

                elses()->

                orHaving(['tname', 'like', '改变世界'])->

                endIfs()->

                findAll(true)
            )
        );
    }

    public function testHavingNotSupportExists()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Having do not support [not] exists.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        having([
            'exists__' => 'select *from d_sub',
        ])->

        findAll(true);
    }

    public function testHavingNotSupportExists2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Having do not support [not] exists.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        having([
            'notexists__' => 'select *from d_sub',
        ])->

        findAll(true);
    }

    public function testHavingFieldWithTable()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`name` = 1",
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

                groupBy('name')->

                having('test.name', '=', 1)->

                findAll(true)
            )
        );
    }

    public function testHavingBetweenValueNotAnArrayException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between parameter value must be an array which not less than two elements.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        groupBy('name')->

        havingBetween('id', 'foo')->

        findAll(true);
    }

    public function testHavingBetweenValueNotAnArrayException2()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between parameter value must be an array which not less than two elements.'
        );

        $connect = $this->createConnect();

        $connect->table('test')->

        groupBy('name')->

        havingBetween('id', [1])->

        findAll(true);
    }

    public function testHavingBetweenArrayItemIsClosure()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` BETWEEN (SELECT `subsql`.`id` FROM `subsql` WHERE `subsql`.`id` = 1) AND 100",
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

                groupBy('name')->

                havingBetween('id', [function ($select) {
                    $select->table('subsql', 'id')->where('id', 1);
                }, 100])->

                findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsClosure()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IN ((SELECT `subsql`.`id` FROM `subsql` WHERE `subsql`.`id` = 1),100)",
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

                groupBy('name')->

                havingIn('id', [function ($select) {
                    $select->table('subsql', 'id')->where('id', 1);
                }, 100])->

                findAll(true)
            )
        );
    }

    public function testHavingBetweenArrayItemIsExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` BETWEEN (SELECT 1) AND 100",
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

                groupBy('name')->

                havingBetween('id', ['(SELECT 1)', 100])->

                findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsExpression()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IN ((SELECT 1),100)",
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

                groupBy('name')->

                havingIn('id', ['(SELECT 1)', 100])->

                findAll(true)
            )
        );
    }

    public function testHavingBetweenArrayItemIsSelect()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` BETWEEN (SELECT `foo`.`id` FROM `foo` LIMIT 1) AND 100",
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

                groupBy('name')->

                havingBetween('id', [$select, 100])->

                findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsSelect()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IN ((SELECT `foo`.`id` FROM `foo` LIMIT 1),100)",
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

                groupBy('name')->

                havingIn('id', [$select, 100])->

                findAll(true)
            )
        );
    }

    public function testHavingBetweenArrayItemIsCondition()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` BETWEEN (SELECT `foo`.`id` FROM `foo` LIMIT 1) AND 100",
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

                groupBy('name')->

                havingBetween('id', [$condition, 100])->

                findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsCondition()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IN ((SELECT `foo`.`id` FROM `foo` LIMIT 1),100)",
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

                groupBy('name')->

                havingIn('id', [$condition, 100])->

                findAll(true)
            )
        );
    }

    public function testHavingInIsClosure()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` GROUP BY `hello`.`name` HAVING `hello`.`id` IN (SELECT `world`.* FROM `world`)",
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

                groupBy('name')->

                havingIn('id', function ($select) {
                    $select->table('world');
                })->

                findAll(true)
            )
        );
    }

    public function testHavingInIsSubString()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` GROUP BY `hello`.`name` HAVING `hello`.`id` IN (SELECT `test`.* FROM `test`)",
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

                groupBy('name')->

                having('id', 'in', $subSql)->

                findAll(true)
            )
        );
    }

    public function testHavingInIsSubIsSelect()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` GROUP BY `hello`.`name` HAVING `hello`.`id` IN (SELECT `test`.* FROM `test`)",
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

                groupBy('name')->

                having('id', 'in', $subSql)->

                findAll(true)
            )
        );
    }

    public function testHavingEqualIsSub()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT `hello`.* FROM `hello` GROUP BY `hello`.`name` HAVING `hello`.`id` = (SELECT `test`.`id` FROM `test` WHERE `test`.`id` = 1)",
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

                groupBy('name')->

                having('id', '=', function ($select) {
                    $select->table('test', 'id')->where('id', 1);
                })->

                findAll(true)
            )
        );
    }
}
