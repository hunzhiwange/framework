<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.havingDate',
    'zh-CN:title' => '查询语言.havingDate',
    'path' => 'database/query/havingdate',
])]
final class HavingTimeTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'havingDate 时间查询',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = strtotime('+5 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingDate('create_date', '+5 month')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'havingDay 时间查询',
    ])]
    public function testHavingDay(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingDay('create_date', 5)
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingDataFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = strtotime('+6 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->if($condition)
                    ->havingDate('create_date', '+5 month')
                    ->else()
                    ->havingDate('create_date', '+6 month')
                    ->fi()
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testHavingDataFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = strtotime('+5 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->if($condition)
                    ->havingDate('create_date', '+5 month')
                    ->else()
                    ->havingDate('create_date', '+6 month')
                    ->fi()
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingDay('create_date', '5')
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingDay('create_date', '5 foo')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'havingMonth 时间查询',
    ])]
    public function testHavingMonth(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingMonth('create_date', 5)
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingMonth('create_date', '5')
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingMonth('create_date', '5 foo')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'havingYear 时间查询',
    ])]
    public function testHavingYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingYear('create_date', 2018)
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingYear('create_date', '2018')
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->havingYear('create_date', '2018 foo')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'time().having.endTime 时间查询，等价于 havingDate',
    ])]
    public function testTime(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = strtotime('+5 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        static::assertTrue(
            \in_array(
                $this->varJsonSql(
                    $connect
                        ->table('test_query')
                        ->setColumns('create_date')
                        ->groupBy('create_date')
                        ->time()
                        ->having('create_date', '+5 month')
                        ->endTime()
                        ->findOne(),
                    $connect
                ),
                [
                    sprintf($sql, $value),
                    sprintf($sql, $value2),
                    sprintf($sql, $value3),
                ],
                true
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'time(date).having.endTime 时间查询，等价于 havingDate',
    ])]
    public function testTimeDateIsDefault(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = strtotime('+5 month');
        $value2 = $value + 1;
        $value3 = $value + 2;

        static::assertTrue(
            \in_array(
                $this->varJsonSql(
                    $connect
                        ->table('test_query')
                        ->setColumns('create_date')
                        ->groupBy('create_date')
                        ->time('date')
                        ->having('create_date', '+5 month')
                        ->endTime()
                        ->findOne(),
                    $connect
                ),
                [
                    sprintf($sql, $value),
                    sprintf($sql, $value2),
                    sprintf($sql, $value3),
                ],
                true
            )
        );
    }

    #[Api([
        'zh-CN:title' => 'time(day).having.endTime 时间查询，等价于 havingDay',
    ])]
    public function testTimeDay(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->time('day')
                    ->having('create_date', 5)
                    ->endTime()
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'time(month).having.endTime 时间查询，等价于 havingMonth',
    ])]
    public function testTimeMonth(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $value = mktime(0, 0, 0, 5, 1, $date['year']);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->time('month')
                    ->having('create_date', 5)
                    ->endTime()
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'time(year).having.endTime 时间查询，等价于 havingYear',
    ])]
    public function testTimeYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date` FROM `test_query` GROUP BY `test_query`.`create_date` HAVING `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $value = mktime(0, 0, 0, 1, 1, 2018);
        $value2 = $value + 1;
        $value3 = $value + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date')
                    ->groupBy('create_date')
                    ->time('year')
                    ->having('create_date', 2018)
                    ->endTime()
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date`,`test_query`.`create_month`,`test_query`.`create_day`,`test_query`.`create_year` FROM `test_query` GROUP BY `test_query`.`create_date`,`test_query`.`create_month`,`test_query`.`create_day`,`test_query`.`create_year` HAVING `test_query`.`create_year` = :test_query_create_year AND `test_query`.`create_month` = :test_query_create_month AND `test_query`.`create_day` = :test_query_create_day AND `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_year": [
                        %d
                    ],
                    "test_query_create_month": [
                        %d
                    ],
                    "test_query_create_day": [
                        %d
                    ],
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
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
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date,create_month,create_day,create_year')
                    ->groupBy('create_date,create_month,create_day,create_year')
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
                    ->findOne(),
                $connect
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
                "SELECT `test_query`.`create_date`,`test_query`.`create_month`,`test_query`.`create_day`,`test_query`.`create_year` FROM `test_query` GROUP BY `test_query`.`create_date`,`test_query`.`create_month`,`test_query`.`create_day`,`test_query`.`create_year` HAVING `test_query`.`create_year` = :test_query_create_year AND `test_query`.`create_month` = :test_query_create_month AND `test_query`.`create_day` = :test_query_create_day AND `test_query`.`create_date` = :test_query_create_date LIMIT 1",
                {
                    "test_query_create_year": [
                        %d
                    ],
                    "test_query_create_month": [
                        %d
                    ],
                    "test_query_create_day": [
                        %d
                    ],
                    "test_query_create_date": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $year = mktime(0, 0, 0, 1, 1, 2018);
        $month = mktime(0, 0, 0, 5, 1, $date['year']);
        $day = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $date = strtotime('+5 month');
        $date2 = $date + 1;
        $date3 = $date + 2;

        static::assertTrue(
            \in_array(
                $this->varJsonSql(
                    $connect
                        ->table('test_query')
                        ->setColumns('create_date,create_month,create_day,create_year')
                        ->groupBy('create_date,create_month,create_day,create_year')
                        ->time('year')
                        ->having('create_year', 2018)
                        ->time('month')
                        ->havingMonth('create_month', 5)
                        ->time('day')
                        ->having('create_day', 5)
                        ->time('date')
                        ->having('create_date', '+5 month')
                        ->endTime()
                        ->findOne(),
                    $connect
                ),
                [
                    sprintf($sql, $year, $month, $day, $date),
                    sprintf($sql, $year, $month, $day, $date2),
                    sprintf($sql, $year, $month, $day, $date3),
                ],
                true
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
            ->table('test_query')
            ->groupBy('create_date')
            ->havingDate('create_date', 'hello')
            ->findOne()
        ;
    }

    public function testDayLessThan31(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Days can only be less than 31,but 40 given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->groupBy('create_date')
            ->havingDay('create_date', 40)
            ->findOne()
        ;
    }

    public function testMonthLessThan12(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Months can only be less than 12,but 13 given.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->groupBy('create_date')
            ->havingMonth('create_date', 13)
            ->findOne()
        ;
    }

    public function testTimeTypeInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Time type `foo` is invalid.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->groupBy('create_date')
            ->time('foo')
            ->having('create_date', 5)
            ->endTime()
            ->findOne()
        ;
    }

    public function testTimeFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.`create_date`,`test_query`.`create_at` FROM `test_query` GROUP BY `test_query`.`create_date`,`test_query`.`create_at` HAVING `test_query`.`create_at` = :test_query_create_at",
                {
                    "test_query_create_at": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $time = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $time2 = $time + 1;
        $time3 = $time + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date,create_at')
                    ->groupBy('create_date,create_at')
                    ->if($condition)
                    ->time('month')
                    ->having('create_at', 5)
                    ->endTime()
                    ->else()
                    ->time('day')
                    ->having('create_at', 5)
                    ->endTime()
                    ->fi()
                    ->findAll(),
                $connect
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
                "SELECT `test_query`.`create_date`,`test_query`.`create_at` FROM `test_query` GROUP BY `test_query`.`create_date`,`test_query`.`create_at` HAVING `test_query`.`create_at` = :test_query_create_at",
                {
                    "test_query_create_at": [
                        %d
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $time = mktime(0, 0, 0, 5, 1, $date['year']);
        $time2 = $time + 1;
        $time3 = $time + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date,create_at')
                    ->groupBy('create_date,create_at')
                    ->if($condition)
                    ->time('month')
                    ->having('create_at', 5)
                    ->endTime()
                    ->else()
                    ->time('day')
                    ->having('create_at', 5)
                    ->endTime()
                    ->fi()
                    ->findAll(),
                $connect
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
                "SELECT `test_query`.`create_date`,`test_query`.`create_at` FROM `test_query` GROUP BY `test_query`.`create_date`,`test_query`.`create_at` HAVING `test_query`.`create_at` = :test_query_create_at AND `test_query`.`create_at` = :test_query_create_at_1",
                {
                    "test_query_create_at": [
                        %d
                    ],
                    "test_query_create_at_1": [
                        6
                    ]
                },
                false
            ]
            eot;

        $date = getdate();
        $time = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $time2 = $time + 1;
        $time3 = $time + 2;

        $this->assertTimeRange(
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date,create_at')
                    ->groupBy('create_date,create_at')
                    ->time('day')
                    ->having('create_at', 5)
                    ->if($condition)
                    ->else()
                    ->endTime()
                    ->fi()
                    ->having('create_at', 6)
                    ->endTime()
                    ->findAll(),
                $connect
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
                "SELECT `test_query`.`create_date`,`test_query`.`create_at` FROM `test_query` GROUP BY `test_query`.`create_date`,`test_query`.`create_at` HAVING `test_query`.`create_at` = :test_query_create_at AND `test_query`.`create_at` = :test_query_create_at_1",
                {
                    "test_query_create_at": [
                        %d
                    ],
                    "test_query_create_at_1": [
                        %d
                    ]
                },
                false
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
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->setColumns('create_date,create_at')
                    ->groupBy('create_date,create_at')
                    ->time('day')
                    ->having('create_at', 5)
                    ->if($condition)
                    ->else()
                    ->endTime()
                    ->fi()
                    ->having('create_at', 6)
                    ->endTime()
                    ->findAll(),
                $connect
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
