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
 * union test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 *
 * @api(
 *     title="查询语言.union",
 *     path="database/query/union",
 *     description="",
 * )
 */
class UnionTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="Union 联合查询基本用法",
     *     description="",
     *     note="参数支持字符串、子查询器以及它们构成的一维数组。",
     * )
     */
    public function testBaseUse(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION SELECT `yyyyy`.`yid` AS `id`,`yyyyy`.`name` AS `value` FROM `yyyyy` WHERE `yyyyy`.`first_name` = '222'\nUNION SELECT id,value FROM test2\nUNION SELECT `yyyyy`.`yid` AS `id`,`yyyyy`.`name` AS `value` FROM `yyyyy` WHERE `yyyyy`.`first_name` = '222'",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $union1 = $connect
            ->table('yyyyy', 'yid as id,name as value')
            ->where('first_name', '=', '222');
        $union2 = 'SELECT id,value FROM test2';

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->union($union1)
                    ->union($union2)
                    ->union($union1)
                    ->findAll(true)
            )
        );

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->union([$union1, $union2, $union1])
                    ->findAll(true)
            )
        );
    }

    /**
     * @api(
     *     zh-CN:title="UnionAll 联合查询不去重",
     *     description="",
     *     note="",
     * )
     */
    public function testUnionAll(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION ALL SELECT id,value FROM test2",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $union1 = 'SELECT id,value FROM test2';

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->unionAll($union1)
                    ->findAll(true)
            )
        );
    }

    public function testUnionFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION SELECT id,value FROM test3",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $union1 = 'SELECT id,value FROM test2';
        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->if($condition)
                    ->union($union1)
                    ->else()
                    ->union($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION SELECT id,value FROM test2",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $union1 = 'SELECT id,value FROM test2';
        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->if($condition)
                    ->union($union1)
                    ->else()
                    ->union($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionAllFlow(): void
    {
        $condition = false;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION ALL SELECT id,value FROM test3",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $union1 = 'SELECT id,value FROM test2';
        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->if($condition)
                    ->unionAll($union1)
                    ->else()
                    ->unionAll($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionAllFlow2(): void
    {
        $condition = true;

        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` \nUNION ALL SELECT id,value FROM test2",
                [],
                false,
                null,
                null,
                []
            ]
            eot;

        $union1 = 'SELECT id,value FROM test2';
        $union2 = 'SELECT id,value FROM test3';

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test', 'tid as id,tname as value')
                    ->if($condition)
                    ->unionAll($union1)
                    ->else()
                    ->unionAll($union2)
                    ->fi()
                    ->findAll(true)
            )
        );
    }

    public function testUnionNotSupportType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Invalid UNION type `NOT FOUND`.'
        );

        $connect = $this->createDatabaseConnectMock();
        $union1 = 'SELECT id,value FROM test2';

        $connect
            ->table('test', 'tid as id,tname as value')
            ->union($union1, 'NOT FOUND')
            ->findAll(true);
    }
}
