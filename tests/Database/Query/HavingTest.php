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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

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
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->groupBy('tid')
                    ->having('tid', '>', 5)
                    ->findAll(true)
            )
        );
    }

    public function testArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having(['name', 'like', '技术'])
                    ->findAll(true)
            )
        );
    }

    public function testOrHaving(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having(['name', 'like', '技术'])
                    ->orHaving(['tname', 'like', '技术'])
                    ->findAll(true)
            )
        );
    }

    public function testHavingBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having('id', 'between', [1, 10])
                    ->havingBetween('id', [1, 100])
                    ->findAll(true)
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
                $connect
                    ->table('test', 'name as id,tname as value')
                    ->groupBy('name')
                    ->havingBetween([
                        ['name', [1, 100]],
                        ['tname', [5, 22]],
                    ])
                    ->findAll(true),
                1
            )
        );
    }

    public function testHavingNotBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id', 'not between', [1, 10])
                    ->havingNotBetween('id', [1, 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id', 'in', [2, 50])
                    ->havingIn('num', [2, 50])
                    ->findAll(true)
            )
        );
    }

    public function testHavingNotIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id', 'not in', [2, 50])
                    ->havingNotIn('num', [2, 50])
                    ->findAll(true)
            )
        );
    }

    public function testHavingNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id', 'null')
                    ->havingNull('num')
                    ->findAll(true)
            )
        );
    }

    public function testHavingNotNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id', 'not null')
                    ->havingNotNull('num')
                    ->findAll(true)
            )
        );
    }

    public function testOrHavingDefaultNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`name` HAVING `test`.`id` IS NULL",
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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id')
                    ->findAll(true)
            )
        );
    }

    public function testHavingLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id', 'like', '123')
                    ->havingLike('num', '55')
                    ->findAll(true)
            )
        );
    }

    public function testHavingNotLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('id', 'not like', '123')
                    ->havingNotLike('num', '55')
                    ->findAll(true)
            )
        );
    }

    public function testHavingGroup(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('id')
                    ->having('id', 5)
                    ->orHaving(function ($select) {
                        $select
                            ->having('votes', '>', 100)
                            ->having('title', '<>', 'Admin');
                    })
                    ->findAll(true)
            )
        );
    }

    public function testConditionalExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test', 'post,value,{concat("tt_",[id])}')
                    ->groupBy('id')
                    ->having('{concat("hello_",[posts])}', '=', '{[id]}')
                    ->findAll(true)
            )
        );
    }

    public function testArrayKeyAsField(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('id')
                    ->having([
                        'id'     => ['=', '故事'],
                        'name'   => ['in', [1, 2, 3]],
                        'weidao' => ['between', '40,100'],
                        'value'  => 'null',
                        'remark' => ['not null'],
                        'goods'  => '东亚商品',
                        'hello'  => ['world'],
                    ])
                    ->findAll(true)
            )
        );
    }

    public function testSupportString(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('id')
                    ->having([':string' => '{[name] = 11 and [post.value] = 22 and concat("tt_",[id])}'])
                    ->findAll(true)
            )
        );
    }

    public function testSupportSubandSubor(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('id')
                    ->having(
                        [
                            'hello'   => 'world',
                            ':subor'  => ['id', 'like', '你好'],
                        ]
                    )
                    ->findAll(true)
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
                $connect
                    ->table('test')
                    ->groupBy('id')
                    ->having(
                        [
                            'hello'   => '111',
                            ':subor'  => [
                                ['id', 'like', '你好'],
                                ['value', '=', 'helloworld'],
                            ],
                            ':suband' => [
                                ':logic' => 'or',
                                ['id', 'like', '你好'],
                                ['value', '=', 'helloworld'],
                                ':subor' => [
                                    ['child_one', '>', '123'],
                                    ['child_two', 'like', '123'],
                                ],
                            ],
                        ]
                    )
                    ->findAll(true),
                1
            )
        );
    }

    public function testHavingNotSupportMethod(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Select do not implement magic method havingNotSupportMethod.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->havingNotSupportMethod()
            ->findAll(true);
    }

    public function testCallHavingSugarFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('id')
                    ->if($condition)
                    ->havingLike('id', '5')
                    ->else()
                    ->havingLike('id', '6')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testCallHavingSugarFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('id')
                    ->if($condition)
                    ->havingLike('id', '5')
                    ->else()
                    ->havingLike('id', '6')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrHavingFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having(['name', 'like', '技术'])
                    ->if($condition)
                    ->orHaving(['tname', 'like', '技术'])
                    ->else()
                    ->orHaving(['tname', 'like', '改变世界'])
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrHavingFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having(['name', 'like', '技术'])
                    ->if($condition)
                    ->orHaving(['tname', 'like', '技术'])
                    ->else()
                    ->orHaving(['tname', 'like', '改变世界'])
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testHavingNotSupportExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Having do not support [not] exists.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->having([':exists' => 'select *from d_sub'])
            ->findAll(true);
    }

    public function testHavingNotSupportExists2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Having do not support [not] exists.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->having([':notexists' => 'select *from d_sub'])
            ->findAll(true);
    }

    public function testHavingFieldWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->having('test.name', '=', 1)
                    ->findAll(true)
            )
        );
    }

    public function testHavingBetweenValueNotAnArrayException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between param value must be an array which not less than two elements.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->groupBy('name')
            ->havingBetween('id', 'foo')
            ->findAll(true);
    }

    public function testHavingBetweenValueNotAnArrayException2(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The [not] between param value must be an array which not less than two elements.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->groupBy('name')
            ->havingBetween('id', [1])
            ->findAll(true);
    }

    public function testHavingBetweenArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingBetween('id', [function ($select) {
                        $select
                            ->table('subsql', 'id')
                            ->where('id', 1);
                    }, 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingIn('id', [function ($select) {
                        $select
                            ->table('subsql', 'id')
                            ->where('id', 1);
                    }, 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingBetweenArrayItemIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingBetween('id', ['(SELECT 1)', 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingIn('id', ['(SELECT 1)', 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingBetweenArrayItemIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingBetween('id', [$select, 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingIn('id', [$select, 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingBetweenArrayItemIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

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

        $condition = $connect
            ->table('foo', 'id')
            ->one()
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingBetween('id', [$condition, 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingInArrayItemIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

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

        $condition = $connect
            ->table('foo', 'id')
            ->one()
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->groupBy('name')
                    ->havingIn('id', [$condition, 100])
                    ->findAll(true)
            )
        );
    }

    public function testHavingInIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('hello')
                    ->groupBy('name')
                    ->havingIn('id', function ($select) {
                        $select->table('world');
                    })
                    ->findAll(true)
            )
        );
    }

    public function testHavingInIsSubString(): void
    {
        $connect = $this->createDatabaseConnectMock();

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

        $subSql = $connect
            ->table('test')
            ->makeSql(true);

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('hello')
                    ->groupBy('name')
                    ->having('id', 'in', $subSql)
                    ->findAll(true)
            )
        );
    }

    public function testHavingInIsSubIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('hello')
                    ->groupBy('name')
                    ->having('id', 'in', $subSql)
                    ->findAll(true)
            )
        );
    }

    public function testHavingEqualIsSub(): void
    {
        $connect = $this->createDatabaseConnectMock();

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
                $connect
                    ->table('hello')
                    ->groupBy('name')
                    ->having('id', '=', function ($select) {
                        $select
                            ->table('test', 'id')
                            ->where('id', 1);
                    })
                    ->findAll(true)
            )
        );
    }

    public function testHavingRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value`,`test`.`id` FROM `test` GROUP BY `test`.`name` HAVING FIND_IN_SET(1, id)",
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
                $connect
                    ->table('test', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->havingRaw('FIND_IN_SET(1, id)')
                    ->findAll(true)
            )
        );
    }

    public function testOrHavingRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value`,`test`.`id`,`test`.`value` FROM `test` GROUP BY `test`.`name` HAVING FIND_IN_SET(1, id) OR FIND_IN_SET(1, value)",
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
                $connect
                    ->table('test', 'name as id,tname as value,id,value')
                    ->groupBy('name')
                    ->havingRaw('FIND_IN_SET(1, id)')
                    ->orHavingRaw('FIND_IN_SET(1, value)')
                    ->findAll(true)
            )
        );
    }

    public function testHavingRawFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value`,`test`.`id` FROM `test` GROUP BY `test`.`name` HAVING FIND_IN_SET(2, id)",
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
                $connect
                    ->table('test', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(1, id)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(2, id)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testHavingRawFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value`,`test`.`id` FROM `test` GROUP BY `test`.`name` HAVING FIND_IN_SET(1, id)",
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
                $connect
                    ->table('test', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(1, id)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(2, id)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrHavingRawFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value`,`test`.`id` FROM `test` GROUP BY `test`.`name` HAVING FIND_IN_SET(1, id) OR FIND_IN_SET(2, value)",
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
                $connect
                    ->table('test', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(2, id)')
                    ->orHavingRaw('FIND_IN_SET(1, value)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(1, id)')
                    ->orHavingRaw('FIND_IN_SET(2, value)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testOrHavingRawFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name` AS `id`,`test`.`tname` AS `value`,`test`.`id` FROM `test` GROUP BY `test`.`name` HAVING FIND_IN_SET(2, id) OR FIND_IN_SET(1, value)",
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
                $connect
                    ->table('test', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(2, id)')
                    ->orHavingRaw('FIND_IN_SET(1, value)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(1, id)')
                    ->orHavingRaw('FIND_IN_SET(2, value)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
