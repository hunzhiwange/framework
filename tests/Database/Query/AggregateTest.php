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
 * aggregate test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 *
 * @api(
 *     title="Query lang.aggregate",
 *     zh-CN:title="查询语言.aggregate",
 *     path="database/query/aggregate",
 *     description="数据库聚合查询功能。",
 * )
 */
class AggregateTest extends TestCase
{
    /**
     * @api(
     *     title="记录数量 count",
     *     description="计算记录数量。
     *
     * `函数原型`
     *
     * ``` php
     * public function findCount(string $field = '*', string $alias = 'row_count', bool $flag = false);
     * ```
     *
     * ::: tip
     * 可使用 `findCount()` 或者 `count()->find()` 来统计记录行。
     * :::
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT COUNT(*) AS row_count FROM `test_query` LIMIT 1",
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
                    ->count()
                    ->findOne(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`id`) AS row_count FROM `test_query` LIMIT 1",
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
                    ->count('id')
                    ->findOne(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`id`) AS count1 FROM `test_query` LIMIT 1",
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
                    ->count('id', 'count1')
                    ->findOne(true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`id`*50) AS count1 FROM `test_query` LIMIT 1",
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
                    ->count('{[id]*50}', 'count1')
                    ->findOne(true),
                3
            )
        );
    }

    /**
     * @api(
     *     title="平均值 avg",
     *     description="计算平均值。",
     *     note="",
     * )
     */
    public function testAvg(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`id`) AS avg_value FROM `test_query` LIMIT 1",
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
                    ->avg('id')
                    ->findOne(true)
            )
        );
    }

    /**
     * @api(
     *     title="最大值 max",
     *     description="计算最大值。",
     *     note="",
     * )
     */
    public function testMax(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MAX(`test_query`.`num`) AS max_value FROM `test_query` LIMIT 1",
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
                    ->max('num')
                    ->findOne(true)
            )
        );
    }

    /**
     * @api(
     *     title="最小值 min",
     *     description="计算最小值。",
     *     note="",
     * )
     */
    public function testMin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MIN(`test_query`.`num`) AS min_value FROM `test_query` LIMIT 1",
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
                    ->min('num')
                    ->findOne(true)
            )
        );
    }

    /**
     * @api(
     *     title="合计 sum",
     *     description="计算合计。",
     *     note="",
     * )
     */
    public function testSum(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SUM(`test_query`.`num`) AS sum_value FROM `test_query` LIMIT 1",
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
                    ->sum('num')
                    ->findOne(true)
            )
        );
    }

    public function testCountFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`foo`) AS row_count FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->count('bar')
                    ->else()
                    ->count('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testCountFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`bar`) AS row_count FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->count('bar')
                    ->else()
                    ->count('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testAvgFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`foo`) AS avg_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->avg('bar')
                    ->else()
                    ->avg('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testAvgFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`bar`) AS avg_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->avg('bar')
                    ->else()
                    ->avg('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testMaxFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MAX(`test_query`.`foo`) AS max_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->max('bar')
                    ->else()
                    ->max('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testMaxFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MAX(`test_query`.`bar`) AS max_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->max('bar')
                    ->else()
                    ->max('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testMinFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MIN(`test_query`.`foo`) AS min_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->min('bar')
                    ->else()
                    ->min('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testMinFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MIN(`test_query`.`bar`) AS min_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->min('bar')
                    ->else()
                    ->min('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testSumFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SUM(`test_query`.`foo`) AS sum_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->sum('bar')
                    ->else()
                    ->sum('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }

    public function testSumFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SUM(`test_query`.`bar`) AS sum_value FROM `test_query` LIMIT 1",
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
                    ->if($condition)
                    ->sum('bar')
                    ->else()
                    ->sum('foo')
                    ->fi()
                    ->findOne(true)
            )
        );
    }
}
