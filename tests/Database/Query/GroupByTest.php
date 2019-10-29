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
 * groupBy test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 *
 * @api(
 *     title="Query lang.groupBy",
 *     zh-CN:title="查询语言.groupBy",
 *     path="database/query/groupby",
 *     zh-CN:description="
 * ## groupBy 函数原型
 *
 * ``` php
 * public function groupBy($expression);
 * ```
 *
 *  - 参数支持字符串以及它们构成的一维数组，用法和 《查询语言.orderBy》 非常相似。",
 * )
 */
class GroupByTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="groupBy 基础用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`id`,`test`.`name`",
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
                    ->groupBy('id')
                    ->groupBy('name')
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 字段指定表名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `post`.`id`",
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
                    ->groupBy('post.id')
                    ->findAll(true),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 字段表达式",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithExpression(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY SUM(`test`.`num`)",
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
                    ->groupBy('{SUM([num])}')
                    ->findAll(true),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 复合型",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithComposite(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`title`,`test`.`id`,concat('1234',`test`.`id`,'ttt')",
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
                    ->groupBy("title,id,{concat('1234',[id],'ttt')}")
                    ->findAll(true),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="groupBy 字段数组支持",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` GROUP BY `test`.`title`,`test`.`id`,`test`.`ttt`,`test`.`value`",
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
                    ->groupBy(['title,id,ttt', 'value'])
                    ->findAll(true),
                4
            )
        );
    }

    public function testGroupByFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`name`",
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
                    ->groupBy('id')
                    ->else()
                    ->groupBy('name')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testGroupByFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`id`",
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
                    ->groupBy('id')
                    ->else()
                    ->groupBy('name')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
