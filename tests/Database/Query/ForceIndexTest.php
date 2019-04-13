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
 * forceIndex test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.14
 *
 * @version 1.0
 */
class ForceIndexTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` FORCE INDEX(nameindex,statusindex) IGNORE INDEX(testindex) WHERE `test`.`id` = 5",
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
                $connect->table('test')->

                forceIndex('nameindex,statusindex')->

                ignoreIndex('testindex')->

                where('id', '=', 5)->

                findAll(true)
            )
        );
    }

    public function testForceIndex()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` FORCE INDEX(nameindex,statusindex) WHERE `test`.`id` = 2",
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
                $connect->table('test')->

                forceIndex(['nameindex', 'statusindex'])->

                where('id', '=', 2)->

                findAll(true)
            )
        );
    }

    public function testIgnoreIndex()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` IGNORE INDEX(nameindex,statusindex) WHERE `test`.`id` = 6",
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
                $connect->table('test')->

                ignoreIndex(['nameindex', 'statusindex'])->

                where('id', '=', 6)->

                findAll(true)
            )
        );
    }

    public function testForceIndexTypeNotSupported()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid Index type `NOT_SUPPORT`.'
        );

        $connect = $this->createDatabaseConnectMock();

        $connect->table('test')->

        forceIndex('foo', 'NOT_SUPPORT')->

        findAll(true);
    }

    public function testForceIndexFlow()
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` IGNORE INDEX(testindex) WHERE `test`.`id` = 5",
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
                $connect->table('test')->

                ifs($condition)->

                forceIndex('nameindex,statusindex')->

                elses()->

                ignoreIndex('testindex')->

                endIfs()->

                where('id', '=', 5)->

                findAll(true)
            )
        );
    }

    public function testForceIndexFlow2()
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` FORCE INDEX(nameindex,statusindex) WHERE `test`.`id` = 5",
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
                $connect->table('test')->

                ifs($condition)->

                forceIndex('nameindex,statusindex')->

                elses()->

                ignoreIndex('testindex')->

                endIfs()->

                where('id', '=', 5)->

                findAll(true)
            )
        );
    }
}
