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

namespace Tests\Database\Query;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * forceIndex test.
 *
 * @api(
 *     title="Query lang.forceIndex",
 *     zh-CN:title="查询语言.forceIndex",
 *     path="database/query/forceindex",
 *     description="",
 * )
 */
class ForceIndexTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="forceIndex,ignoreIndex 基础用法",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(nameindex,statusindex) IGNORE INDEX(testindex) WHERE `test_query`.`id` = 5",
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
                    ->table('test_query')
                    ->forceIndex('nameindex,statusindex')
                    ->ignoreIndex('testindex')
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="forceIndex 数组支持",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testForceIndexWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(nameindex,statusindex) WHERE `test_query`.`id` = 2",
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
                    ->table('test_query')
                    ->forceIndex(['nameindex', 'statusindex'])
                    ->where('id', '=', 2)
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="ignoreIndex 数组支持",
     *     zh-CN:description="",
     *     note="",
     * )
     */
    public function testIgnoreIndexWithArray(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` IGNORE INDEX(nameindex,statusindex) WHERE `test_query`.`id` = 6",
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
                    ->table('test_query')
                    ->ignoreIndex(['nameindex', 'statusindex'])
                    ->where('id', '=', 6)
                    ->findAll(true)
            )
        );
    }

    public function testForceIndexTypeNotSupported(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid Index type `NOT_SUPPORT`.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect
            ->table('test_query')
            ->forceIndex('foo', 'NOT_SUPPORT')
            ->findAll(true);
    }

    public function testForceIndexFlow(): void
    {
        $condition = false;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` IGNORE INDEX(testindex) WHERE `test_query`.`id` = 5",
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
                    ->table('test_query')
                    ->if($condition)
                    ->forceIndex('nameindex,statusindex')
                    ->else()
                    ->ignoreIndex('testindex')
                    ->fi()
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }

    public function testForceIndexFlow2(): void
    {
        $condition = true;
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test_query`.* FROM `test_query` FORCE INDEX(nameindex,statusindex) WHERE `test_query`.`id` = 5",
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
                    ->table('test_query')
                    ->if($condition)
                    ->forceIndex('nameindex,statusindex')
                    ->else()
                    ->ignoreIndex('testindex')
                    ->fi()
                    ->where('id', '=', 5)
                    ->findAll(true)
            )
        );
    }
}
