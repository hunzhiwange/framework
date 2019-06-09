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
 * columns test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.20
 *
 * @version 1.0
 */
class ColumnsTest extends TestCase
{
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`test`.`id`,`test`.`name`,`test`.`value` FROM `test`",
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
                    ->columns('id')
                    ->columns('name,value')
                    ->findAll(true)
            )
        );
    }

    public function testSetColumns(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`remark` FROM `test`",
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
                    ->columns('id')
                    ->columns('name,value')
                    ->setColumns('remark')
                    ->findAll(true)
            )
        );
    }

    public function testColumnsExpressionForSelectString(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                [
                    "SELECT 'foo'",
                    [],
                    false,
                    null,
                    null,
                    []
                ]
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                [
                    $connect
                        ->columns("{'foo'}")
                        ->findAll(true),
                ]
            )
        );
    }

    public function testColumnsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`test`.`name`,`test`.`value` FROM `test`",
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
                    ->columns('id')
                    ->else()
                    ->columns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testColumnsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.*,`test`.`id` FROM `test`",
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
                    ->columns('id')
                    ->else()
                    ->columns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testSetColumnsFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`value` FROM `test`",
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
                    ->setColumns('foo')
                    ->if($condition)
                    ->setColumns('id')
                    ->else()
                    ->setColumns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testSetColumnsFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`id` FROM `test`",
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
                    ->setColumns('foo')
                    ->if($condition)
                    ->setColumns('id')
                    ->else()
                    ->setColumns('name,value')
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testSetColumnsWithTableName(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`name`,`test`.`value`,`hello`.`name`,`hello`.`value` FROM `test` INNER JOIN `hello` ON `hello`.`name` = `test`.`name`",
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
                    ->setColumns('test.name,test.value')
                    ->join('hello', 'name,value', 'name', '=', '{[test.name]}')
                    ->findAll(true)
            )
        );
    }
}
