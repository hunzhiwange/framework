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
 * join test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.17
 *
 * @version 1.0
 *
 * @api(
 *     title="查询语言.join",
 *     path="database/query/join",
 *     description="
 * ## join 函数原型
 *
 * ``` php
 * join($table, $cols, ...$cond);
 * ```
 *
 *  - 其中 $table 和 $cols 与 《查询语言.table》中的用法一致。
 *  - $cond 与《查询语言.where》中的用法一致。",
 * )
 */
class JoinTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="join 基础用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = '小牛'",
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
                    ->join('hello', 'name,value', 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->join(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件支持数组和表达式",
     *     zh-CN:description="实质上 where 支持语法特性都支持。",
     *     note="",
     * )
     */
    public function testWithConditionSupportArrayAndExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`hello` = 'world' AND `hello`.`test` > `hello`.`name`",
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
                    ->join('hello', 'name,value', ['hello' => 'world', ['test', '>', '{[name]}']])
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 附加条件支持闭包",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithConditionIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON (`hello`.`id` < 5 AND `hello`.`name` LIKE 'hello')",
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
                    ->join('hello', 'name,value', function ($select) {
                        $select
                            ->where('id', '<', 5)
                            ->where('name', 'like', 'hello');
                    })
                    ->findAll(true),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="innerJoin 查询",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="leftJoin 查询",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testLeftJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` LEFT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="rightJoin 查询",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testRightJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` RIGHT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="fullJoin 查询",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testFullJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` FULL JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="crossJoin 查询",
     *     zh-CN:description="自然连接不用设置 on 条件。",
     *     note="",
     * )
     */
    public function testCrossJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` CROSS JOIN `hello` `t`",
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
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="naturalJoin 查询",
     *     zh-CN:description="自然连接不用设置 on 条件。",
     *     note="",
     * )
     */
    public function testNaturalJoin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` NATURAL JOIN `hello` `t`",
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
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'])
                    ->findAll(true)
            )
        );
    }

    public function testJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = '哥'",
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
                    ->if($condition)
                    ->join('hello', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('hello', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = '小牛'",
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
                    ->if($condition)
                    ->join('hello', 'name,value', 'name', '=', '小牛')
                    ->else()
                    ->join('hello', 'name,value', 'name', '=', '哥')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testInnerJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->if($condition)
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testInnerJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` INNER JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->if($condition)
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testLeftJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` LEFT JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->if($condition)
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testLeftJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` LEFT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->if($condition)
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->leftJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testRightJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` RIGHT JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->if($condition)
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testRightJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` RIGHT JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->if($condition)
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->rightJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFullJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` FULL JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->if($condition)
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFullJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` FULL JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->if($condition)
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->fullJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testCrossJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` CROSS JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->if($condition)
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testCrossJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` CROSS JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->if($condition)
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->crossJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testNaturalJsonFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` NATURAL JOIN `hello` `t` ON `t`.`name` = '仔'",
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
                    ->if($condition)
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testNaturalJsonFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`t`.`name` AS `nikename`,`t`.`value` AS `tt` FROM `test` NATURAL JOIN `hello` `t` ON `t`.`name` = '小牛'",
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
                    ->if($condition)
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->else()
                    ->naturalJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '仔')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testInnerJsonAndUnionWillThrowException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'JOIN queries cannot be used while using UNION queries.'
        );

        $connect = $this->createDatabaseConnectMock();
        $union = 'SELECT id,value FROM test2';

        $connect
            ->table('test', 'tid as id,tname as value')
            ->union($union)
            ->innerJoin(['t' => 'hello'], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
            ->findAll(true);
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持查询对象",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJoinWithTableIsSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) b ON `b`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect->table('foo as b');

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持查询条件对象",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJoinWithTableIsCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) b ON `b`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin($joinTable, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持闭包",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJoinWithTableIsClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`b`.`name` AS `nikename`,`b`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) b ON `b`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin(function ($select) {
                        $select->table('foo as b');
                    }, ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持数组别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJoinWithTableIsArrayCondition(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`foo`.`name` AS `nikename`,`foo`.`value` AS `tt` FROM `test` INNER JOIN (SELECT `b`.* FROM `foo` `b`) foo ON `foo`.`name` = '小牛'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->innerJoin(['foo' => $joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
                    ->findAll(true)
            )
        );
    }

    public function testInnerJoinWithTableIsArrayAndTheAliasKeyMustBeString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Alias must be string,but integer given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $joinTable = $connect
            ->table('foo as b')
            ->databaseCondition();

        $connect
            ->table('test')
            ->innerJoin([$joinTable], ['name as nikename', 'tt' => 'value'], 'name', '=', '小牛')
            ->findAll(true);
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持表达式",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJsonWithTableNameIsExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`a`.`name` AS `nikename`,`a`.`value` AS `tt` FROM `test` INNER JOIN (SELECT * FROM foo) a ON `a`.`name` = `test`.`name`",
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
                    ->innerJoin('(SELECT * FROM foo)', ['name as nikename', 'tt' => 'value'], 'name', '=', '{[test.name]}')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="join 查询支持表支持表达式别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testInnerJsonWithTableNameIsExpressionWithAsCustomAlias(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`foo`.`name` AS `nikename`,`foo`.`value` AS `tt` FROM `test` INNER JOIN (SELECT * FROM foo) foo ON `foo`.`name` = `test`.`name`",
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
                    ->innerJoin('(SELECT * FROM foo) as foo', ['name as nikename', 'tt' => 'value'], 'name', '=', '{[test.name]}')
                    ->findAll(true)
            )
        );
    }
}
