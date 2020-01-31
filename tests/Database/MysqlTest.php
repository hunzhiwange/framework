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

use Leevel\Database\Mysql;
use Tests\Database\DatabaseTestCase as TestCase;

class MysqlTest extends TestCase
{
    public function testGetTableNames(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->tableNames('test');
        $this->assertTrue(in_array('guest_book', $result, true));
    }

    public function testGetTableColumns(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->tableColumns('guest_book');

        $sql = <<<'eot'
            {
                "list": {
                    "id": {
                        "field": "id",
                        "type": "int(11)",
                        "collation": null,
                        "null": false,
                        "key": "PRI",
                        "default": null,
                        "extra": "auto_increment",
                        "comment": "ID",
                        "primary_key": true,
                        "type_name": "int",
                        "type_length": "11",
                        "auto_increment": true
                    },
                    "name": {
                        "field": "name",
                        "type": "varchar(64)",
                        "collation": "utf8_general_ci",
                        "null": false,
                        "key": "",
                        "default": "",
                        "extra": "",
                        "comment": "名字",
                        "primary_key": false,
                        "type_name": "varchar",
                        "type_length": "64",
                        "auto_increment": false
                    },
                    "content": {
                        "field": "content",
                        "type": "longtext",
                        "collation": "utf8_general_ci",
                        "null": false,
                        "key": "",
                        "default": null,
                        "extra": "",
                        "comment": "评论内容",
                        "primary_key": false,
                        "type_name": "longtext",
                        "type_length": null,
                        "auto_increment": false
                    },
                    "create_at": {
                        "field": "create_at",
                        "type": "datetime",
                        "collation": null,
                        "null": false,
                        "key": "",
                        "default": "CURRENT_TIMESTAMP",
                        "extra": "",
                        "comment": "创建时间",
                        "primary_key": false,
                        "type_name": "datetime",
                        "type_length": null,
                        "auto_increment": false
                    }
                },
                "primary_key": [
                    "id"
                ],
                "auto_increment": "id",
                "table_collation": "utf8_general_ci",
                "table_comment": "留言板"
            }
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );
    }

    public function testGetTableColumnsButTableNotFound(): void
    {
        $connect = $this->createDatabaseConnect();
        $result = $connect->tableColumns('table_not_found');

        $sql = <<<'eot'
            {
                "list": [],
                "primary_key": null,
                "auto_increment": null,
                "table_collation": null,
                "table_comment": null
            }
            eot;

        $this->assertSame(
            $sql,
            $this->varJson(
                $result
            )
        );
    }

    public function testLimitCount(): void
    {
        $mysql = new Mysql([]);

        $this->assertSame('', $mysql->limitCount());
        $this->assertSame('LIMIT 5,999999999999', $mysql->limitCount(null, 5));
        $this->assertSame('LIMIT 5', $mysql->limitCount(5, null));
        $this->assertSame('LIMIT 5,5', $mysql->limitCount(5, 5));
    }

    public function testParsePort(): void
    {
        $mysql = new Mysql([]);

        $result = $this->invokeTestMethod($mysql, 'parsePort', [['port' => '']]);

        $this->assertSame('', $result);
    }

    public function testParseSocket(): void
    {
        $mysql = new Mysql([]);

        $result = $this->invokeTestMethod($mysql, 'parseSocket', [['socket' => '']]);

        $this->assertSame('', $result);
    }

    public function testParseSocket2(): void
    {
        $mysql = new Mysql([]);

        $result = $this->invokeTestMethod($mysql, 'parseSocket', [['socket' => '/var/lib/mysql/mysql.sock']]);

        $this->assertSame(';unix_socket=/var/lib/mysql/mysql.sock', $result);
    }

    public function testParseCharset(): void
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
