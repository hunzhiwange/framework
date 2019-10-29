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
 * prefix test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.14
 *
 * @version 1.0
 *
 * @api(
 *     title="查询语言.prefix",
 *     path="database/query/prefix",
 *     description="",
 * )
 */
class PrefixTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="prefix 基础用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_CALC_FOUND_ROWS `test`.* FROM `test` WHERE `test`.`id` = 5",
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
                    ->table('test')
                    ->prefix('SQL_CALC_FOUND_ROWS')
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="prefix 示例用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSqlNoCache(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_NO_CACHE `test`.* FROM `test` WHERE `test`.`id` = 5",
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
                    ->table('test')
                    ->prefix('SQL_NO_CACHE')
                    ->where('id', '=', 5)
                    ->findAll(true),
                1
            )
        );
    }

    public function testFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_CALC_FOUND_ROWS `test`.* FROM `test`",
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
                    ->table('test')
                    ->if($condition)
                    ->prefix('SQL_NO_CACHE')
                    ->else()
                    ->prefix('SQL_CALC_FOUND_ROWS')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT SQL_NO_CACHE `test`.* FROM `test`",
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
                    ->table('test')
                    ->if($condition)
                    ->prefix('SQL_NO_CACHE')
                    ->else()
                    ->prefix('SQL_CALC_FOUND_ROWS')
                    ->fi()
                    ->findAll(true)
            )
        );
    }
}
