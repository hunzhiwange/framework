<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     title="Query lang.aggregate",
 *     zh-CN:title="查询语言.aggregate",
 *     path="database/query/aggregate",
 *     zh-CN:description="数据库聚合查询功能。",
 * )
 *
 * @internal
 */
final class AggregateTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="记录数量 count",
     *     zh-CN:description="
     * 计算记录数量。
     *
     * `函数原型`
     *
     * ``` php
     * public function findCount(string $field = '*', string $alias = 'row_count');
     * ```
     *
     * ::: tip
     * 可使用 `findCount()` 或者 `count()->find()` 来统计记录行。
     * :::
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT COUNT(*) AS row_count FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->count()
                    ->findOne(),
                $connect
            )
        );

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`id`) AS row_count FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->count('id')
                    ->findOne(),
                $connect,
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`id`) AS count1 FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->count('id', 'count1')
                    ->findOne(),
                $connect,
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT COUNT(`test_query`.`id`*50) AS count1 FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->count(Condition::raw('[id]*50'), 'count1')
                    ->findOne(),
                $connect,
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="平均值 avg",
     *     zh-CN:description="计算平均值。",
     *     zh-CN:note="",
     * )
     */
    public function testAvg(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`id`) AS avg_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->avg('id')
                    ->findOne(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="最大值 max",
     *     zh-CN:description="计算最大值。",
     *     zh-CN:note="",
     * )
     */
    public function testMax(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MAX(`test_query`.`num`) AS max_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->max('num')
                    ->findOne(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="最小值 min",
     *     zh-CN:description="计算最小值。",
     *     zh-CN:note="",
     * )
     */
    public function testMin(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT MIN(`test_query`.`num`) AS min_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->min('num')
                    ->findOne(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="合计 sum",
     *     zh-CN:description="计算合计。",
     *     zh-CN:note="",
     * )
     */
    public function testSum(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SUM(`test_query`.`num`) AS sum_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->sum('num')
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->count('bar')
                    ->else()
                    ->count('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->count('bar')
                    ->else()
                    ->count('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->avg('bar')
                    ->else()
                    ->avg('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->avg('bar')
                    ->else()
                    ->avg('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->max('bar')
                    ->else()
                    ->max('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->max('bar')
                    ->else()
                    ->max('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->min('bar')
                    ->else()
                    ->min('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->min('bar')
                    ->else()
                    ->min('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->sum('bar')
                    ->else()
                    ->sum('foo')
                    ->fi()
                    ->findOne(),
                $connect
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
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->if($condition)
                    ->sum('bar')
                    ->else()
                    ->sum('foo')
                    ->fi()
                    ->findOne(),
                $connect
            )
        );
    }
}
