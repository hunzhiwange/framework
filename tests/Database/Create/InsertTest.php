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

namespace Tests\Database\Create;

use Tests\Database\DatabaseTestCase as TestCase;

/**
 * insert test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.23
 *
 * @version 1.0
 */
class InsertTest extends TestCase
{
    public function testBaseUse()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value)",
    {
        "name": [
            "小鸭子",
            2
        ],
        "value": [
            "吃饭饭",
            2
        ]
    }
]
eot;

        $data = ['name' => '小鸭子', 'value' => '吃饭饭'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                insert($data)
            )
        );
    }

    public function testBind()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:questionmark_0)",
    {
        "name": [
            "小鸭子",
            2
        ],
        "questionmark_0": [
            "吃肉",
            2
        ]
    }
]
eot;

        $data = ['name' => '小鸭子', 'value' => '[?]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                insert($data, ['吃肉'])
            )
        );

        $sql = <<<'eot'
[
    "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value)",
    {
        "name": [
            "小鸭子",
            2
        ],
        "value": "呱呱呱"
    }
]
eot;

        $data = ['name' => '小鸭子', 'value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                insert($data, ['value' => '呱呱呱']),
                1
            )
        );
    }

    public function testWithBindFunction()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "INSERT INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:questionmark_0)",
    {
        "name": [
            "小鸭子",
            2
        ],
        "questionmark_0": [
            "吃鱼",
            2
        ]
    }
]
eot;

        $data = ['name' => '小鸭子', 'value' => '[?]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                bind(['吃鱼'])->

                insert($data)
            )
        );
    }

    public function testReplace()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "REPLACE INTO `test` (`test`.`name`,`test`.`value`) VALUES (:name,:value)",
    {
        "name": [
            "小鸭子",
            2
        ],
        "value": "呱呱呱"
    }
]
eot;

        $data = ['name' => '小鸭子', 'value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                insert($data, ['value' => '呱呱呱'], true)
            )
        );
    }

    public function testInsertSupportTable()
    {
        $connect = $this->createDatabaseConnectMock();

        $sql = <<<'eot'
[
    "REPLACE INTO `test` (`test`.`name`,`test`.`test`.`value`) VALUES (:name,:value)",
    {
        "name": [
            "小鸭子",
            2
        ],
        "value": "呱呱呱"
    }
]
eot;

        $data = ['name' => '小鸭子', 'test.value' => '[:value]'];

        $this->assertSame(
            $sql,
            $this->varJson(
                $connect->sql()->

                table('test')->

                insert($data, ['value' => '呱呱呱'], true)
            )
        );
    }
}
