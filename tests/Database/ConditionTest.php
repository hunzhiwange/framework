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

namespace Tests\Database;

use Leevel\Database\Condition;
use Tests\Database\DatabaseTestCase as TestCase;

class ConditionTest extends TestCase
{
    public function testForPage(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 114,6",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->table('test')
                    ->forPage(20, 6)
                    ->findAll(true)
            )
        );
    }

    public function testParseFormNotSet(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT 2",
                [],
                false
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect
                    ->setColumns(Condition::raw('2'))
                    ->findAll(true)
            )
        );
    }

    public function testMakeSql(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test`"
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                [
                    $connect
                        ->table('test')
                        ->makeSql(),
                ]
            )
        );
    }

    public function testMakeSqlWithLogicGroup(): void
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "(SELECT `test`.* FROM `test`)"
            ]
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                [
                    $connect
                        ->table('test')
                        ->makeSql(true),
                ]
            )
        );
    }
}
