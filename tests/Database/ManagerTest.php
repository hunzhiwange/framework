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

namespace Tests\Database;

use PDO;
use Tests\Database\DatabaseTestCase as TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.10
 *
 * @version 1.0
 *
 * @api(
 *     title="数据库配置",
 *     path="database/config",
 *     description="我们可以在 `option/database.php` 文件中定义数据库连接。",
 * )
 */
class ManagerTest extends TestCase
{
    /**
     * @api(
     *     title="基本配置",
     *     description="数据库配置基本定义功能展示。
     *
     * `数据库配置`
     *
     * ``` php
     * ".\Leevel\Leevel\Utils\Doc::getMethodBody(\Tests\Database::class, 'createDatabaseManager')."
     * ```
     *
     * 请使用这样的格式来定义连接，系统会自动帮你访问数据库。
     * 系统底层实质上会使用 `\Leevel\Option\Option` 来管理配置信息。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse()
    {
        $manager = $this->createDatabaseManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1',
            $manager
                ->table('guest_book')
                ->insert($data)
        );

        $result = $manager->table('guest_book', 'name,content')
            ->where('id', 1)
            ->findOne();

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);
    }

    /**
     * @api(
     *     title="数据库主从设置",
     *     description="QueryPHP 允许用户一个主数据库作为写入、更新以及删除,外加多个附属从数据库作为只读数据库来共同提供数据库服务。
     * 多个数据库需要需要开启 `distributed`，而 `separate` 主要用于读写分离。
     * `master` 为主数据库，`slave` 为附属从数据库设置。
     * ",
     *     note="",
     * )
     */
    public function testParseDatabaseOptionDistributedIsTrue()
    {
        $manager = $this->createDatabaseManager();

        $option = [
            'driver'   => 'mysql',
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'name'     => 'test',
            'user'     => 'root',
            'password' => '123456',
            'charset'  => 'utf8',
            'options'  => [
                PDO::ATTR_PERSISTENT => false,
            ],
            'separate'           => false,
            'distributed'        => true,
            'master'             => [],
            'slave'              => ['host' => '127.0.0.1'],
        ];

        $optionNew = $this->invokeTestMethod($manager, 'parseDatabaseOption', [$option]);

        $data = <<<'eot'
            {
                "driver": "mysql",
                "separate": false,
                "distributed": true,
                "master": {
                    "host": "127.0.0.1",
                    "port": 3306,
                    "name": "test",
                    "user": "root",
                    "password": "123456",
                    "charset": "utf8",
                    "options": {
                        "12": false
                    }
                },
                "slave": [
                    {
                        "host": "127.0.0.1",
                        "port": 3306,
                        "name": "test",
                        "user": "root",
                        "password": "123456",
                        "charset": "utf8",
                        "options": {
                            "12": false
                        }
                    }
                ]
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson($optionNew)
        );
    }

    /**
     * @api(
     *     title="附属从数据库支持二维数组",
     *     description="从数据库支持多个，支持二维数组",
     *     note="",
     * )
     */
    public function testParseDatabaseOptionDistributedIsTrueWithTwoDimensionalArray()
    {
        $manager = $this->createDatabaseManager();

        $option = [
            'driver'   => 'mysql',
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'name'     => 'test',
            'user'     => 'root',
            'password' => '123456',
            'charset'  => 'utf8',
            'options'  => [
                PDO::ATTR_PERSISTENT => false,
            ],
            'separate'           => false,
            'distributed'        => true,
            'master'             => [],
            'slave'              => [
                ['host' => '127.0.0.1'],
                ['password' => '123456'],
            ],
        ];

        $optionNew = $this->invokeTestMethod($manager, 'parseDatabaseOption', [$option]);

        $data = <<<'eot'
            {
                "driver": "mysql",
                "separate": false,
                "distributed": true,
                "master": {
                    "host": "127.0.0.1",
                    "port": 3306,
                    "name": "test",
                    "user": "root",
                    "password": "123456",
                    "charset": "utf8",
                    "options": {
                        "12": false
                    }
                },
                "slave": [
                    {
                        "host": "127.0.0.1",
                        "port": 3306,
                        "name": "test",
                        "user": "root",
                        "password": "123456",
                        "charset": "utf8",
                        "options": {
                            "12": false
                        }
                    },
                    {
                        "password": "123456",
                        "host": "127.0.0.1",
                        "port": 3306,
                        "name": "test",
                        "user": "root",
                        "charset": "utf8",
                        "options": {
                            "12": false
                        }
                    }
                ]
            }
            eot;

        $this->assertSame(
            $data,
            $this->varJson($optionNew)
        );
    }

    /**
     * @api(
     *     title="数据库设置只支持数组",
     *     description="数据库主从连接只支持数组。",
     *     note="",
     * )
     */
    public function testParseDatabaseOptionMasterAndSlaveMustBeAnArray()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Database option `slave` must be an array.'
        );

        $manager = $this->createDatabaseManager();

        $option = [
            'driver'   => 'mysql',
            'host'     => '127.0.0.1',
            'port'     => 3306,
            'name'     => 'test',
            'user'     => 'root',
            'password' => '123456',
            'charset'  => 'utf8',
            'options'  => [
                PDO::ATTR_PERSISTENT => false,
            ],
            'separate'           => false,
            'distributed'        => true,
            'master'             => [],
            'slave'              => 'notarray',
        ];

        $this->invokeTestMethod($manager, 'parseDatabaseOption', [$option]);
    }

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}
