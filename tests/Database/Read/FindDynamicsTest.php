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

namespace Tests\Database\Read;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * findDynamics test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.22
 *
 * @version 1.0
 */
class FindDynamicsTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 0,10",
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
                $connect->sql()->

                table('test')->

                find10()
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` LIMIT 3,10",
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
                $connect->sql()->

                table('test')->

                find10start3(),
                1
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`user_name` = '1111' LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findByUserName('1111'),
                2
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`UserName` = '1111' LIMIT 1",
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
                $connect->sql()->

                table('test')->

                findByUserName_('1111'),
                3
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`user_name` = '1111' AND `test`.`sex` = '222'",
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
                $connect->sql()->

                table('test')->

                findAllByUserNameAndSex('1111', '222'),
                4
            )
        );

        $sql = <<<'eot'
            [
                "SELECT `test`.* FROM `test` WHERE `test`.`UserName` = '1111' AND `test`.`Sex` = '222'",
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
                $connect->sql()->

                table('test')->

                findAllByUserNameAndSex_('1111', '222'),
                5
            )
        );
    }
}
