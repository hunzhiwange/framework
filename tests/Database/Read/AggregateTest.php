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
    public function testCount()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT COUNT(*) AS row_count FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findCount()
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                count()->

                find(),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT COUNT(*) AS row_count2 FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findCount('*', 'row_count2'),
                2
            )
        );
    }

    public function testAvg()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT AVG(`test`.`num`) AS avg_value FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findAvg('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                avg('num')->

                find(),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT AVG(`test`.`num`) AS avg_value2 FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findAvg('num', 'avg_value2'),
                2
            )
        );
    }

    public function testMax()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT MAX(`test`.`num`) AS max_value FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findMax('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                max('num')->

                find(),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT MAX(`test`.`num`) AS max_value2 FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findMax('num', 'max_value2'),
                2
            )
        );
    }

    public function testMin()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT MIN(`test`.`num`) AS min_value FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findMin('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                min('num')->

                find(),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT MIN(`test`.`num`) AS min_value2 FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findMin('num', 'min_value2'),
                2
            )
        );
    }

    public function testSum()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT SUM(`test`.`num`) AS sum_value FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findSum('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                sum('num')->

                find(),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT SUM(`test`.`num`) AS sum_value2 FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findSum('num', 'sum_value2'),
                2
            )
        );
    }

    public function testAvgWithTable()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT AVG(`test`.`num`) AS avg_value FROM `test` LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findAvg('test.num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                avg('test.num')->

                find(),
                1
            )
        );
    }
}
