<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Leevel\Kernel\Utils\Api;
use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'zh-CN:title' => '聚合查询.aggregate',
    'path' => 'database/read/aggregate',
])]
final class AggregateTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'findCount 查询数量',
    ])]
    public function testCount(): void
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
                $connect->table('test_query')
                    ->findCount(),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'count.find 查询数量',
    ])]
    public function testCount2(): void
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
                $connect->table('test_query')
                    ->count()
                    ->find(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findCount 查询数量指定字段和别名',
    ])]
    public function testCount3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT COUNT(*) AS row_count2 FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findCount('*', 'row_count2'),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findAvg 查询平均值',
    ])]
    public function testAvg(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findAvg('num'),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'avg.find 查询平均值',
    ])]
    public function testAvg2(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->avg('num')
                    ->find(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findAvg 查询平均值指定字段和别名',
    ])]
    public function testAvg3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value2 FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findAvg('num', 'avg_value2'),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findMax 查询最大值',
    ])]
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
                $connect->table('test_query')
                    ->findMax('num'),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'max.find 查询最大值',
    ])]
    public function testMax2(): void
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
                $connect->table('test_query')
                    ->max('num')
                    ->find(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findMax 查询最大值指定字段和别名',
    ])]
    public function testMax3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT MAX(`test_query`.`num`) AS max_value2 FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findMax('num', 'max_value2'),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findMin 查询最小值',
    ])]
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
                $connect->table('test_query')
                    ->findMin('num'),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'min.find 查询最小值',
    ])]
    public function testMin2(): void
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
                $connect->table('test_query')
                    ->min('num')
                    ->find(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findMin 查询最小值指定字段和别名',
    ])]
    public function testMin3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT MIN(`test_query`.`num`) AS min_value2 FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findMin('num', 'min_value2'),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findSum 查询合计',
    ])]
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
                $connect->table('test_query')
                    ->findSum('num'),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'sum.find 查询合计',
    ])]
    public function testSum2(): void
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
                $connect->table('test_query')
                    ->sum('num')
                    ->find(),
                $connect,
                1
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findSum 查询合计指定字段和别名',
    ])]
    public function testSum3(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT SUM(`test_query`.`num`) AS sum_value2 FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findSum('num', 'sum_value2'),
                $connect,
                2
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'findAvg 查询字段指定表名',
    ])]
    public function testAvgWithTable(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findAvg('test_query.num'),
                $connect
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'avg.find 查询字段指定表名',
    ])]
    public function testAvgWithTable2(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT AVG(`test_query`.`num`) AS avg_value FROM `test_query` LIMIT 1",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->avg('test_query.num')
                    ->find(),
                $connect,
                1
            )
        );
    }
}
