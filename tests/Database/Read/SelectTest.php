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
 * select test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.21
 *
 * @version 1.0
 *
 * @api(
 *     zh-CN:title="查询数据.select",
 *     path="database/read/select",
 *     description="",
 * )
 */
class SelectTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="select 查询指定 SQL",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "select *from test where id = ?",
                [
                    1
                ]
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->table('test')
                    ->select('select *from test where id = ?', [1])
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="select 直接查询",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSelect(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`",
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
                    ->table('test')
                    ->select(),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="select 查询支持闭包",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSelectClosure(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 1",
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
                    ->table('test')
                    ->select(function ($select) {
                        $select->where('id', 1);
                    }),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="select 查询支持 \Leevel\Database\Select 对象",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testSelectObject(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`id` = 5",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $select = $connect
            ->table('test')
            ->where('id', 5);

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->sql()
                    ->select($select),
                3
            )
        );
    }
}
