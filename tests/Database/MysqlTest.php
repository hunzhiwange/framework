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
}
