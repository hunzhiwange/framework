<?php

declare(strict_types=1);

namespace Tests\Database;

use Leevel\Database\Mysql;
use Leevel\Di\Container;
use Tests\Database\DatabaseTestCase as TestCase;

final class MysqlTest extends TestCase
{
    public function testLimitCount(): void
    {
        $mysql = new Mysql(new Container(), []);
        self::assertSame('', $mysql->limitCount());
        self::assertSame('LIMIT 5,999999999999', $mysql->limitCount(null, 5));
        self::assertSame('LIMIT 5', $mysql->limitCount(5, null));
        self::assertSame('LIMIT 5,5', $mysql->limitCount(5, 5));
    }

    public function testParsePort(): void
    {
        $mysql = new Mysql(new Container(), []);
        $result = $this->invokeTestMethod($mysql, 'parsePort', [['port' => '']]);
        self::assertSame('', $result);
    }

    public function testParseSocket(): void
    {
        $mysql = new Mysql(new Container(), []);
        $result = $this->invokeTestMethod($mysql, 'parseSocket', [['socket' => '']]);
        self::assertSame('', $result);
    }

    public function testParseSocket2(): void
    {
        $mysql = new Mysql(new Container(), []);
        $result = $this->invokeTestMethod($mysql, 'parseSocket', [['socket' => '/var/lib/mysql/mysql.sock']]);
        self::assertSame(';unix_socket=/var/lib/mysql/mysql.sock', $result);
    }

    public function testParseCharset(): void
    {
        $mysql = new Mysql(new Container(), []);
        $result = $this->invokeTestMethod($mysql, 'parseCharset', [['charset' => '']]);
        self::assertSame('', $result);
    }
}
