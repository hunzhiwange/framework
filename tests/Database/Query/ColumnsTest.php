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
 *
 * @api(
 *     title="查询语言.columns",
 *     path="database/query/columns",
 *     description="",
 * )
 */
class ColumnsTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="Columns 添加字段",
     *     zh-CN:description="字段条件用法和 table 中的字段用法一致，详细可以查看《查询语言.table》。",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="SetColumns 设置字段",
     *     description="清空原有字段，然后添加新的字段。",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="Columns 字段支持表达式",
     *     description="",
     *     note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="Columns 字段在连表中的查询",
     *     description="",
     *     note="",
     * )
     */
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
