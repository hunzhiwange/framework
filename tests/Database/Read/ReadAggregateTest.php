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

namespace Tests\Database\Read;

use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * read aggregate test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.23
 *
 * @version 1.0
 * @coversNothing
 */
class ReadAggregateTest extends TestCase
{
    use Query;

    public function testCount()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT COUNT(*) AS row_count FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getCount()
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                count()->

                get()
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT COUNT(*) AS row_count2 FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getCount('*', 'row_count2')
            )
        );
    }

    public function testAvg()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT AVG(`test`.`num`) AS avg_value FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getAvg('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                avg('num')->

                get()
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT AVG(`test`.`num`) AS avg_value2 FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getAvg('num', 'avg_value2')
            )
        );
    }

    public function testMax()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT MAX(`test`.`num`) AS max_value FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getMax('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                max('num')->

                get()
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT MAX(`test`.`num`) AS max_value2 FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getMax('num', 'max_value2')
            )
        );
    }

    public function testMin()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT MIN(`test`.`num`) AS min_value FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getMin('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                min('num')->

                get()
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT MIN(`test`.`num`) AS min_value2 FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getMin('num', 'min_value2')
            )
        );
    }

    public function testSum()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
array (
  0 => 'SELECT SUM(`test`.`num`) AS sum_value FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getSum('num')
            )
        );

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                sum('num')->

                get()
            )
        );

        $sql = <<<'eot'
array (
  0 => 'SELECT SUM(`test`.`num`) AS sum_value2 FROM `test` LIMIT 1',
  1 => 
  array (
  ),
  2 => false,
  3 => NULL,
  4 => NULL,
  5 => 
  array (
  ),
)
eot;

        $this->assertSame(
            $sql,
            $this->varExport(
                $connect->sql()->

                table('test')->

                getSum('num', 'sum_value2')
            )
        );
    }
}
