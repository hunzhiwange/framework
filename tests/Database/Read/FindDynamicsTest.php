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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * @api(
 *     zh-CN:title="动态查询.find.findStart.findBy.findAllBy",
 *     path="database/read/finddynamics",
 *     description="",
 * )
 */
class FindDynamicsTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="find[0-9] 查询指定条数数据",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 0,10",
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
                    ->find10()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="find[0-9]start[0-9] 查询指定开始位置指定条数数据",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testFindStart(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` LIMIT 3,10",
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
                    ->find10start3(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findBy 字段条件查询单条数据",
     *     zh-CN:description="方法遵循驼峰法，相应的字段为下划线。",
     *     note="",
     * )
     */
    public function testFindByField(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`user_name` = :__test_query__user_name LIMIT 1",
                {
                    "__test_query__user_name": [
                        "'1111'",
                        2
                    ]
                },
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
                    ->findByUserName('1111'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findBy 字段条件查询单条数据，字段保持原样",
     *     zh-CN:description="方法遵循驼峰法，尾巴加一个下划线 `_`，相应的字段保持原样。",
     *     note="",
     * )
     */
    public function testFindByFieldWithoutCamelize(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`UserName` = :__test_query__UserName LIMIT 1",
                {
                    "__test_query__UserName": [
                        "'1111'",
                        2
                    ]
                },
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
                    ->findByUserName_('1111'),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findAllBy 字段条件查询多条数据，字段保持原样",
     *     zh-CN:description="方法遵循驼峰法，相应的字段为下划线。",
     *     note="",
     * )
     */
    public function testTestfindAllByField(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`user_name` = :__test_query__user_name AND `test_query`.`sex` = :__test_query__sex",
                {
                    "__test_query__user_name": [
                        "'1111'",
                        2
                    ],
                    "__test_query__sex": [
                        "'222'",
                        2
                    ]
                },
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
                    ->findAllByUserNameAndSex('1111', '222'),
                4
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="findAllBy 字段条件查询多条数据，字段保持原样",
     *     zh-CN:description="方法遵循驼峰法，尾巴加一个下划线 `_`，相应的字段保持原样。",
     *     note="",
     * )
     */
    public function testTestfindAllByFieldWithoutCamelize(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` WHERE `test_query`.`UserName` = :__test_query__UserName AND `test_query`.`Sex` = :__test_query__Sex",
                {
                    "__test_query__UserName": [
                        "'1111'",
                        2
                    ],
                    "__test_query__Sex": [
                        "'222'",
                        2
                    ]
                },
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
                    ->findAllByUserNameAndSex_('1111', '222'),
                5
            )
        );
    }
}
