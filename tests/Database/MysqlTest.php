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

use Leevel\Database\Mysql;
use Tests\Database\DatabaseTestCase as TestCase;

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
    public function testGetTableNames()
    {
        $connect = $this->createDatabaseConnect();

        $result = $connect->tableNames('test');

        $this->assertTrue(in_array('guest_book', $result, true));
    }

    public function testGetTableColumns()
    {
        $connect = $this->createDatabaseConnect();

        $result = $connect->tableColumns('guest_book');

        $sql = <<<'eot'
            {
                "list": {
                    "id": {
                        "name": "id",
                        "type": "int",
                        "length": "int",
                        "primary_key": true,
                        "auto_increment": true,
                        "default": null,
                        "comment": ""
                    },
                    "name": {
                        "name": "name",
                        "type": "varchar",
                        "length": "varchar",
                        "primary_key": false,
                        "auto_increment": false,
                        "default": null,
                        "comment": ""
                    },
                    "content": {
                        "name": "content",
                        "type": "longtext",
                        "length": null,
                        "primary_key": false,
                        "auto_increment": false,
                        "default": null,
                        "comment": "评论内容"
                    },
                    "create_at": {
                        "name": "create_at",
                        "type": "timestamp",
                        "length": null,
                        "primary_key": false,
                        "auto_increment": false,
                        "default": "CURRENT_TIMESTAMP",
                        "comment": "创建时间"
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

    protected function getDatabaseTable(): array
    {
        return ['guest_book'];
    }
}
