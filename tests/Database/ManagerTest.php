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

namespace Tests\Database;

use PDO;
use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.10
 *
 * @version 1.0
 */
class ManagerTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->truncate('guest_book');
    }

    protected function tearDown()
    {
        $this->setUp();
    }

    public function testBaseUse()
    {
        $manager = $this->createManager();

        $data = ['name' => 'tom', 'content' => 'I love movie.'];

        $this->assertSame('1', $manager->
        table('guest_book')->
        insert($data));

        $result = $manager->table('guest_book', 'name,content')->

        where('id', 1)->

        findOne();

        $this->assertSame('tom', $result->name);
        $this->assertSame('I love movie.', $result->content);

        $this->truncate('guest_book');
    }

    public function testParseDatabaseOptionDistributedIsTrue()
    {
        $manager = $this->createManager();

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

    public function testParseDatabaseOptionDistributedIsTrue2()
    {
        $manager = $this->createManager();

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
}
