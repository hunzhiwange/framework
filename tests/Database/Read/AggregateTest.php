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

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * aggregate test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.23
 *
 * @version 1.0
 */
class AggregateTest extends TestCase
{
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
