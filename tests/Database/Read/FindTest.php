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
 * read find test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.21
 *
 * @version 1.0
 *
 * @api(
 *     zh-CN:title="查询数据.find",
 *     path="database/read/find",
 *     description="",
 * )
 */
class FindTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="find 查询基础用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
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
                    ->find()
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="find 查询指定数量",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testFindLimit(): void
    {
        $connect = $this->createDatabaseConnectMock();
        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 0,5",
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
                    ->find(5),
                1
            )
        );
    }
}
