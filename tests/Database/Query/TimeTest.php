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

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * time test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.29
 *
 * @version 1.0
 */
class TimeTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $value = strtotime('+5 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereDate('create_date', '+5 month')->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereDay()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereDay('create_date', 5)->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereDayWillFormatInt()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereDay('create_date', '5')->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereDayWillFormatInt2()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereDay('create_date', '5 foo')->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereMonth()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereMonth('create_date', 5)->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereMonthFormatInt()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereMonth('create_date', '5')->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereMonthFormatInt2()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereMonth('create_date', '5 foo')->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereYear()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereYear('create_date', 2018)->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereYearFormatYear()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereYear('create_date', '2018')->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereYearFormatYear2()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                whereYear('create_date', '2018 foo')->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTime()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $value = strtotime('+5 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                time()->

                where('create_date', '+5 month')->

                endTime()->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeDateIsDefault()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $value = strtotime('+5 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                time('date')->

                where('create_date', '+5 month')->

                endTime()->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeDay()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                time('day')->

                where('create_date', 5)->

                endTime()->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeMonth()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                time('month')->

                where('create_date', 5)->

                endTime()->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeYear()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_date` = %d LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                time('year')->

                where('create_date', 2018)->

                endTime()->

                findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeMulti()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_year` = %s AND `test`.`create_month` = %s AND `test`.`create_day` = %s AND `test`.`create_date` = %s LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $year = mktime(0, 0, 0, 1, 1, 2018);
        $month = mktime(0, 0, 0, 5, 1, $date['year']);
        $day = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $date = strtotime('+5 month');
        $date2 = $date + 1;
        $date3 = $date + 2;

        $this->assertTrue(
            in_array(
                $this->varJson(
                    $connect->table('test')->

                    time('year')->

                    where('create_year', 2018)->

                    endTime()->

                    time('month')->

                    whereMonth('create_month', 5)->

                    endTime()->

                    time('day')->

                    where('create_day', 5)->

                    endTime()->

                    time('date')->

                    where('create_date', '+5 month')->

                    endTime()->

                    findOne(true)
                ), [
                    sprintf($sql, $year, $month, $day, $date),
                    sprintf($sql, $year, $month, $day, $date2),
                    sprintf($sql, $year, $month, $day, $date3),
                ], true)
        );
    }

    public function testTimeMultiWithoutEndTime()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_year` = %s AND `test`.`create_month` = %s AND `test`.`create_day` = %s AND `test`.`create_date` = %s LIMIT 1",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $year = mktime(0, 0, 0, 1, 1, 2018);
        $month = mktime(0, 0, 0, 5, 1, $date['year']);
        $day = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $date = strtotime('+5 month');
        $date2 = $date + 1;
        $date3 = $date + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                time('year')->

                where('create_year', 2018)->

                time('month')->

                whereMonth('create_month', 5)->

                time('day')->

                where('create_day', 5)->

                time('date')->

                where('create_date', '+5 month')->

                endTime()->

                findOne(true)
            ),
            sprintf($sql, $year, $month, $day, $date),
            sprintf($sql, $year, $month, $day, $date2),
            sprintf($sql, $year, $month, $day, $date3)
        );
    }

    public function testDateStrtotimeReturnFalse()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Please enter a right time of strtotime.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect->table('test')->

        whereDate('create_date', 'hello')->

        findOne(true);
    }

    public function testDayLessThan31()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Days can only be less than 31,but 40 given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect->table('test')->

        whereDay('create_date', 40)->

        findOne(true);
    }

    public function testMonthLessThan12()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Months can only be less than 12,but 13 given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect->table('test')->

        whereMonth('create_date', 13)->

        findOne(true);
    }

    public function testTimeTypeInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Time type `foo` is invalid.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect->table('test')->

        time('foo')->

        where('create_date', 5)->

        endTime()->

        findOne(true);
    }

    public function testTimeFlow()
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_at` = %d",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $time = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $time2 = $time + 1;
        $time3 = $time + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                time('month')->

                where('create_at', 5)->

                endTime()->

                elses()->

                time('day')->

                where('create_at', 5)->

                endTime()->

                endIfs()->

                findAll(true)
            ),
            sprintf($sql, $time),
            sprintf($sql, $time2),
            sprintf($sql, $time3)
        );
    }

    public function testTimeFlow2()
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_at` = %d",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $time = mktime(0, 0, 0, 5, 1, $date['year']);
        $time2 = $time + 1;
        $time3 = $time + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                ifs($condition)->

                time('month')->

                where('create_at', 5)->

                endTime()->

                elses()->

                time('day')->

                where('create_at', 5)->

                endTime()->

                endIfs()->

                findAll(true)
            ),
            sprintf($sql, $time),
            sprintf($sql, $time2),
            sprintf($sql, $time3)
        );
    }

    public function testEndTimeFlow()
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_at` = %d AND `test`.`create_at` = 6",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $time = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $time2 = $time + 1;
        $time3 = $time + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect->table('test')->

                time('day')->

                where('create_at', 5)->

                ifs($condition)->

                elses()->

                endTime()->

                endIfs()->

                where('create_at', 6)->

                endTime()->

                findAll(true)
            ),
            sprintf($sql, $time),
            sprintf($sql, $time2),
            sprintf($sql, $time3)
        );
    }

    public function testEndTimeFlow2()
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "SELECT `test`.* FROM `test` WHERE `test`.`create_at` = %d AND `test`.`create_at` = %d",
    [],
    false,
    null,
    null,
    []
]
eot;

        $date = getdate();
        $time = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $time2 = mktime(0, 0, 0, $date['mon'], 6, $date['year']);

        $this->assertSame(
            sprintf($sql, $time, $time2),
            $this->varJson(
                $connect->table('test')->

                time('day')->

                where('create_at', 5)->

                ifs($condition)->

                elses()->

                endTime()->

                endIfs()->

                where('create_at', 6)->

                endTime()->

                findAll(true)
            )
        );
    }
}
