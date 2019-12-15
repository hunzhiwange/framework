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
 *     zh-CN:title="查询一列数据.list",
 *     path="database/read/list",
 *     description="",
 * )
 */
class ListTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="list 查询基础用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name` FROM `test`",
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
                    ->list('name')
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段逗号分隔",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testStringByCommaSeparation(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
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
                    ->list('name,id'),
                1
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段多个字符串",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testMoreString(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
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
                    ->list('name', 'id'),
                2
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段数组",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testArray(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
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
                    ->list(['name', 'id']),
                3
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="list 查询字段数组和字符串混合",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testArrayAndString(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`id` FROM `test`",
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
                    ->list(['name'], 'id'),
                4
            )
        );
    }
}
