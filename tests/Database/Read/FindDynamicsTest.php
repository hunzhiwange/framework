<?php

declare(strict_types=1);

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="动态查询.find.findStart.findBy.findAllBy",
 *     path="database/read/finddynamics",
 *     zh-CN:description="",
 * )
 */
final class FindDynamicsTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="find[0-9] 查询指定条数数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 0,10",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->find10(),
                $connect
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="find[0-9]start[0-9] 查询指定开始位置指定条数数据",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testFindStart(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 3,10",
                [],
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->find10start3(),
                $connect,
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findBy 字段条件查询单条数据",
     *     zh-CN:description="方法遵循驼峰法，相应的字段为下划线。",
     *     zh-CN:note="",
     * )
     */
    public function testFindByField(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`user_name` = :test_query_user_name LIMIT 1",
                {
                    "test_query_user_name": [
                        "1111"
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findByUserName('1111'),
                $connect,
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findBy 字段条件查询单条数据，字段保持原样",
     *     zh-CN:description="方法遵循驼峰法，尾巴加一个下划线 `_`，相应的字段保持原样。",
     *     zh-CN:note="",
     * )
     */
    public function testFindByFieldWithoutCamelize(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`UserName` = :test_query_UserName LIMIT 1",
                {
                    "test_query_UserName": [
                        "1111"
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findByUserName_('1111'),
                $connect,
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findAllBy 字段条件查询多条数据，字段保持原样",
     *     zh-CN:description="方法遵循驼峰法，相应的字段为下划线。",
     *     zh-CN:note="",
     * )
     */
    public function testTestfindAllByField(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`user_name` = :test_query_user_name AND `test_query`.`sex` = :test_query_sex",
                {
                    "test_query_user_name": [
                        "1111"
                    ],
                    "test_query_sex": [
                        "222"
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findAllByUserNameAndSex('1111', '222'),
                $connect,
                4
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findAllBy 字段条件查询多条数据，字段保持原样",
     *     zh-CN:description="方法遵循驼峰法，尾巴加一个下划线 `_`，相应的字段保持原样。",
     *     zh-CN:note="",
     * )
     */
    public function testTestfindAllByFieldWithoutCamelize(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`UserName` = :test_query_UserName AND `test_query`.`Sex` = :test_query_Sex",
                {
                    "test_query_UserName": [
                        "1111"
                    ],
                    "test_query_Sex": [
                        "222"
                    ]
                },
                false
            ]
            eot;

        static::assertSame(
            $sql,
            $this->varJsonSql(
                $connect->table('test_query')
                    ->findAllByUserNameAndSex_('1111', '222'),
                $connect,
                5
            )
        );
    }
}
