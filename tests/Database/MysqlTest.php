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

use Leevel\Database\Mysql;
use Tests\Database\Query\Query;
use Tests\TestCase;

/**
 * mysql test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.10.10
 *
 * @version 1.0
 */
class MysqlTest extends TestCase
{
    use Query;

    protected function setUp()
    {
        $this->truncate('guestbook');
    }

    protected function tearDown()
    {
        $this->setUp();
    }

    public function testGetTableNames()
    {
        $connect = $this->createConnectTest();

        $result = $connect->tableNames('test');

        $this->assertTrue(in_array('guestbook', $result, true));
    }

    public function testGetTableColumns()
    {
        $connect = $this->createConnectTest();

        $result = $connect->tableColumns('guestbook');

        $sql = <<<'eot'
{
    "list": {
        "id": {
            "name": "id",
            "type": "int",
            "length": "int",
            "primary_key": true,
            "auto_increment": true,
            "default": null
        },
        "name": {
            "name": "name",
            "type": "varchar",
            "length": "varchar",
            "primary_key": false,
            "auto_increment": false,
            "default": null
        },
        "content": {
            "name": "content",
            "type": "text",
            "length": null,
            "primary_key": false,
            "auto_increment": false,
            "default": null
        },
        "create_at": {
            "name": "create_at",
            "type": "timestamp",
            "length": null,
            "primary_key": false,
            "auto_increment": false,
            "default": "CURRENT_TIMESTAMP"
        }
    },
    "primary_key": [
        "id"
    ],
    "auto_increment": "id"
}
eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );

        $this->truncate('guestbook');
    }

    public function testLimitCount()
    {
        $mysql = new Mysql([]);

        $this->assertSame('', $mysql->limitCount());
        $this->assertSame('LIMIT 5,999999999999', $mysql->limitCount(null, 5));
        $this->assertSame('LIMIT 5', $mysql->limitCount(5, null));
        $this->assertSame('LIMIT 5,5', $mysql->limitCount(5, 5));
    }

    public function testParsePort()
    {
        $mysql = new Mysql([]);

        $result = $this->invokeTestMethod($mysql, 'parsePort', [['port' => '']]);

        $this->assertSame('', $result);
    }

    public function testParseSocket()
    {
        $mysql = new Mysql([]);

        $result = $this->invokeTestMethod($mysql, 'parseSocket', [['socket' => '']]);

        $this->assertSame('', $result);
    }

    public function testParseSocket2()
    {
        $mysql = new Mysql([]);

        $result = $this->invokeTestMethod($mysql, 'parseSocket', [['socket' => '/var/lib/mysql/mysql.sock']]);

        $this->assertSame(';unix_socket=/var/lib/mysql/mysql.sock', $result);
    }

    public function testParseCharset()
    {
        $mysql = new Mysql([]);

        $result = $this->invokeTestMethod($mysql, 'parseCharset', [['charset' => '']]);

        $this->assertSame('', $result);
    }
}
