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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Query;

use Tests\TestCase;

/**
 * aggregate test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 */
class QueryAggregateTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

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
                $connect->table('test')->

                count()->

                findOne(true)
            )
        );

        $sql = <<<'eot'
[
    "SELECT COUNT(`test`.`id`) AS row_count FROM `test` LIMIT 1",
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
                $connect->table('test')->

                count('id')->

                findOne(true),
                1
            )
        );

        $sql = <<<'eot'
[
    "SELECT COUNT(`test`.`id`) AS count1 FROM `test` LIMIT 1",
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
                $connect->table('test')->

                count('id', 'count1')->

                findOne(true),
                2
            )
        );

        $sql = <<<'eot'
[
    "SELECT COUNT(`test`.`id`*50) AS count1 FROM `test` LIMIT 1",
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
                $connect->table('test')->

                count('{[id]*50}', 'count1')->

                findOne(true),
                3
            )
        );
    }

    public function testAvg()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT AVG(`test`.`id`) AS avg_value FROM `test` LIMIT 1",
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
                $connect->table('test')->

                avg('id')->

                findOne(true)
            )
        );
    }

    public function testMax()
    {
        $connect = $this->createConnect();

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
                $connect->table('test')->

                max('num')->

                findOne(true)
            )
        );
    }

    public function testMin()
    {
        $connect = $this->createConnect();

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
                $connect->table('test')->

                min('num')->

                findOne(true)
            )
        );
    }

    public function testSum()
    {
        $connect = $this->createConnect();

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
                $connect->table('test')->

                sum('num')->

                findOne(true)
            )
        );
    }
}
