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
 * havingTime test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.02
 *
 * @version 1.0
 */
class HavingTimeTest extends TestCase
{
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingDate('create_date', '+5 month')
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingDay(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingDay('create_date', 5)
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingDayWillFormatInt(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingDay('create_date', '5')
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingDayWillFormatInt2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingDay('create_date', '5 foo')
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingMonth(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingMonth('create_date', 5)
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingMonthFormatInt(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingMonth('create_date', '5')
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingMonthFormatInt2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingMonth('create_date', '5 foo')
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingYear('create_date', 2018)
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingYearFormatYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingYear('create_date', '2018')
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingYearFormatYear2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->havingYear('create_date', '2018 foo')
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTime(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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

        $this->assertTrue(
            in_array(
                $this->varJson(
                    $connect
                        ->table('test')
                        ->groupBy('create_date')
                        ->time()
                        ->having('create_date', '+5 month')
                        ->endTime()
                        ->findOne(true)
                ), [
                    sprintf($sql, $value),
                    sprintf($sql, $value2),
                    sprintf($sql, $value3),
                ], true
            )
        );
    }

    public function testTimeDateIsDefault(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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

        $this->assertTrue(
            in_array(
                $this->varJson(
                    $connect
                        ->table('test')
                        ->groupBy('create_date')
                        ->time('date')
                        ->having('create_date', '+5 month')
                        ->endTime()
                        ->findOne(true)
                ), [
                    sprintf($sql, $value),
                    sprintf($sql, $value2),
                    sprintf($sql, $value3),
                ], true)
        );
    }

    public function testTimeDay(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->time('day')
                    ->having('create_date', 5)
                    ->endTime()
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeMonth(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->time('month')
                    ->having('create_date', 5)
                    ->endTime()
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_date` = %d LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->time('year')
                    ->having('create_date', 2018)
                    ->endTime()
                    ->findOne(true)
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testTimeMulti(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_year` = %s AND `test`.`create_month` = %s AND `test`.`create_day` = %s AND `test`.`create_date` = %s LIMIT 1",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->time('year')
                    ->having('create_year', 2018)
                    ->endTime()
                    ->time('month')
                    ->havingMonth('create_month', 5)
                    ->endTime()
                    ->time('day')
                    ->having('create_day', 5)
                    ->endTime()
                    ->time('date')
                    ->having('create_date', '+5 month')
                    ->endTime()
                    ->findOne(true)
            ),
            sprintf($sql, $year, $month, $day, $date),
            sprintf($sql, $year, $month, $day, $date2),
            sprintf($sql, $year, $month, $day, $date3)
        );
    }

    public function testTimeMultiWithoutEndTime(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_year` = %s AND `test`.`create_month` = %s AND `test`.`create_day` = %s AND `test`.`create_date` = %s LIMIT 1",
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
                    $connect
                        ->table('test')
                        ->groupBy('create_date')
                        ->time('year')
                        ->having('create_year', 2018)
                        ->time('month')
                        ->havingMonth('create_month', 5)
                        ->time('day')
                        ->having('create_day', 5)
                        ->time('date')
                        ->having('create_date', '+5 month')
                        ->endTime()
                        ->findOne(true)
                ), [
                    sprintf($sql, $year, $month, $day, $date),
                    sprintf($sql, $year, $month, $day, $date2),
                    sprintf($sql, $year, $month, $day, $date3),
                ], true
            )
        );
    }

    public function testDateStrtotimeReturnFalse(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Please enter a right time of strtotime.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->groupBy('create_date')
            ->havingDate('create_date', 'hello')
            ->findOne(true);
    }

    public function testDayLessThan31(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Days can only be less than 31,but 40 given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->groupBy('create_date')
            ->havingDay('create_date', 40)
            ->findOne(true);
    }

    public function testMonthLessThan12(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Months can only be less than 12,but 13 given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->groupBy('create_date')
            ->havingMonth('create_date', 13)
            ->findOne(true);
    }

    public function testTimeTypeInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
           'Time type `foo` is invalid.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test')
            ->groupBy('create_date')
            ->time('foo')
            ->having('create_date', 5)
            ->endTime()
            ->findOne(true);
    }

    public function testTimeFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_at` = %d",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->if($condition)
                    ->time('month')
                    ->having('create_at', 5)
                    ->endTime()
                    ->else()
                    ->time('day')
                    ->having('create_at', 5)
                    ->endTime()
                    ->fi()
                    ->findAll(true)
            ),
            sprintf($sql, $time),
            sprintf($sql, $time2),
            sprintf($sql, $time3)
        );
    }

    public function testTimeFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_at` = %d",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->if($condition)
                    ->time('month')
                    ->having('create_at', 5)
                    ->endTime()
                    ->else()
                    ->time('day')
                    ->having('create_at', 5)
                    ->endTime()
                    ->fi()
                    ->findAll(true)
            ),
            sprintf($sql, $time),
            sprintf($sql, $time2),
            sprintf($sql, $time3)
        );
    }

    public function testEndTimeFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_at` = %d AND `test`.`create_at` = 6",
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
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->time('day')
                    ->having('create_at', 5)
                    ->if($condition)
                    ->else()
                    ->endTime()
                    ->fi()
                    ->having('create_at', 6)
                    ->endTime()
                    ->findAll(true)
            ),
            sprintf($sql, $time),
            sprintf($sql, $time2),
            sprintf($sql, $time3)
        );
    }

    public function testEndTimeFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` GROUP BY `test`.`create_date` HAVING `test`.`create_at` = %d AND `test`.`create_at` = %d",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $date = getdate();
        $time1 = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $time2 = mktime(0, 0, 0, $date['mon'], 6, $date['year']);
        $time12 = $time1 + 1;
        $time13 = $time1 + 2;
        $time22 = $time2 + 1;
        $time23 = $time2 + 2;

        $this->assertTimeRange(
            $this->varJson(
                $connect
                    ->table('test')
                    ->groupBy('create_date')
                    ->time('day')
                    ->having('create_at', 5)
                    ->if($condition)
                    ->else()
                    ->endTime()
                    ->fi()
                    ->having('create_at', 6)
                    ->endTime()
                    ->findAll(true)
            ),
            sprintf($sql, $time1, $time2),
            sprintf($sql, $time1, $time22),
            sprintf($sql, $time1, $time23),
            sprintf($sql, $time12, $time2),
            sprintf($sql, $time12, $time22),
            sprintf($sql, $time12, $time23),
            sprintf($sql, $time13, $time2),
            sprintf($sql, $time13, $time22),
            sprintf($sql, $time13, $time23)
        );
    }
}
