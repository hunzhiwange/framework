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

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="聚合查询.aggregate",
 *     path="database/read/aggregate",
 *     description="",
 * )
 */
class AggregateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="findCount 查询数量",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testCount(): void
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
                    ->sql()
                    ->table('test_query')
                    ->findCount()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="count.find 查询数量",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testCount2(): void
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
                    ->sql()
                    ->table('test_query')
                    ->count()
                    ->find(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findCount 查询数量指定字段和别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testCount3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT COUNT(*) AS row_count2 FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->findCount('*', 'row_count2'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findAvg 查询平均值",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testAvg(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->findAvg('num')
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="avg.find 查询平均值",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testAvg2(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->avg('num')
                    ->find(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findAvg 查询平均值指定字段和别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testAvg3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value2 FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->findAvg('num', 'avg_value2'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findMax 查询最大值",
     *     zh-CN:description="",
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
                    ->sql()
                    ->table('test_query')
                    ->findMax('num')
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="max.find 查询最大值",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testMax2(): void
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
                    ->sql()
                    ->table('test_query')
                    ->max('num')
                    ->find(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findMax 查询最大值指定字段和别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testMax3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT MAX(`test_query`.`num`) AS max_value2 FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->findMax('num', 'max_value2'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findMin 查询最小值",
     *     zh-CN:description="",
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
                    ->sql()
                    ->table('test_query')
                    ->findMin('num')
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="min.find 查询最小值",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testMin2(): void
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
                    ->sql()
                    ->table('test_query')
                    ->min('num')
                    ->find(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findMin 查询最小值指定字段和别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testMin3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT MIN(`test_query`.`num`) AS min_value2 FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->findMin('num', 'min_value2'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findSum 查询合计",
     *     zh-CN:description="",
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
                    ->sql()
                    ->table('test_query')
                    ->findSum('num')
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="sum.find 查询合计",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSum2(): void
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
                    ->sql()
                    ->table('test_query')
                    ->sum('num')
                    ->find(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findSum 查询合计指定字段和别名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSum3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT SUM(`test_query`.`num`) AS sum_value2 FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->findSum('num', 'sum_value2'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findAvg 查询字段指定表名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testAvgWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->findAvg('test_query.num')
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="avg.find 查询字段指定表名",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testAvgWithTable2(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
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
                    ->sql()
                    ->table('test_query')
                    ->avg('test_query.num')
                    ->find(),
                1
            )
        );
    }
}
