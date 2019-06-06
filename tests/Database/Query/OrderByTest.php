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
 * orderBy test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 */
class OrderByTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`id` DESC,`test`.`name` ASC",
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
                    ->table('test', 'tid as id,tname as value')
                    ->orderBy('id DESC')
                    ->orderBy('name')
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`id` DESC",
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
                    ->table('test', 'tid as id,tname as value')
                    ->orderBy('test.id DESC')
                    ->findAll(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY SUM(`test`.`num`) ASC",
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
                    ->table('test', 'tid as id,tname as value')
                    ->orderBy('{SUM([num]) ASC}')
                    ->findAll(true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY SUM(`test`.`num`) ASC",
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
                    ->table('test', 'tid as id,tname as value')
                    ->orderBy('{SUM([num]) ASC}')
                    ->findAll(true),
                3
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`title` ASC,`test`.`id` ASC,concat('1234',`test`.`id`,'ttt') DESC",
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
                    ->table('test', 'tid as id,tname as value')
                    ->orderBy("title,id,{concat('1234',[id],'ttt') desc}")
                    ->findAll(true),
                4
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`title` ASC,`test`.`id` ASC,`test`.`ttt` ASC,`test`.`value` DESC",
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
                    ->table('test', 'tid as id,tname as value')
                    ->orderBy(['title,id,ttt', 'value desc'])
                    ->findAll(true),
                5
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.`tid` AS `id`,`test`.`tname` AS `value` FROM `test` ORDER BY `test`.`title` DESC,`test`.`id` DESC,`test`.`ttt` ASC,`test`.`value` DESC",
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
                    ->table('test', 'tid as id,tname as value')
                    ->orderBy(['title,id,ttt asc', 'value'], 'desc')
                    ->findAll(true),
                6
            )
        );
    }

    public function testLatestOrOldest()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` ORDER BY `test`.`create_at` DESC",
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
                    ->latest()
                    ->findAll(true)
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` ORDER BY `test`.`foo` DESC",
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
                    ->latest('foo')
                    ->findAll(true),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` ORDER BY `test`.`create_at` ASC",
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
                    ->oldest()
                    ->findAll(true),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` ORDER BY `test`.`bar` ASC",
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
                    ->oldest('bar')
                    ->findAll(true),
                3
            )
        );
    }

    public function testOrderByExpressionNotSetWithDefaultAsc()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` ORDER BY foo ASC",
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
                    ->orderBy('{foo}')
                    ->findAll(true)
            )
        );
    }
}
