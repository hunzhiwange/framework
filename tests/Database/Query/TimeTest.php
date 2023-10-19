<?php

declare(strict_types=1);

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

#[Api([
    'title' => 'Query lang.whereDate',
    'zh-CN:title' => '查询语言.whereDate',
    'path' => 'database/query/wheredate',
])]
/**
 * @internal
 */
final class TimeTest extends TestCase
{
    #[Api([
        'zh-CN:title' => 'whereDate 时间查询',
    ])]
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereDate('create_date', '+5 month')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'whereDay 时间查询',
    ])]
    public function testWhereDay(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereDay('create_date', 5)
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereDataFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->if($condition)
                    ->whereDate('create_date', '+5 month')
                    ->else()
                    ->whereDate('create_date', '+6 month')
                    ->fi()
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereDataFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->if($condition)
                    ->whereDate('create_date', '+5 month')
                    ->else()
                    ->whereDate('create_date', '+6 month')
                    ->fi()
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereDayWillFormatInt(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereDay('create_date', '5')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereDayWillFormatInt2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereDay('create_date', '5 foo')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'whereMonth 时间查询',
    ])]
    public function testWhereMonth(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereMonth('create_date', 5)
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereMonthFormatInt(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereMonth('create_date', '5')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereMonthFormatInt2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereMonth('create_date', '5 foo')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'whereYear 时间查询',
    ])]
    public function testWhereYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereYear('create_date', 2018)
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereYearFormatYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereYear('create_date', '2018')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    public function testWhereYearFormatYear2(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->whereYear('create_date', '2018 foo')
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $value),
            sprintf($sql, $value2),
            sprintf($sql, $value3)
        );
    }

    #[Api([
        'zh-CN:title' => 'time().where.endTime 时间查询，等价于 whereDate',
    ])]
    public function testTime(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->time()
                    ->where('create_date', '+5 month')
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
        'zh-CN:title' => 'time(date).where.endTime 时间查询，等价于 whereDate',
    ])]
    public function testTimeDateIsDefault(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->time('date')
                    ->where('create_date', '+5 month')
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
        'zh-CN:title' => 'time(day).where.endTime 时间查询，等价于 whereDay',
    ])]
    public function testTimeDay(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->time('day')
                    ->where('create_date', 5)
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
        'zh-CN:title' => 'time(month).where.endTime 时间查询，等价于 whereMonth',
    ])]
    public function testTimeMonth(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->time('month')
                    ->where('create_date', 5)
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
        'zh-CN:title' => 'time(year).where.endTime 时间查询，等价于 whereYear',
    ])]
    public function testTimeYear(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->time('year')
                    ->where('create_date', 2018)
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
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_year` = :test_query_create_year AND `test_query`.`create_month` = :test_query_create_month AND `test_query`.`create_day` = :test_query_create_day AND `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                        ->time('year')
                        ->where('create_year', 2018)
                        ->endTime()
                        ->time('month')
                        ->whereMonth('create_month', 5)
                        ->endTime()
                        ->time('day')
                        ->where('create_day', 5)
                        ->endTime()
                        ->time('date')
                        ->where('create_date', '+5 month')
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

    public function testTimeMultiWithoutEndTime(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_year` = :test_query_create_year AND `test_query`.`create_month` = :test_query_create_month AND `test_query`.`create_day` = :test_query_create_day AND `test_query`.`create_date` = :test_query_create_date LIMIT 1",
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
                    ->time('year')
                    ->where('create_year', 2018)
                    ->time('month')
                    ->whereMonth('create_month', 5)
                    ->time('day')
                    ->where('create_day', 5)
                    ->time('date')
                    ->where('create_date', '+5 month')
                    ->endTime()
                    ->findOne(),
                $connect
            ),
            sprintf($sql, $year, $month, $day, $date),
            sprintf($sql, $year, $month, $day, $date2),
            sprintf($sql, $year, $month, $day, $date3)
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
            ->whereDate('create_date', 'hello')
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
            ->whereDay('create_date', 40)
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
            ->whereMonth('create_date', 13)
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
            ->time('foo')
            ->where('create_date', 5)
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
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_at` = :test_query_create_at",
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
                    ->if($condition)
                    ->time('month')
                    ->where('create_at', 5)
                    ->endTime()
                    ->else()
                    ->time('day')
                    ->where('create_at', 5)
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
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_at` = :test_query_create_at",
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
                    ->if($condition)
                    ->time('month')
                    ->where('create_at', 5)
                    ->endTime()
                    ->else()
                    ->time('day')
                    ->where('create_at', 5)
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
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_at` = :test_query_create_at AND `test_query`.`create_at` = :test_query_create_at_1",
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
                    ->time('day')
                    ->where('create_at', 5)
                    ->if($condition)
                    ->else()
                    ->endTime()
                    ->fi()
                    ->where('create_at', 6)
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
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`create_at` = :test_query_create_at AND `test_query`.`create_at` = :test_query_create_at_1",
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
        $time = mktime(0, 0, 0, $date['mon'], 5, $date['year']);
        $time2 = mktime(0, 0, 0, $date['mon'], 6, $date['year']);

        static::assertSame(
            sprintf($sql, $time, $time2),
            $this->varJsonSql(
                $connect
                    ->table('test_query')
                    ->time('day')
                    ->where('create_at', 5)
                    ->if($condition)
                    ->else()
                    ->endTime()
                    ->fi()
                    ->where('create_at', 6)
                    ->endTime()
                    ->findAll(),
                $connect
            )
        );
    }
}
