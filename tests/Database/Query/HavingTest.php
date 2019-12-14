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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * having test.
 *
 * @api(
 *     title="Query lang.having",
 *     zh-CN:title="查询语言.having",
 *     path="database/query/having",
 *     description="having 和 where 用法几乎一致。",
 * )
 */
class HavingTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="having 查询条件",
     *     zh-CN:description="最基本的用法为字段 （表达式） 值。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        // 字段 （表达式） 值
        $sql = <<<'eot'
            [
                "SELECT `test_query`.`tid` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`tid` HAVING `test_query`.`tid` > 5",
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
                    ->table('test_query', 'tid as id,tname as value')
                    ->groupBy('tid')
                    ->having('tid', '>', 5)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持数组方式",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`name` LIKE '技术'",
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
                    ->table('test_query', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having(['name', 'like', '技术'])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="orHaving 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testOrHaving(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`name` LIKE '技术' OR `test_query`.`tname` LIKE '技术'",
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
                    ->table('test_query', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having(['name', 'like', '技术'])
                    ->orHaving(['tname', 'like', '技术'])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingBetween 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`name` BETWEEN 1 AND 10 AND `test_query`.`name` BETWEEN 1 AND 100",
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
                    ->table('test_query', 'name as id,tname as value')
                    ->groupBy('name')
                    ->having('name', 'between', [1, 10])
                    ->havingBetween('name', [1, 100])
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`name` BETWEEN 1 AND 100 AND `test_query`.`tname` BETWEEN 5 AND 22",
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
                    ->table('test_query', 'name as id,tname as value')
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

    /**
     * @api(
     *     zh-CN:title="havingNotBetween 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingNotBetween(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` NOT BETWEEN 1 AND 10 AND `test_query`.`id` NOT BETWEEN 1 AND 100",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', 'not between', [1, 10])
                    ->havingNotBetween('id', [1, 100])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingIn 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN (2,50) AND `test_query`.`num` IN (2,50)",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', 'in', [2, 50])
                    ->havingIn('num', [2, 50])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingNotIn 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingNotIn(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` NOT IN (2,50) AND `test_query`.`num` NOT IN (2,50)",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', 'not in', [2, 50])
                    ->havingNotIn('num', [2, 50])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingNull 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IS NULL AND `test_query`.`num` IS NULL",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', 'null')
                    ->havingNull('num')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingNotNull 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingNotNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IS NOT NULL AND `test_query`.`num` IS NOT NULL",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', 'not null')
                    ->havingNotNull('num')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件未指定值默认为 null",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingDefaultNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IS NULL",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件指定值为 null",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingEqualNull(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IS NULL",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', '=', null)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingLike 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` LIKE '123' AND `test_query`.`num` LIKE '55'",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', 'like', '123')
                    ->havingLike('num', '55')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingNotLike 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingNotLike(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` NOT LIKE '123' AND `test_query`.`num` NOT LIKE '55'",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', 'not like', '123')
                    ->havingNotLike('num', '55')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持分组",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingGroup(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id` HAVING `test_query`.`id` = 5 OR (`test_query`.`votes` > 100 AND `test_query`.`title` <> 'Admin')",
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
                    ->table('test_query')
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

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持表达式",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testConditionalExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`posts`,`test_query`.`value`,concat(\"tt_\",`test_query`.`id`) FROM `test_query` GROUP BY `test_query`.`id` HAVING concat(\"hello_\",`test_query`.`posts`) = `test_query`.`id`",
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
                    ->table('test_query', 'posts,value,{concat("tt_",[id])}')
                    ->groupBy('id')
                    ->having('{concat("hello_",[posts])}', '=', '{[id]}')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持二维数组的键值为字段",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testArrayKeyAsField(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id` HAVING `test_query`.`id` = '故事' AND `test_query`.`name` IN (1,2,3) AND `test_query`.`weidao` BETWEEN '40' AND '100' AND `test_query`.`value` IS NULL AND `test_query`.`remark` IS NOT NULL AND `test_query`.`goods` = '东亚商品' AND `test_query`.`hello` = 'world'",
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
                    ->table('test_query')
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

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持字符串语法 `:string`",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSupportString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id` HAVING `test_query`.`name` = 11 and `test_query`.`value` = 22 and concat(\"tt_\",`test_query`.`id`)",
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
                    ->table('test_query')
                    ->groupBy('id')
                    ->having([':string' => '{[name] = 11 and [test_query.value] = 22 and concat("tt_",[id])}'])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持分组语法 `:subor` 和 `suband` ",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSupportSubandSubor(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id` HAVING `test_query`.`hello` = 'world' OR (`test_query`.`id` LIKE '你好')",
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
                    ->table('test_query')
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
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持分组语法 `:subor` 和 `suband` 任意嵌套",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSupportSubandSuborMore(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id` HAVING `test_query`.`hello` = '111' OR (`test_query`.`id` LIKE '你好' AND `test_query`.`value` = 'helloworld') AND (`test_query`.`id` LIKE '你好' OR `test_query`.`value` = 'helloworld' OR (`test_query`.`child_one` > '123' AND `test_query`.`child_two` LIKE '123'))",
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
                    ->table('test_query')
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
            'Select do not implement magic method `havingNotSupportMethod`.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->havingNotSupportMethod()
            ->findAll(true);
    }

    public function testCallHavingSugarFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id` HAVING `test_query`.`id` LIKE '6'",
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
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`id` HAVING `test_query`.`id` LIKE '5'",
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
                    ->table('test_query')
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
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`name` LIKE '技术' OR `test_query`.`tname` LIKE '改变世界'",
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
                    ->table('test_query', 'name as id,tname as value')
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
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value` FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`name` LIKE '技术' OR `test_query`.`tname` LIKE '技术'",
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
                    ->table('test_query', 'name as id,tname as value')
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
            ->table('test_query')
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
            ->table('test_query')
            ->having([':notexists' => 'select *from d_sub'])
            ->findAll(true);
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件字段可以指定表",
     *     zh-CN:description="字段条件用法和 table 中的字段用法一致，详情可以查看《查询语言.table》。",
     *     note="",
     * )
     */
    public function testHavingFieldWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`name` = 1",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('test_query.name', '=', 1)
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
            ->table('test_query')
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
            ->table('test_query')
            ->groupBy('name')
            ->havingBetween('id', [1])
            ->findAll(true);
    }

    public function testHavingBetweenArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = 1) AND 100",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->havingBetween('id', [function ($select) {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1);
                    }, 100])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="having 查询条件支持复杂的子查询",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingInArrayItemIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = 1),100)",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->havingIn('id', [function ($select) {
                        $select
                            ->table('test_query_subsql', 'id')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` BETWEEN (SELECT 1) AND 100",
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
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN ((SELECT 1),100)",
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
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1) AND 100",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $select = $connect->table('test_query_subsql', 'id')->one();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1),100)",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $select = $connect->table('test_query_subsql', 'id')->one();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` BETWEEN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1) AND 100",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $condition = $connect
            ->table('test_query_subsql', 'id')
            ->one()
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN ((SELECT `test_query_subsql`.`id` FROM `test_query_subsql` LIMIT 1),100)",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $condition = $connect
            ->table('test_query_subsql', 'id')
            ->one()
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql`)",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->havingIn('id', function ($select) {
                        $select->table('test_query_subsql', 'id');
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql`)",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $subSql = $connect
            ->table('test_query_subsql', 'id')
            ->makeSql(true);

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` IN (SELECT `test_query_subsql`.`id` FROM `test_query_subsql`)",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $subSql = $connect->table('test_query_subsql', 'id');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test_query')
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
                "SELECT `test_query`.* FROM `test_query` GROUP BY `test_query`.`name` HAVING `test_query`.`id` = (SELECT `test_query_subsql`.`id` FROM `test_query_subsql` WHERE `test_query_subsql`.`id` = 1)",
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
                    ->table('test_query')
                    ->groupBy('name')
                    ->having('id', '=', function ($select) {
                        $select
                            ->table('test_query_subsql', 'id')
                            ->where('id', 1);
                    })
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="havingRaw 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testHavingRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value`,`test_query`.`id` FROM `test_query` GROUP BY `test_query`.`name` HAVING FIND_IN_SET(1, `test_query`.`id`)",
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
                    ->table('test_query', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->havingRaw('FIND_IN_SET(1, `test_query`.`id`)')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="orHavingRaw 查询条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testOrHavingRaw(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value`,`test_query`.`id`,`test_query`.`value` FROM `test_query` GROUP BY `test_query`.`name` HAVING FIND_IN_SET(1, `test_query`.`id`) OR FIND_IN_SET(1, `test_query`.`value`)",
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
                    ->table('test_query', 'name as id,tname as value,id,value')
                    ->groupBy('name')
                    ->havingRaw('FIND_IN_SET(1, `test_query`.`id`)')
                    ->orHavingRaw('FIND_IN_SET(1, `test_query`.`value`)')
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
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value`,`test_query`.`id` FROM `test_query` GROUP BY `test_query`.`name` HAVING FIND_IN_SET(2, `test_query`.`id`)",
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
                    ->table('test_query', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(1, `test_query`.`id`)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(2, `test_query`.`id`)')
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
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value`,`test_query`.`id` FROM `test_query` GROUP BY `test_query`.`name` HAVING FIND_IN_SET(1, `test_query`.`id`)",
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
                    ->table('test_query', 'name as id,tname as value,id')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(1, `test_query`.`id`)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(2, `test_query`.`id`)')
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
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value`,`test_query`.`id`,`test_query`.`value` FROM `test_query` GROUP BY `test_query`.`name` HAVING FIND_IN_SET(1, `test_query`.`id`) OR FIND_IN_SET(2, `test_query`.`value`)",
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
                    ->table('test_query', 'name as id,tname as value,id,value')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(2, `test_query`.`id`)')
                    ->orHavingRaw('FIND_IN_SET(1, `test_query`.`value`)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(1, `test_query`.`id`)')
                    ->orHavingRaw('FIND_IN_SET(2, `test_query`.`value`)')
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
                "SELECT `test_query`.`name` AS `id`,`test_query`.`tname` AS `value`,`test_query`.`id`,`test_query`.`value` FROM `test_query` GROUP BY `test_query`.`name` HAVING FIND_IN_SET(2, `test_query`.`id`) OR FIND_IN_SET(1, `test_query`.`value`)",
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
                    ->table('test_query', 'name as id,tname as value,id,value')
                    ->groupBy('name')
                    ->if($condition)
                    ->havingRaw('FIND_IN_SET(2, `test_query`.`id`)')
                    ->orHavingRaw('FIND_IN_SET(1, `test_query`.`value`)')
                    ->else()
                    ->havingRaw('FIND_IN_SET(1, `test_query`.`id`)')
                    ->orHavingRaw('FIND_IN_SET(2, `test_query`.`value`)')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
