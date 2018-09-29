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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Database\Query;

use Tests\TestCase;

/**
 * distinct test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.18
 *
 * @version 1.0
 */
class QueryDistinctTest extends TestCase
{
    use Query;

    public function testBaseUse()
    {
        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT DISTINCT `test`.* FROM `test`",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJsonEncode(
                $connect->table('test')->

                distinct()->

                findAll(true),
                __FUNCTION__
            )
        );

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
            $this->varJsonEncode(
                $connect->table('test')->

                distinct()->

                distinct(false)->

                findAll(true),
                __FUNCTION__.'1'
            )
        );
    }

    public function testFlow()
    {
        $condition = false;

        $connect = $this->createConnect();

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
            $this->varJsonEncode(
                $connect->table('test')->

                ifs($condition)->

                distinct()->

                elses()->

                distinct(false)->

                endIfs()->

                findAll(true),
                __FUNCTION__
            )
        );
    }

    public function testFlow2()
    {
        $condition = true;

        $connect = $this->createConnect();

        $sql = <<<'eot'
[
    "SELECT DISTINCT `test`.* FROM `test`",
    [],
    false,
    null,
    null,
    []
]
eot;

        $this->assertSame(
            $sql,
            $this->varJsonEncode(
                $connect->table('test')->

                ifs($condition)->

                distinct()->

                elses()->

                distinct(false)->

                endIfs()->

                findAll(true),
                __FUNCTION__
            )
        );
    }
}
